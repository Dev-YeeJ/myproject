<?php
// app/Http/Controllers/CaptainController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CaptainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the captain dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get statistics for dashboard
        $stats = [
            'registered_residents' => Resident::where('is_active', true)->count(),
            'monthly_budget' => 500000, // Example value
            'budget_remaining' => 325000, // Example value
            'pending_documents' => 12, // Example value
            'documents_completed_today' => 5, // Example value
            'active_projects' => 8, // Example value
            'projects_near_completion' => 3, // Example value
            'recent_incidents' => 4, // Example value
            'resolved_incidents' => 3, // Example value
            'health_programs' => 6, // Example value
            'ongoing_programs' => 4, // Example value
        ];

        return view('dashboards.captain', compact('user', 'stats'));
    }

    /**
     * Display the resident profiling page
     */
    public function residentProfiling()
    {
        $user = Auth::user();
        
        // Get all residents with their household information
        $residents = Resident::with('household')
            //->where('is_active', true)
            //->orderBy('last_name', 'asc')
            ->get();
        
        // Get statistics
        $totalResidents = Resident::where('is_active', true)->count();
        $totalHouseholds = Household::count();
        $completeHouseholds = Household::count();

        
$seniorCitizens = Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 60')
    ->where('is_active', true)
    ->count();

        $minors = Resident::whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')
    ->where('is_active', true)
    ->count();

        
        return view('dashboard.captain-resident-profiling', compact(
            'user',
            'residents',
            'totalResidents',
            'totalHouseholds',
            'completeHouseholds',
            'seniorCitizens',
            'minors'
        ));
    }
}