<?php

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
use Illuminate\Support\Facades\Log; // Added for any future logging

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
            // Build query for residents
            $query = Resident::with('household')->where('is_active', true);

            // Apply search filter for residents
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            }

            // Apply household status filter (Resident's role in household)
            if ($status && $status !== 'All Status') {
                $query->where('household_status', $status);
            }

            // Apply gender filter
            if ($gender && $gender !== 'All') {
                $query->where('gender', $gender);
            }

            $residents = $query->orderBy('last_name')->paginate(10);
        } else {
            // Build query for households
            $query = Household::with(['head', 'residents' => function ($q) {
                $q->where('is_active', true); // Eager load only active residents for display if needed
            }]);

            // Apply search filter for households
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_name', 'like', "%{$search}%")
                        ->orWhere('household_number', 'like', "%{$search}%") // Search by number
                        ->orWhereHas('head', function ($q_head) use ($search) {
                            $q_head->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
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

        $seniorCitizens = Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60')
            ->where('is_active', true)
            ->count();

        $minors = Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
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

        // Order households by number for the dropdown
        $households = Household::orderBy('household_number')->get();

        // Check if a household_id is passed in the URL to pre-select
        $selectedHousehold = $request->input('household_id', null);

        return view('dashboard.captain-resident-add', compact(
            'user',
            'households',
            'selectedHousehold' // Pass this to the view
        ));
    }


    /**
     * Store new resident
     */
    public function storeResident(Request $request)
    {
        // Add '0' default for boolean fields if not present in request
        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

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
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
        ]);

        // Calculate age
        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;

        // Determine if senior citizen
        $validated['is_senior_citizen'] = $age >= 60;

        // Set is_active to true for new residents
        $validated['is_active'] = true;

        // Create resident
        $resident = Resident::create($validated);

        // Update household member count if household_id is provided
        if ($resident->household_id) {
            $household = Household::find($resident->household_id);
            if ($household) {
                // Recalculate based on active residents
                $household->total_members = Resident::where('household_id', $household->id)
                    ->where('is_active', true)
                    ->count();
                $household->save();
            }
        }

        // Redirect back to the view they were on (residents or households)
        $view = 'residents';

        return redirect()->route('captain.resident-profiling', ['view' => $view])
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
        // Sort by household_number
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

        // Add '0' default for boolean fields if not present in request
        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

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
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
        ]);

        // Calculate age
        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;

        // Update resident
        $resident->update($validated);
        $newHouseholdId = $resident->household_id;

        // Update household counts if household changed
        if ($oldHouseholdId && $oldHouseholdId != $newHouseholdId) {
            $oldHousehold = Household::find($oldHouseholdId);
            if ($oldHousehold) {
                $oldHousehold->total_members = Resident::where('household_id', $oldHousehold->id)
                    ->where('is_active', true)
                    ->count();
                $oldHousehold->save();
            }
        }

        if ($newHouseholdId) {
            if ($oldHouseholdId != $newHouseholdId) {
                $newHousehold = Household::find($newHouseholdId);
                if ($newHousehold) {
                    $newHousehold->total_members = Resident::where('household_id', $newHousehold->id)
                        ->where('is_active', true)
                        ->count();
                    $newHousehold->save();
                }
            } else {
                $household = Household::find($newHouseholdId);
                if ($household) {
                    $household->total_members = Resident::where('household_id', $household->id)
                        ->where('is_active', true)
                        ->count();
                    $household->save();
                }
            }
        }

        $view = 'residents';

        return redirect()->route('captain.resident-profiling', ['view' => $view])
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
                $household->total_members = Resident::where('household_id', $household->id)
                    ->where('is_active', true)
                    ->count();
                $household->save();
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
        
        // --- ADD THIS LINE ---
        // Call the static function from your Household model to get the next number
        $nextHouseholdNumber = \App\Models\Household::generateHouseholdNumber();

        // --- AND PASS THE VARIABLE HERE ---
        return view('dashboard.captain-household-create', compact('user', 'nextHouseholdNumber'));
    }

    /**
     * Store new household
     */
    public function storeHousehold(Request $request)
    {
        // ============================================
        // MODIFICATION 1: Removed 'household_number' from validation
        // ============================================
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            // 'household_number' => 'required|string|max:50|unique:households,household_number', // <-- REMOVED
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'status' => 'required|in:complete,incomplete',
        ]);

        Household::create($validated + ['total_members' => 0]);

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
        // MODIFICATION 2: Removed 'household_number' from validation
        // ============================================
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            // 'household_number' => 'required|string|max:50|unique:households,household_number,' . $id, // <-- REMOVED
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'status' => 'required|in:complete,incomplete',
        ]);

        $validated['total_members'] = Resident::where('household_id', $id)->where('is_active', true)->count();

        $household->update($validated);

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household updated successfully!');
    }

    /**
     * Delete household (hard delete) and soft delete its residents
     */
    public function destroyHousehold($id)
    {
        $household = Household::findOrFail($id);

        Resident::where('household_id', $id)->where('is_active', true)->update(['is_active' => false]);
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
        
        // Eager load the household with its active residents and the head
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

    /**
     * Show form to add new medicine
     */
    public function createMedicine()
    {
        $user = Auth::user();
        return view('dashboard.captain-medicine-create', compact('user'));
    }

    /**
     * Store new medicine
     */
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

    // ============================================
    // DOCUMENT SERVICES (*** UPDATED FUNCTION ***)
    // ============================================

    /**
     * Display the document services page
     */
    public function documentServices(Request $request)
    {
        $user = Auth::user();
        // 1. SET DEFAULT VIEW TO 'REQUESTS'
        $view = $request->input('view', 'requests');

        // --- Stats Grid ---
        $stats = [
            'total_types' => DocumentType::count(),
            'total_templates' => Template::count(),
            'paid_documents' => DocumentType::where('requires_payment', true)->count(),
            'active_types' => DocumentType::where('is_active', true)->count(),
            // 2. ADDED PENDING REQUESTS STAT
            'pending_requests' => DocumentRequest::whereIn('status', ['Pending', 'Processing', 'Under Review'])->count(),
            'requests_today' => DocumentRequest::whereDate('created_at', Carbon::today())->count(),
        ];

        // --- Initialize all paginated variables ---
        $documentRequests = null;
        $documentTypes = null;
        $templates = null;

        // --- Fetch data based on the current view ---
        // We use if/elseif/else to only fetch and paginate the data we need.
        // This is more efficient and prevents pagination conflicts.

        if ($view === 'requests') {
            // 3. FETCH DOCUMENT REQUESTS
            $requestsQuery = DocumentRequest::with(['resident', 'documentType'])
                ->orderBy('created_at', 'desc');

            // Apply search filter
            if ($request->filled('search')) {
                 $search = $request->search;
                 $requestsQuery->where(function ($q) use ($search) {
                     $q->where('tracking_number', 'like', "%{$search}%")
                         ->orWhere('purpose', 'like', "%{$search}%")
                         ->orWhereHas('resident', function ($q_res) use ($search) {
                             $q_res->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%");
                         });
                 });
            }

            // Apply status filter
            if ($request->filled('status') && $request->status !== 'All') {
                $requestsQuery->where('status', $request->status);
            }

            $documentRequests = $requestsQuery->paginate(10, ['*'], 'page');

        } elseif ($view === 'types') {
            // --- Toggle View: Document Types ---
            $typesQuery = DocumentType::query();
            if ($request->filled('search_types')) {
                $typesQuery->where('name', 'like', '%' . $request->search_types . '%');
            }
            // Use 'page' as the pagination parameter name for consistency
            $documentTypes = $typesQuery->orderBy('name')->paginate(10, ['*'], 'page');

        } else { // $view === 'templates'
            // --- Toggle View: Templates ---
            $templatesQuery = Template::with('documentType'); // Eager load relation
            if ($request->filled('search_templates')) {
                $templatesQuery->where('name', 'like', '%' . $request->search_templates . '%');
            }
            // Use 'page' as the pagination parameter name
            $templates = $templatesQuery->orderBy('name')->paginate(10, ['*'], 'page');
        }

        return view('dashboard.captain-document-services', compact(
            'user',
            'stats',
            'view',
            'documentRequests', // <-- 4. PASS NEW DATA
            'documentTypes',
            'templates'
        ));
    }
}