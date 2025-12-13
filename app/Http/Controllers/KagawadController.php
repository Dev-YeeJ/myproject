<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; 

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
        $activeQuery = Project::whereIn('status', ['In Progress', 'Planning'])->latest();
        
        // 2. Get Proposals (Created by Kagawad, Waiting for Captain)
        $proposalsQuery = Project::where('status', 'Proposed')->latest();

        // Search Logic
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $activeQuery->where('title', 'like', "%{$search}%");
            $proposalsQuery->where('title', 'like', "%{$search}%");
        }

        // 3. Stats for the top cards (FIXED THE MISSING KEYS)
        $stats = [
            'total_active'       => Project::whereIn('status', ['In Progress', 'Planning'])->count(),
            'my_proposals'       => Project::where('status', 'Proposed')->count(),
            'completed_projects' => Project::where('status', 'Completed')->count(), // Correct key name
            'total_projects'     => Project::count(),
            'total_budget'       => Project::sum('budget'),
            'total_spent'        => Project::sum('amount_spent'),
        ];

        // 4. Paginate Results
        $activeProjects = $view === 'active' ? $activeQuery->paginate(6, ['*'], 'active_page') : collect([]);
        $proposals = $view === 'proposals' ? $proposalsQuery->paginate(6, ['*'], 'proposal_page') : collect([]);

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

    /**
     * =========================================================================
     * 4. INCIDENT & BLOTTER (Mediation & Investigation)
     * =========================================================================
     */
    public function incidents(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');

        // Kagawads see ALL cases to assist in mediation
        $query = BlotterRecord::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('complainant', 'like', "%{$search}%")
                  ->orWhere('respondent', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'All') {
            $query->where('status', $status);
        }

        // Prioritize Active Cases (Open, Hearing, Mediation)
        $incidents = $query->orderByRaw("FIELD(status, 'Open', 'Scheduled for Hearing', 'For Mediation', 'Under Investigation', 'Resolved', 'Dismissed')")
                            ->orderBy('date_reported', 'desc')
                            ->paginate(10);

        $stats = [
            'for_mediation' => BlotterRecord::where('status', 'For Mediation')->count(),
            'scheduled' => BlotterRecord::where('status', 'Scheduled for Hearing')->count(),
            'resolved_this_month' => BlotterRecord::where('status', 'Resolved')
                ->whereMonth('updated_at', Carbon::now()->month)->count(),
        ];

        return view('dashboard.kagawad-incident-blotter', compact('user', 'incidents', 'search', 'status', 'stats'));
    }

    /**
     * Store Incident (For Walk-in Complainants assisted by Kagawad)
     */
    public function storeIncident(Request $request)
    {
        $validated = $request->validate([
            'complainant' => 'required|string',
            'respondent' => 'nullable|string',
            'incident_type' => 'required|string',
            'location' => 'required|string',
            'date_reported' => 'required|date',
            'narrative' => 'required|string',
        ]);

        BlotterRecord::create([
            'case_number' => 'BL-' . date('Ymd') . '-' . rand(100,999), 
            'complainant' => $validated['complainant'],
            'respondent' => $validated['respondent'],
            'incident_type' => $validated['incident_type'],
            'location' => $validated['location'],
            'date_reported' => $validated['date_reported'],
            'narrative' => $validated['narrative'],
            'status' => 'Open',
            'priority' => 'Medium', // Default for Kagawad entry
            'actions_taken' => "[" . now()->format('M d, Y h:i A') . "] Case filed via Kagawad assistance.",
        ]);

        return redirect()->back()->with('success', 'Incident recorded successfully.');
    }

    /**
     * Process Incident (Mediation / Investigation Updates)
     */
    public function updateIncident(Request $request, $id)
    {
        $incident = BlotterRecord::findOrFail($id);

        $request->validate([
            'action' => 'required|in:update_status,schedule_hearing,add_log',
            'remarks' => 'required|string',
            'new_status' => 'nullable|string'
        ]);

        $timestamp = now()->format('M d, Y h:i A');
        $kagawadName = Auth::user()->last_name;
        $newLog = "";

        switch ($request->action) {
            case 'schedule_hearing':
                $incident->status = 'Scheduled for Hearing';
                $newLog = "[$timestamp] [Kag. $kagawadName] SET HEARING: " . $request->remarks;
                break;
            
            case 'update_status':
                $incident->status = $request->new_status;
                $newLog = "[$timestamp] [Kag. $kagawadName] STATUS UPDATE: Changed to {$request->new_status}. REMARKS: " . $request->remarks;
                break;

            case 'add_log':
                // Status doesn't change, just adding notes
                $newLog = "[$timestamp] [Kag. $kagawadName] INVESTIGATION NOTE: " . $request->remarks;
                break;
        }

        // Append to existing log
        $incident->actions_taken = $incident->actions_taken . "\n" . $newLog;
        $incident->save();

        return redirect()->back()->with('success', 'Case updated successfully.');
    }
    /**
     * =========================================================================
     * 5. ANNOUNCEMENTS (Restricted to Residents Audience)
     * =========================================================================
     */

    public function announcements(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // Fetch announcements created by this Kagawad OR all public resident announcements
        // You can adjust this to 'where('user_id', Auth::id())' if they should only see their own.
        $query = Announcements::with('user')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(9);
        
        return view('dashboard.kagawad-announcements', compact('user', 'announcements', 'search'));
    }

    public function createAnnouncement()
    {
        $user = Auth::user();
        return view('dashboard.kagawad-announcements-create', compact('user'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            // 'audience' validation is removed because we hardcode it
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        Announcements::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'audience' => 'Residents', // STRICTLY ENFORCED
            'is_published' => $request->has('is_published'), // Toggle or Draft
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('kagawad.announcements.index')
            ->with('success', 'Announcement for residents created successfully.');
    }

    public function editAnnouncement($id)
    {
        $user = Auth::user();
        // Ensure Kagawad can only edit their OWN posts
        $announcement = Announcements::where('user_id', Auth::id())->findOrFail($id);
        
        return view('dashboard.kagawad-announcements-edit', compact('user', 'announcement'));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcements::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $announcement->image_path = $request->file('image')->store('announcements', 'public');
        }

        $announcement->title = $validated['title'];
        $announcement->content = $validated['content'];
        // Audience remains 'Residents' automatically, or you can re-save it if needed
        $announcement->is_published = $request->has('is_published');
        $announcement->save();

        return redirect()->route('kagawad.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroyAnnouncement($id)
    {
        $announcement = Announcements::where('user_id', Auth::id())->findOrFail($id);
        
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        
        $announcement->delete();
        return redirect()->route('kagawad.announcements.index')
            ->with('success', 'Announcement deleted.');
    }
}