<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Project;
use App\Models\BlotterRecord;
use App\Models\Announcements;
use App\Models\FinancialTransaction;

class KagawadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * =========================================================================
     * 1. DASHBOARD INDEX
     * =========================================================================
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Fetch Real Stats
        $stats = [
            'active_projects' => Project::where('status', 'In Progress')->count(),
            'registered_residents' => Resident::where('is_active', true)->count(),
            'active_households' => Household::count(),
            'pending_incidents' => BlotterRecord::whereIn('status', ['Open', 'Under Investigation', 'For Mediation'])->count(),
        ];

        // 2. Fetch Data for the Activity Grids
        $projects = Project::where('status', 'In Progress')
            ->orderBy('end_date', 'asc')
            ->take(3)
            ->get();

        $upcomingEvents = Announcements::where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.kagawad', compact('user', 'stats', 'projects', 'upcomingEvents'));
    }

    /**
     * =========================================================================
     * 2. RESIDENT PROFILING (Read-Only)
     * =========================================================================
     */
    public function residents(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'residents');
        $search = $request->input('search');
        $status = $request->input('status'); 
        $gender = $request->input('gender'); 
        $filter = $request->input('filter'); 

        $residents = null;
        $households = null;

        // --- Logic for Resident View ---
        if ($view === 'residents') {
            $query = Resident::with(['household', 'user']) 
                ->where('is_active', true);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%");
                });
            }

            if ($status) $query->where('household_status', $status);
            if ($gender) $query->where('gender', $gender);

            if ($filter) {
                switch ($filter) {
                    case 'seniors': $query->where('is_senior_citizen', true); break;
                    case 'pwd': $query->where('is_pwd', true); break;
                    case '4ps': $query->where('is_4ps', true); break;
                    case 'voters': $query->where('is_registered_voter', true); break;
                }
            }
            
            $residents = $query->orderBy('last_name')->paginate(10);

        // --- Logic for Household View ---
        } else {
            $query = Household::with(['head', 'activeResidents']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('household_name', 'like', "%{$search}%")
                        ->orWhere('household_number', 'like', "%{$search}%") 
                        ->orWhereHas('head', function ($q_head) use ($search) {
                            $q_head->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                        });
                });
            }

            if ($status && in_array($status, ['complete', 'incomplete'])) {
                $query->where('status', $status);
            }

            $households = $query->orderBy('household_number')->paginate(10);
        }

        // --- Stats Calculation ---
        $totalResidents = Resident::where('is_active', true)->count();
        $totalHouseholds = Household::count();
        $completeHouseholds = Household::where('status', 'complete')->count();
        $seniorCitizens = Resident::where('is_senior_citizen', true)->where('is_active', true)->count();
        $minors = Resident::where('age', '<', 18)->where('is_active', true)->count();
        $totalPwd = Resident::where('is_active', true)->where('is_pwd', true)->count();
        $total4ps = Resident::where('is_active', true)->where('is_4ps', true)->count();
        $totalVoters = Resident::where('is_active', true)->where('is_registered_voter', true)->count();
        $incompleteHouseholds = $totalHouseholds - $completeHouseholds;

        return view('dashboard.kagawad-resident-profiling', compact(
            'user', 'view', 'residents', 'households', 'totalResidents', 'totalHouseholds',
            'completeHouseholds', 'seniorCitizens', 'minors', 'filter', 'totalPwd',
            'total4ps', 'totalVoters', 'incompleteHouseholds'
        ));
    }

    /**
     * =========================================================================
     * 3. PROJECT MONITORING (Proposals & Updates)
     * =========================================================================
     */
    public function projects(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'active'); // 'active' or 'proposals'

        // 1. Get Active Projects (Approved by Captain)
        $activeProjects = Project::whereIn('status', ['In Progress', 'Planning'])
            ->orderBy('start_date', 'desc')
            ->paginate(6, ['*'], 'active_page');

        // 2. Get Proposals (Created by Kagawad, Waiting for Captain)
        $proposals = Project::where('status', 'Proposed')
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'proposal_page');

        // 3. Stats for the top cards
        $stats = [
            'total_active' => Project::where('status', 'In Progress')->count(),
            'my_proposals' => Project::where('status', 'Proposed')->count(),
            'completed' => Project::where('status', 'Completed')->count(),
        ];

        return view('dashboard.kagawad-project-monitoring', compact('user', 'activeProjects', 'proposals', 'view', 'stats'));
    }

    /**
     * Store a New Project Proposal
     * Status defaults to 'Proposed' so Captain must approve it.
     */
    public function storeProjectProposal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'required|string',
        ]);

        Project::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'budget' => $validated['budget'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'description' => $validated['description'],
            'status' => 'Proposed', // IMPORTANT: Captain must approve this
            'progress' => 0,
            'amount_spent' => 0,
        ]);

        return redirect()->route('kagawad.projects', ['view' => 'proposals'])
            ->with('success', 'Project proposal submitted successfully! Waiting for Captain approval.');
    }

    /**
     * Update Project Progress
     * Allowed only for 'Active' projects.
     */
    public function updateProjectProgress(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // Security check
        if ($project->status === 'Proposed') {
            return redirect()->back()->with('error', 'Cannot update progress on unapproved projects.');
        }

        $project->update([
            'progress' => $request->input('progress'),
            'status' => $request->input('progress') == 100 ? 'Completed' : $project->status
        ]);

        return redirect()->back()->with('success', 'Project progress updated.');
    }

    /**
     * Store Project Expense
     * Creates a 'Pending' financial transaction for the Captain to approve.
     */
    public function storeProjectExpense(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            // 'proof_image' => 'nullable|image|max:5120', // Uncomment if handling file uploads
        ]);

        FinancialTransaction::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'] . ' (Project Expense)',
            'amount' => $validated['amount'],
            'type' => 'expense',
            'category' => $validated['category'],
            'status' => 'pending', // IMPORTANT: Captain must approve deduction
            'transaction_date' => $validated['transaction_date'],
            'requested_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
        ]);

        return redirect()->back()->with('success', 'Expense recorded. Pending approval from Captain.');
    }
}