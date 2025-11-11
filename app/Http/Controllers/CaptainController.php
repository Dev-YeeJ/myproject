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
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
// Import the validation rule
use Illuminate\Validation\Rule; 

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

        $residents = null;
        $households = null;

        if ($view === 'residents') {
            // Build query for residents using the Model Scope
            $query = Resident::with('household')
                ->where('is_active', true)
                ->search($search) // Use the search scope from the Resident model
                ->byHouseholdStatus($status) // Use the status scope
                ->byGender($gender); // Use the gender scope

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

        return view('dashboard.captain-resident-profiling', compact(
            'user',
            'view',
            'residents',
            'households',
            'totalResidents',
            'totalHouseholds',
            'completeHouseholds',
            'seniorCitizens',
            'minors'
        ));
    }


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

        // ============================================
        // LOGIC: Handle 'Student' occupation
        // If occupation is 'Student', force monthly_income to be null (or 0)
        // ============================================
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
            'email' => 'nullable|email|max:255|unique:residents,email',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',

            // ============================================
            // LOGIC: Conditional validation for new fields
            // ============================================
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
        
        // ============================================
        // LOGIC: Clean up conditional data
        // If checkbox is unchecked, nullify the corresponding text field
        // ============================================
        if (!$validated['is_registered_voter']) {
            $validated['precinct_number'] = null;
        }
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }
        
        // ============================================
        // LOGIC: Handle Household Head Uniqueness
        // If new resident is a Head, demote any existing active Head in that household
        // ============================================
        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        // Create resident
        $resident = Resident::create($validated);

        // Update household member count and status
        if ($resident->household_id) {
            $household = Household::find($resident->household_id);
            if ($household) {
                // Use the methods from the Household model
                $household->updateTotalMembers();
                $household->updateHouseholdStatus(); // <-- ADDED LOGIC
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
        $resident = Resident::with('household')->findOrFail($id);
        return view('dashboard.captain-resident-view', compact('user', 'resident'));
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

        // Add '0' default for boolean fields
        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

        // LOGIC: Handle 'Student' occupation
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
            // Ensure email is unique, but ignore the current resident's email
            'email' => ['nullable', 'email', 'max:255', Rule::unique('residents')->ignore($id)],
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',

            // LOGIC: Conditional validation
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
        
        // LOGIC: Clean up conditional data
        if (!$validated['is_registered_voter']) {
            $validated['precinct_number'] = null;
        }
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }

        // ============================================
        // LOGIC: Handle Household Head Uniqueness
        // If resident is a Head, demote any other active Head in that household
        // ============================================
        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('id', '!=', $id) // Exclude the current resident
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        // Update resident
        $resident->update($validated);
        $newHouseholdId = $resident->household_id; 

        // ============================================
        // LOGIC: Update Household Counts & Status
        // ============================================
        
        // Find the new household (if any) and update it
        if ($newHouseholdId) {
            $newHousehold = Household::find($newHouseholdId);
            if ($newHousehold) {
                $newHousehold->updateTotalMembers();
                $newHousehold->updateHouseholdStatus();
            }
        }

        // If the resident moved households, update the old one too
        if ($oldHouseholdId && $oldHouseholdId != $newHouseholdId) {
            $oldHousehold = Household::find($oldHouseholdId);
            if ($oldHousehold) {
                $oldHousehold->updateTotalMembers();
                $oldHousehold->updateHouseholdStatus();
            }
        }

        return redirect()->route('captain.resident-profiling', ['view' => 'residents'])
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

        if ($householdId) {
            $household = Household::find($householdId);
            if ($household) {
                $household->updateTotalMembers();
                $household->updateHouseholdStatus(); // <-- ADDED LOGIC
            }
        }

        $view = request('view', 'residents');

        return redirect()->route('captain.resident-profiling', ['view' => $view])
            ->with('success', 'Resident removed successfully!');
    }

    // ============================================
    // HOUSEHOLD MANAGEMENT
    // ============================================

    /**
     * Show form to add new household
     */
    public function createHousehold()
    {
        $user = Auth::user();
        $nextHouseholdNumber = \App\Models\Household::generateHouseholdNumber();
        return view('dashboard.captain-household-create', compact('user', 'nextHouseholdNumber'));
    }

    /**
     * Store new household
     */
    public function storeHousehold(Request $request)
    {
        // ============================================
        // LOGIC: Removed 'status' from validation.
        // New households are always 'incomplete' by default.
        // ============================================
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);
        
        // Create household, forcing 'incomplete' status
        Household::create($validated + [
            'total_members' => 0,
            'status' => 'incomplete' // <-- ADDED LOGIC
        ]);

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household added successfully!');
    }

    /**
     * Show edit form for household
     */
    public function editHousehold($id)
    {
        $user = Auth::user();
        $household = Household::findOrFail($id);
        return view('dashboard.captain-household-edit', compact('user', 'household'));
    }

    /**
     * Update household
     */
    public function updateHousehold(Request $request, $id)
    {
        $household = Household::findOrFail($id);

        // ============================================
        // LOGIC: Removed 'status' from validation.
        // Status is now only updated automatically via Resident logic.
        // ============================================
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);

        // Recalculate total members just in case
        $validated['total_members'] = Resident::where('household_id', $id)->where('is_active', true)->count();
        
        // Note: We do NOT update $validated['status'] here.
        // The status is only changed by adding/removing/updating a Household Head.
        
        $household->update($validated);
        
        // We can optionally re-run the status check
        $household->updateHouseholdStatus();

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household updated successfully!');
    }

    /**
     * Delete household (hard delete) and soft delete its residents
     */
    public function destroyHousehold($id)
    {
        $household = Household::findOrFail($id);

        // Soft delete all active residents associated with this household
        Resident::where('household_id', $id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
            
        // Hard delete the household
        $household->delete();

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household and all associated residents removed successfully!');
    }
    
    /**
     * Show household details and members
     */
    public function showHousehold($id)
    {
        $user = Auth::user();
        $household = Household::with(['activeResidents', 'head'])->findOrFail($id);
        return view('dashboard.captain-household-view', compact('user', 'household'));
    }

    // ============================================
    // HEALTH & SOCIAL SERVICES
    // ============================================
    
    /**
     * Display the health and social services page
     */
    public function healthAndSocialServices(Request $request)
    {
        $user = Auth::user();
        $allMedicines = Medicine::all();

        $stats = [
            'total_medicines' => $allMedicines->count(),
            'low_stock_medicines' => $allMedicines->where('status', 'Low Stock')->count(),
            'expired_medicines' => $allMedicines->where('status', 'Expired')->count(),
            'pending_requests' => 0, // Placeholder
        ];

        $medicines = Medicine::orderBy('item_name')->get();

        return view('dashboard.captain-health-services', compact(
            'user',
            'stats',
            'medicines'
        ));
    }


    // ============================================
    // MEDICINE (HEALTH SERVICES) CRUD
    // ============================================

    /*
    // ============================================
    // CREATE MEDICINE - DISABLED FOR CAPTAIN
    // ============================================
    public function createMedicine()
    {
        $user = Auth::user();
        return view('dashboard.captain-medicine-create', compact('user'));
    }
    */

    /*
    // ============================================
    // STORE MEDICINE - DISABLED FOR CAPTAIN
    // ============================================
    public function storeMedicine(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'expiration_date' => 'required|date',
        ]);

        Medicine::create($validated);

        return redirect()->route('captain.health-services')
            ->with('success', 'Medicine added to inventory successfully!');
    }
    */
    
    // ============================================
    // DOCUMENT SERVICES 
    // ============================================
    
    // (This section is unchanged)

    /**
     * Display the document services page
     */
    public function documentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'requests');

        // --- Stats Grid ---
        $stats = [
            'total_types' => DocumentType::count(),
            'total_templates' => Template::count(),
            'paid_documents' => DocumentType::where('requires_payment', true)->count(),
            'active_types' => DocumentType::where('is_active', true)->count(),
            'pending_requests' => DocumentRequest::whereIn('status', ['Pending', 'Processing', 'Under Review'])->count(),
            'requests_today' => DocumentRequest::whereDate('created_at', Carbon::today())->count(),
        ];

        // --- Initialize all paginated variables ---
        $documentRequests = null;
        $documentTypes = null;
        $templates = null;

        if ($view === 'requests') {
            $requestsQuery = DocumentRequest::with(['resident', 'documentType'])
                ->orderBy('created_at', 'desc');

            // Apply search filter
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

            // Apply status filter
            if ($request->filled('status') && $request->status !== 'All') {
                $requestsQuery->where('status', $request->status);
            }

            $documentRequests = $requestsQuery->paginate(10, ['*'], 'page');

        } elseif ($view === 'types') {
            $typesQuery = DocumentType::query();
            if ($request->filled('search_types')) {
                $typesQuery->where('name', 'like', '%' . $request->search_types . '%');
            }
            $documentTypes = $typesQuery->orderBy('name')->paginate(10, ['*'], 'page');

        } else { // $view === 'templates'
            $templatesQuery = Template::with('documentType'); 
            if ($request->filled('search_templates')) {
                $templatesQuery->where('name', 'like', '%' . $request->search_templates . '%');
            }
            $templates = $templatesQuery->orderBy('name')->paginate(10, ['*'], 'page');
        }

        return view('dashboard.captain-document-services', compact(
            'user',
            'stats',
            'view',
            'documentRequests', 
            'documentTypes',
            'templates'
        ));
    }
}