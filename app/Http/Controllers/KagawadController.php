<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Models
use App\Models\Resident;
use App\Models\Household;
use App\Models\Project;
use App\Models\BlotterRecord;
use App\Models\Announcements;

class KagawadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Kagawad Dashboard Index
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
        // Get active projects for the "Active Projects" card
        $projects = Project::where('status', 'In Progress')
            ->orderBy('end_date', 'asc')
            ->take(3)
            ->get();

        // Get upcoming events/announcements
        $upcomingEvents = Announcements::where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.kagawad', compact('user', 'stats', 'projects', 'upcomingEvents'));
    }

    /**
     * Resident Profiling (Read-Only Mode)
     * Handles both Resident Directory and Household Directory views
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

            // Search Scope
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%");
                });
            }

            // Status Filter
            if ($status) {
                $query->where('household_status', $status);
            }

            // Gender Filter
            if ($gender) {
                $query->where('gender', $gender);
            }

            // Special Category Filters
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

        // --- Stats Calculation (Required for the Header Badges) ---
        $totalResidents = Resident::where('is_active', true)->count();
        $totalHouseholds = Household::count();
        $completeHouseholds = Household::where('status', 'complete')->count();
        $seniorCitizens = Resident::where('is_senior_citizen', true)->where('is_active', true)->count();
        $minors = Resident::where('age', '<', 18)->where('is_active', true)->count();
        $totalPwd = Resident::where('is_active', true)->where('is_pwd', true)->count();
        $total4ps = Resident::where('is_active', true)->where('is_4ps', true)->count();
        $totalVoters = Resident::where('is_active', true)->where('is_registered_voter', true)->count();
        $incompleteHouseholds = $totalHouseholds - $completeHouseholds;

        // Return the KAGAWAD SPECIFIC view
        return view('dashboard.kagawad-resident-profiling', compact(
            'user', 'view', 'residents', 'households', 'totalResidents', 'totalHouseholds',
            'completeHouseholds', 'seniorCitizens', 'minors', 'filter', 'totalPwd',
            'total4ps', 'totalVoters', 'incompleteHouseholds'
        ));
    }

    /**
     * Project Monitoring
     * Re-uses the Captain's logic to ensure consistent data viewing.
     */
    public function projects()
    {
        // Reuse the logic from CaptainController to avoid code duplication
        return app(CaptainController::class)->projectMonitoring(request());
    }

    /**
     * Incident & Blotter
     * Re-uses the Captain's logic. Kagawads are part of the Lupon, so they need access.
     */
    public function incidents()
    {
        // Reuse the logic from CaptainController
        return app(CaptainController::class)->incidentAndBlotter(request());
    }
}