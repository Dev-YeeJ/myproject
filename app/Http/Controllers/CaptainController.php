<?php
// app/Http/Controllers/CaptainController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Medicine;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Template;
use App\Models\DocumentRequirement; // <-- Added this
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage; // <-- Added this

class CaptainController extends Controller
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

        return view('dashboard.captain-resident-profiling', compact(
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

        return view('dashboard.captain-resident-add', compact(
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

        return redirect()->route('captain.resident-profiling', ['view' => 'residents'])
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

        return view('dashboard.captain-resident-view', compact(
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
        return view('dashboard.captain-resident-edit', compact('user', 'resident', 'households'));
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
        return redirect()->route('captain.resident.show', $resident->id)
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

        return redirect()->route('captain.resident-profiling', ['view' => $view])
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
        return view('dashboard.captain-household-create', compact('user', 'nextHouseholdNumber'));
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

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household added successfully!');
    }

    public function editHousehold($id)
    {
        $user = Auth::user();
        $household = Household::findOrFail($id);
        return view('dashboard.captain-household-edit', compact('user', 'household'));
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

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
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

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household and all associated residents removed successfully!');
    }
    
    public function showHousehold($id)
    {
        $user = Auth::user();
        $household = Household::with(['activeResidents', 'head'])->findOrFail($id);
        return view('dashboard.captain-household-view', compact('user', 'household'));
    }

    // ============================================
    // HEALTH & SOCIAL SERVICES
    // ============================================
    
    public function healthAndSocialServices(Request $request)
    {
        $user = Auth::user();
        $allMedicines = Medicine::all();
        $stats = [
            'total_medicines' => $allMedicines->count(),
            'low_stock_medicines' => $allMedicines->where('status', 'Low Stock')->count(),
            'expired_medicines' => $allMedicines->where('status', 'Expired')->count(),
            'pending_requests' => 0, 
        ];
        $medicines = Medicine::orderBy('item_name')->get();
        return view('dashboard.captain-health-services', compact('user', 'stats', 'medicines'));
    }

    /*
    // Medicine CRUD is disabled for Captain
    public function createMedicine() { ... }
    public function storeMedicine(Request $request) { ... }
    */
    
    // ============================================
    // DOCUMENT SERVICES 
    // ============================================
    
    /**
     * --- UPDATED ---
     * Display the main document services page (lists requests, types, or templates)
     */
    public function documentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'requests');

        $stats = [
            'total_types' => DocumentType::count(),
            'total_templates' => Template::count(),
            'paid_documents' => DocumentType::where('requires_payment', true)->count(),
            'active_types' => DocumentType::where('is_active', true)->count(),
            'pending_requests' => DocumentRequest::whereIn('status', ['Pending', 'Processing', 'Under Review'])->count(),
            'requests_today' => DocumentRequest::whereDate('created_at', Carbon::today())->count(),
        ];

        $documentRequests = null;
        $documentTypes = null;
        $templates = null;

        if ($view === 'requests') {
            // --- UPDATED --- Eager load all relationships
            $requestsQuery = DocumentRequest::with([
                'resident', 
                'documentType', 
                'requirements' // Eager load the uploaded requirements
            ])->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                 $search = $request->search;
                 $requestsQuery->where(function ($q) use ($search) {
                     $q->where('tracking_number', 'like', "%{$search}%")
                       ->orWhere('purpose', 'like', "%{$search}%")
                       ->orWhereHas('resident', function ($q_res) use ($search) {
                           $q_res->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                       });
                 });
            }
            if ($request->filled('status') && $request->status !== 'All') {
                $requestsQuery->where('status', $request->status);
            }
            $documentRequests = $requestsQuery->paginate(10, ['*'], 'page')->appends($request->except('page'));
        
        } elseif ($view === 'types') {
            $typesQuery = DocumentType::query();
            if ($request->filled('search_types')) {
                $typesQuery->where('name', 'like', '%' . $request->search_types . '%');
            }
            $documentTypes = $typesQuery->orderBy('name')->paginate(9, ['*'], 'page')->appends($request->except('page'));
        
        } else { // $view === 'templates'
            $templatesQuery = Template::with('documentType'); 
            if ($request->filled('search_templates')) {
                $templatesQuery->where('name', 'like', '%' . $request->search_templates . '%');
            }
            $templates = $templatesQuery->orderBy('name')->paginate(10, ['*'], 'page')->appends($request->except('page'));
        }

        return view('dashboard.captain-document-services', compact(
            'user', 'stats', 'view',
            'documentRequests', 'documentTypes', 'templates'
        ));
    }

    /**
     * --- NEW METHOD ---
     * Show the details of a single document request for processing.
     * You will need to create a new blade file for this:
     * resources/views/dashboard/captain-document-view.blade.php
     */
    public function showDocumentRequest($id)
    {
        $user = Auth::user();
        $documentRequest = DocumentRequest::with(['resident', 'documentType', 'requirements'])
                                          ->findOrFail($id);
        
        // This view should contain a form that submits to 'captain.document.update'
        return view('dashboard.captain-document-view', compact('user', 'documentRequest'));
    }

    /**
     * --- NEW METHOD ---
     * Update a document request's status, remarks, and upload the generated file.
     */
    public function updateDocumentRequest(Request $request, $id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Pending,Processing,Under Review,Ready for Pickup,Completed,Rejected,Cancelled',
            'remarks' => 'nullable|string|max:1000',
            'generated_file' => 'nullable|file|mimes:pdf,docx,doc|max:5120' // 5MB max
        ]);

        // Handle the generated file upload
        if ($request->hasFile('generated_file')) {
            // Delete the old file if it exists
            if ($documentRequest->generated_file_path && Storage::disk('public')->exists($documentRequest->generated_file_path)) {
                Storage::disk('public')->delete($documentRequest->generated_file_path);
            }
            
            // Store the new file
            $filePath = $request->file('generated_file')->store('generated_documents', 'public');
            $documentRequest->generated_file_path = $filePath;
        }

        // Update status and remarks
        $documentRequest->status = $validated['status'];
        $documentRequest->remarks = $validated['remarks'];
        $documentRequest->save();

        // You can add logic here to send a notification to the resident

        return redirect()->route('captain.document.show', $id)
                         ->with('success', 'Document request has been updated successfully.');
    }

    /**
     * --- NEW METHOD ---
     * Allow the captain to download a specific requirement file uploaded by the resident.
     */
    public function downloadRequirement($id)
    {
        $requirement = DocumentRequirement::findOrFail($id);

        // Check if file exists
        if (Storage::disk('public')->exists($requirement->file_path)) {
            
            // Return the download response, using the original file name
            return Storage::disk('public')->download($requirement->file_path, $requirement->file_name);
        
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    // You would also add your CRUD methods for DocumentType and Template here
    // e.g. public function createDocumentType() { ... }
    // e.g. public function storeDocumentType() { ... }
    // e.g. public function editDocumentType($id) { ... }
    // e.g. public function updateDocumentType(Request $request, $id) { ... }
    // e.g. public function destroyDocumentType($id) { ... }
    // ... and so on for Templates ...
}