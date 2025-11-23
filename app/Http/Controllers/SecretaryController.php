<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <-- IMPORTED FOR UNIQUE EMAIL RULE

class SecretaryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the resident profiling page with search and filters
     */
    public function residentProfiling(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'residents');
        $search = $request->input('search');
        $status = $request->input('status'); // Used for Resident household_status or Household status
        $gender = $request->input('gender'); // Used for Resident gender
        $filter = $request->input('filter'); // <-- NEW: For quick filters

        $residents = null;
        $households = null;

        if ($view === 'residents') {
            // Build query for residents using the Model Scope
            // --- FIX: Eager load the 'user' relationship ---
            $query = Resident::with(['household', 'user']) 
                ->where('is_active', true)
                ->search($search) // Use the search scope from the Resident model
                ->byHouseholdStatus($status) // Use the status scope
                ->byGender($gender); // Use the gender scope

            // --- NEW: Apply quick filters ---
            if ($filter) {
                switch ($filter) {
                    case 'seniors':
                        $query->where('is_senior_citizen', true);
                        break;
                    case 'pwd':
                        $query->where('is_pwd', true);
                        break;
                    case '4ps':
                        $query->where('is_4ps', true);
                        break;
                    case 'voters':
                        $query->where('is_registered_voter', true);
                        break;
                }
            }
            // --- END NEW ---

            $residents = $query->orderBy('last_name')->paginate(10);

        } else {
            // Build query for households
            $query = Household::with(['head', 'activeResidents']);

            // Apply search filter for households
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_name', 'like', "%{$search}%")
                        ->orWhere('household_number', 'like', "%{$search}%") 
                        ->orWhereHas('head', function ($q_head) use ($search) {
                            $q_head->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                        });
                });
            }

            // Apply household status filter (Household's own status: complete/incomplete)
            if ($status && $status !== 'All Status' && in_array($status, ['complete', 'incomplete'])) {
                $query->where('status', $status);
            }
            // --- END NEW ---

            // Sort by household_number
            $households = $query->orderBy('household_number')->paginate(5);
        }

        // Get overall statistics
        $totalResidents = Resident::where('is_active', true)->count();
        $totalHouseholds = Household::count();
        $completeHouseholds = Household::where('status', 'complete')->count();

        $seniorCitizens = Resident::where('is_senior_citizen', true)
            ->where('is_active', true)
            ->count();

        $minors = Resident::where('age', '<', 18)
            ->where('is_active', true)
            ->count();
        
        // --- NEW STATS ---
        $totalPwd = Resident::where('is_active', true)->where('is_pwd', true)->count();
        $total4ps = Resident::where('is_active', true)->where('is_4ps', true)->count();
        $totalVoters = Resident::where('is_active', true)->where('is_registered_voter', true)->count();
        $incompleteHouseholds = $totalHouseholds - $completeHouseholds;
        // --- END NEW STATS ---

        return view('dashboard.secretary-resident-profiling', compact(
            'user',
            'view',
            'residents',
            'households',
            'totalResidents',
            'totalHouseholds',
            'completeHouseholds',
            'seniorCitizens',
            'minors',
            'filter', // <-- PASS NEW FILTER
            // --- PASS NEW STATS ---
            'totalPwd',
            'total4ps',
            'totalVoters',
            'incompleteHouseholds'
            // --- END NEW STATS ---
        ));
    } // <--- THIS WAS THE MISSING BRACE

    /**
     * Show form to add new resident
     */
    public function createResident(Request $request)
    {
        $user = Auth::user();
        $households = Household::orderBy('household_number')->get();
        $selectedHousehold = $request->input('household_id', null);

        return view('dashboard.secretary-resident-add', compact(
            'user',
            'households',
            'selectedHousehold' 
        ));
    }


    /**
     * Store new resident
     */
    public function storeResident(Request $request)
    {
        // Add '0' default for boolean fields if not present
        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

        if ($request->input('occupation') === 'Student') {
            $request->merge(['monthly_income' => null]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'household_id' => 'nullable|exists:households,id',
            'household_status' => 'required|in:Household Head,Spouse,Child,Member',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            
            // Check for email uniqueness on BOTH tables
            'email' => 'nullable|email|max:255|unique:residents,email|unique:users,email',

            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
            'precinct_number' => 'nullable|required_if:is_registered_voter,true|string|max:100',
            'pwd_id_number' => 'nullable|required_if:is_pwd,true|string|max:100',
            'disability_type' => 'nullable|required_if:is_pwd,true|string|max:255',
        ]);

        // Calculate age
        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;

        // Set is_active to true
        $validated['is_active'] = true;
        
        if (!$validated['is_registered_voter']) $validated['precinct_number'] = null;
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }
        
        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        // Create resident
        // The ResidentObserver will automatically create a User
        $resident = Resident::create($validated); 

        // Update household member count and status
        if ($resident->household_id) {
            $household = Household::find($resident->household_id);
            if ($household) {
                $household->updateTotalMembers();
                $household->updateHouseholdStatus();
            }
        }

        return redirect()->route('secretary.resident-profiling', ['view' => 'residents'])
            ->with('success', 'Resident added successfully!');
    }

    /**
     * Show resident details
     */
    public function showResident($id)
    {
        $user = Auth::user();
        $resident = Resident::with(['household', 'user'])->findOrFail($id);

        // --- Calculate the default password to display it ---
        $defaultPassword = null;
        if ($resident->date_of_birth) {
            // e.g., "angeles"
            $lastName = Str::slug($resident->last_name, '');
            // e.g., "19900115"
            $birthdate = Carbon::parse($resident->date_of_birth)->format('Ymd');
            $defaultPassword = $lastName . $birthdate;
        }
        // --- END ---

        return view('dashboard.secretary-resident-view', compact(
            'user', 
            'resident', 
            'defaultPassword' // <-- Pass the password to the view
        ));
    }

    /**
     * Show edit form for resident
     */
    public function editResident($id)
    {
        $user = Auth::user();
        $resident = Resident::findOrFail($id);
        $households = Household::orderBy('household_number')->get();
        return view('dashboard.secretary-resident-edit', compact('user', 'resident', 'households'));
    }

    /**
     * Update resident
     */
    public function updateResident(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);
        $oldHouseholdId = $resident->household_id;

        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

        if ($request->input('occupation') === 'Student') {
            $request->merge(['monthly_income' => null]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'household_id' => 'nullable|exists:households,id',
            'household_status' => 'required|in:Household Head,Spouse,Child,Member',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => [
                'nullable', 'email', 'max:255', 
                Rule::unique('residents')->ignore($id),
                Rule::unique('users')->ignore($resident->user_id)
            ],
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
            'precinct_number' => 'nullable|required_if:is_registered_voter,true|string|max:100',
            'pwd_id_number' => 'nullable|required_if:is_pwd,true|string|max:100',
            'disability_type' => 'nullable|required_if:is_pwd,true|string|max:255',
        ]);

        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;
        
        if (!$validated['is_registered_voter']) $validated['precinct_number'] = null;
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }

        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('id', '!=', $id) 
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        // The ResidentObserver will automatically update the User
        $resident->update($validated);
        $newHouseholdId = $resident->household_id; 

        if ($newHouseholdId) {
            $newHousehold = Household::find($newHouseholdId);
            if ($newHousehold) {
                $newHousehold->updateTotalMembers();
                $newHousehold->updateHouseholdStatus();
            }
        }

        if ($oldHouseholdId && $oldHouseholdId != $newHouseholdId) {
            $oldHousehold = Household::find($oldHouseholdId);
            if ($oldHousehold) {
                $oldHousehold->updateTotalMembers();
                $oldHousehold->updateHouseholdStatus();
            }
        }

        // Redirect back to the resident's view page
        return redirect()->route('secretary.resident.show', $resident->id)
            ->with('success', 'Resident updated successfully!');
    }

    /**
     * Delete resident (soft delete by setting is_active to false)
     */
    public function destroyResident($id)
    {
        $resident = Resident::findOrFail($id);
        $householdId = $resident->household_id;

        // Soft delete
        $resident->is_active = false;
        $resident->save(); 
        // The ResidentObserver's 'updated' method will catch this
        // and set the user->is_active to false.

        if ($householdId) {
            $household = Household::find($householdId);
            if ($household) {
                $household->updateTotalMembers();
                $household->updateHouseholdStatus();
            }
        }

        $view = request('view', 'residents');

        return redirect()->route('secretary.resident-profiling', ['view' => $view])
            ->with('success', 'Resident removed successfully!');
    }

    /**
     * Reset a resident's user password to the default.
     */
    public function resetPassword(Resident $resident)
    {
        if ($resident->user) {
            // Generate the default password: lastnameYYYYMMDD
            $lastName = Str::slug($resident->last_name, '');
            $birthdate = Carbon::parse($resident->date_of_birth)->format('Ymd');
            $defaultPassword = $lastName . $birthdate;

            $resident->user->password = Hash::make($defaultPassword);
            $resident->user->save();
            
            return redirect()->back()->with('success', "Password for {$resident->user->username} has been reset to '{$defaultPassword}'.");
        }
        return redirect()->back()->with('error', 'This resident does not have a linked user account.');
    }

    // ============================================
    // HOUSEHOLD MANAGEMENT
    // ============================================

    public function createHousehold()
    {
        $user = Auth::user();
        $nextHouseholdNumber = \App\Models\Household::generateHouseholdNumber();
        return view('dashboard.secretary-household-create', compact('user', 'nextHouseholdNumber'));
    }

    public function storeHousehold(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);
        
        Household::create($validated + [
            'total_members' => 0,
            'status' => 'incomplete'
        ]);

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household added successfully!');
    }

    public function editHousehold($id)
    {
        $user = Auth::user();
        $household = Household::findOrFail($id);
        return view('dashboard.secretary-household-edit', compact('user', 'household'));
    }

    public function updateHousehold(Request $request, $id)
    {
        $household = Household::findOrFail($id);

        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);

        $validated['total_members'] = Resident::where('household_id', $id)->where('is_active', true)->count();
        
        $household->update($validated);
        
        $household->updateHouseholdStatus();

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household updated successfully!');
    }

    public function destroyHousehold($id)
    {
        $household = Household::findOrFail($id);

        // Soft delete all active residents associated with this household
        // The ResidentObserver will catch each 'update' and deactivate the user.
        Resident::where('household_id', $id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
            
        // Hard delete the household
        $household->delete();

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household and all associated residents removed successfully!');
    }
    
    public function showHousehold($id)
    {
        $user = Auth::user();
        $household = Household::with(['activeResidents', 'head'])->findOrFail($id);
        return view('dashboard.secretary-household-view', compact('user', 'household'));
    }
    // ============================================
    // DOCUMENT SERVICES (Settings)
    // (No changes requested - Left as is)
    // ============================================

    public function documentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'types');

        $stats = [
            'total_types' => DocumentType::count(),
            'total_templates' => Template::count(),
            'paid_documents' => DocumentType::where('requires_payment', true)->count(),
            'active_types' => DocumentType::where('is_active', true)->count(),
            'requests_today' => DocumentRequest::whereDate('created_at', Carbon::today())->count(),
        ];

        $typesQuery = DocumentType::query();
        if ($request->filled('search_types')) {
            $typesQuery->where('name', 'like', '%' . $request->search_types . '%');
        }
        $documentTypes = $typesQuery->orderBy('name')->paginate(10, ['*'], 'typesPage');

        $templatesQuery = Template::with('documentType');
        if ($request->filled('search_templates')) {
            $templatesQuery->where('name', 'like', '%' . $request->search_templates . '%');
        }
        $templates = $templatesQuery->orderBy('name')->paginate(10, ['*'], 'templatesPage');
        
        return view('dashboard.secretary-document-services', compact(
            'user',
            'stats',
            'view',
            'documentTypes',
            'templates'
        ));
    }


    // ============================================
    // DOCUMENT REQUESTS (Processing)
    // (No changes requested - Left as is)
    // ============================================

    public function documentRequests(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'Pending');
        $search = $request->input('search');

        $query = DocumentRequest::with(['resident', 'documentType'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('resident', function($rq) use ($search) {
                    $rq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                })
                ->orWhereHas('documentType', function($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('tracking_number', 'like', "%{$search}%"); // Use tracking_number, not control_number
            });
        }

        $requests = $query->paginate(15);

        return view('dashboard.secretary-document-requests', compact(
            'user',
            'requests',
            'status',
            'search'
        ));
    }

    public function showDocumentRequest($id)
    {
        $user = Auth::user();
        $request = DocumentRequest::with(['resident', 'documentType.template'])->findOrFail($id);
        
        // ============================================
        // MODIFIED: Corrected typo in variable name
        // ============================================
        $docRequest = $request; 

        return view('dashboard.secretary-document-process', compact('user', 'docRequest'));
    }

    public function updateDocumentRequest(Request $request, $id)
    {
        $docRequest = DocumentRequest::findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve') {
            $docRequest->status = 'Approved';
            $docRequest->processed_by_id = Auth::id();
            $docRequest->save();
            return redirect()->route('secretary.document-requests')->with('success', 'Document approved successfully.');
        } elseif ($action === 'deny') {
            $docRequest->status = 'Denied';
            $docRequest->remarks = $request->input('remarks');
            $docRequest->processed_by_id = Auth::id();
            $docRequest->save();
            return redirect()->route('secretary.document-requests')->with('success', 'Document denied.');
        }

        return redirect()->route('secretary.document-requests.show', $id)->with('error', 'Invalid action.');
    }
}