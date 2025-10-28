<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Medicine; // Import the Medicine model
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade

class CaptainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // NOTE: The dashboard display logic is handled by DashboardController@captain
    // public function dashboard() { ... } // Removed redundant method

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

            // --- FIX: Sort by household_number instead of non-existent household_name ---
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

        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
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

        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
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
        // Retrieve the intended view from a hidden input if needed, otherwise default
        // For simplicity, let's default to residents view after adding a resident
        $view = 'residents'; // Or $request->input('redirect_view', 'residents');

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
        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
        return view('dashboard.captain-resident-view', compact('user', 'resident'));
    }

    /**
     * Show edit form for resident
     */
    public function editResident($id)
    {
        $user = Auth::user();
        $resident = Resident::findOrFail($id);
        // --- FIX: Sort by household_number ---
        $households = Household::orderBy('household_number')->get();
        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
        return view('dashboard.captain-resident-edit', compact('user', 'resident', 'households'));
    }



    /**
     * Update resident
     */
    public function updateResident(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);
        $oldHouseholdId = $resident->household_id; // Get old household ID before update

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
        $newHouseholdId = $resident->household_id; // Get new household ID after update

        // Update household counts if household changed
        // Update old household count
        if ($oldHouseholdId && $oldHouseholdId != $newHouseholdId) {
            $oldHousehold = Household::find($oldHouseholdId);
            if ($oldHousehold) {
                $oldHousehold->total_members = Resident::where('household_id', $oldHousehold->id)
                    ->where('is_active', true)
                    ->count();
                $oldHousehold->save();
            }
        }

        // Update new household count
        if ($newHouseholdId) {
            // Avoid double update if household didn't change
            if ($oldHouseholdId != $newHouseholdId) {
                $newHousehold = Household::find($newHouseholdId);
                if ($newHousehold) {
                    $newHousehold->total_members = Resident::where('household_id', $newHousehold->id)
                        ->where('is_active', true)
                        ->count();
                    $newHousehold->save();
                }
            } else {
                // If household is the same, still might need update if resident status changed (though unlikely needed here)
                // For safety, we can update it anyway if it's the same ID.
                $household = Household::find($newHouseholdId);
                if ($household) {
                    $household->total_members = Resident::where('household_id', $household->id)
                        ->where('is_active', true)
                        ->count();
                    $household->save();
                }
            }
        }

        // Redirect back to the view they were on
        // Retrieve the intended view from a hidden input if needed, otherwise default
        $view = 'residents'; // Or $request->input('redirect_view', 'residents');


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

        // Update household member count if resident belonged to one
        if ($householdId) {
            $household = Household::find($householdId);
            if ($household) {
                $household->total_members = Resident::where('household_id', $household->id)
                    ->where('is_active', true)
                    ->count();
                $household->save();
            }
        }

        // Determine which view to redirect back to (get from request if available)
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
        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
        return view('dashboard.captain-household-create', compact('user'));
    }

    /**
     * Store new household
     */
    public function storeHousehold(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'household_number' => 'required|string|max:50|unique:households,household_number',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'status' => 'required|in:complete,incomplete',
        ]);

        // Create household with 0 initial members
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
        // --- FIX: Changed 'dashboards.' to 'dashboard.' ---
        return view('dashboard.captain-household-edit', compact('user', 'household'));
    }

    /**
     * Update household
     */
    public function updateHousehold(Request $request, $id)
    {
        $household = Household::findOrFail($id);

        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'household_number' => 'required|string|max:50|unique:households,household_number,' . $id, // Ignore current household ID for unique check
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'status' => 'required|in:complete,incomplete',
        ]);

        // Recalculate total members (count active residents associated with this household)
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

        // Soft delete all active residents associated with this household first
        Resident::where('household_id', $id)->where('is_active', true)->update(['is_active' => false]);

        // Now, hard delete the household record itself
        $household->delete();

        return redirect()->route('captain.resident-profiling', ['view' => 'households'])
                         ->with('success', 'Household and all associated residents removed successfully!');
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

        // --- Query Real Data ---
        // Get all medicines, and we will use the Accessor for status
        $allMedicines = Medicine::all();

        $stats = [
            'total_medicines' => $allMedicines->count(),
            // Use collection 'where' to filter by the 'status' Accessor
            'low_stock_medicines' => $allMedicines->where('status', 'Low Stock')->count(),
            'expired_medicines' => $allMedicines->where('status', 'Expired')->count(),
            'pending_requests' => 0, // Placeholder
        ];

        // --- Get Medicine Data for Table ---
        $medicines = Medicine::orderBy('item_name')->get();

        // The 'status' attribute is now automatically handled by the Accessor in the Medicine model

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
        // Validates based on your migration file
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'expiration_date' => 'required|date',
        ]);
        
        // We do NOT save 'status', it will be calculated by the Model Accessor
        // to prevent stale data.

        Medicine::create($validated);

        return redirect()->route('captain.health-services')
                         ->with('success', 'Medicine added to inventory successfully!');
    }
}