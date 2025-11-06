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
     * Get all the necessary stats for the profiling dashboard layout.
     */
    private function _getProfilingStats()
    {
        return [
            'totalResidents' => Resident::where('is_active', true)->count(),
            'totalHouseholds' => Household::count(),
            'completeHouseholds' => Household::where('status', 'complete')->count(),
            'seniorCitizens' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60')
                ->where('is_active', true)
                ->count(),
            'minors' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
                ->where('is_active', true)
                ->count(),
        ];
    }


    // ============================================
    // RESIDENT PROFILING
    // ============================================

    /**
     * Display the resident profiling page with search and filters
     */
    public function residentProfiling(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'residents');
        $search = $request->input('search');
        $status = $request->input('status');
        $gender = $request->input('gender');

        $residents = null;
        $households = null;

        if ($view === 'residents') {
            $query = Resident::with('household')->where('is_active', true);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%");
                });
            }
            if ($status && $status !== 'All Status') {
                $query->where('household_status', $status);
            }
            if ($gender && $gender !== 'All') {
                $query->where('gender', $gender);
            }
            $residents = $query->orderBy('last_name')->paginate(10);
        } else {
            $query = Household::with(['head', 'residents' => function ($q) {
                $q->where('is_active', true);
            }]);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_name', 'like', "%{$search}%")
                        ->orWhere('household_number', 'like', "%{$search}%")
                        ->orWhereHas('head', function ($q_head) use ($search) {
                            $q_head->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            }
            if ($status && $status !== 'All Status' && in_array($status, ['complete', 'incomplete'])) {
                $query->where('status', $status);
            }
            $households = $query->orderBy('household_number')->paginate(5);
        }

        $stats = $this->_getProfilingStats();

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-resident-profiling', array_merge(compact(
            'user',
            'view',
            'residents',
            'households'
        ), $stats));
    }


    /**
     * Show form to add new resident
     */
    public function createResident(Request $request)
    {
        $user = Auth::user();
        $households = Household::orderBy('household_number')->get();
        $selectedHousehold = $request->input('household_id', null);

        $stats = $this->_getProfilingStats();

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-resident-add', array_merge(compact(
            'user',
            'households',
            'selectedHousehold'
        ), $stats));
    }


    /**
     * Store new resident
     */
    public function storeResident(Request $request)
    {
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

        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;
        $validated['is_active'] = true;

        $resident = Resident::create($validated);

        if ($resident->household_id) {
            $household = Household::find($resident->household_id);
            if ($household) {
                $household->total_members = Resident::where('household_id', $household->id)
                    ->where('is_active', true)
                    ->count();
                $household->save();
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
        $resident = Resident::with('household')->findOrFail($id);
        $stats = $this->_getProfilingStats();
        
        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-resident-view', array_merge(compact(
            'user', 
            'resident'
        ), $stats));
    }

    /**
     * Show edit form for resident
     */
    public function editResident($id)
    {
        $user = Auth::user();
        $resident = Resident::findOrFail($id);
        $households = Household::orderBy('household_number')->get();
        $stats = $this->_getProfilingStats();

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-resident-edit', array_merge(compact(
            'user', 
            'resident', 
            'households'
        ), $stats));
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

        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;

        $resident->update($validated);
        $newHouseholdId = $resident->household_id;

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
            $newHousehold = Household::find($newHouseholdId);
            if ($newHousehold) {
                $newHousehold->total_members = Resident::where('household_id', $newHousehold->id)
                    ->where('is_active', true)
                    ->count();
                $newHousehold->save();
            }
        }

        return redirect()->route('secretary.resident-profiling', ['view' => 'residents'])
                         ->with('success', 'Resident updated successfully!');
    }

    /**
     * Delete resident (soft delete by setting is_active to false)
     */
    public function destroyResident($id)
    {
        $resident = Resident::findOrFail($id);
        $householdId = $resident->household_id;

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

        return redirect()->route('secretary.resident-profiling', ['view' => $view])
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
        $stats = $this->_getProfilingStats();
        
        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-household-create', array_merge(compact(
            'user'
        ), $stats));
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

        Household::create($validated + ['total_members' => 0]);

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
                         ->with('success', 'Household added successfully!');
    }

    /**
     * Show edit form for household
     */
    public function editHousehold($id)
    {
        $user = Auth::user();
        $household = Household::findOrFail($id);
        $stats = $this->_getProfilingStats();

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-household-edit', array_merge(compact(
            'user', 
            'household'
        ), $stats));
    }

    /**
     * Update household
     */
    public function updateHousehold(Request $request, $id)
    {
        $household = Household::findOrFail($id);

        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'household_number' => 'required|string|max:50|unique:households,household_number,' . $id,
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'status' => 'required|in:complete,incomplete',
        ]);

        $validated['total_members'] = Resident::where('household_id', $id)->where('is_active', true)->count();
        $household->update($validated);

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
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

        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
                         ->with('success', 'Household and all associated residents removed successfully!');
    }


    // ============================================
    // DOCUMENT SERVICES (Settings)
    // ============================================

    /**
     * Display the document services page (Types & Templates)
     */
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
        
        // --- FIXED VIEW PATH ---
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
    // ============================================

    /**
     * Display the list of document requests (Pending, Approved, etc.)
     */
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
                ->orWhere('control_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(15);

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-document-requests', compact(
            'user',
            'requests',
            'status',
            'search'
        ));
    }

    /**
     * Show the details of a specific document request for processing
     */
    public function showDocumentRequest($id)
    {
        $user = Auth::user();
        $request = DocumentRequest::with(['resident', 'documentType.template'])->findOrFail($id);

        // --- FIXED VIEW PATH ---
        return view('dashboard.secretary-document-process', compact('user', 'request'));
    }

    /**
     * Update a document request (Approve, Deny, Mark as Completed)
     */
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