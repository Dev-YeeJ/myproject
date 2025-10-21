<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Resident;
use App\Models\Document;
use App\Models\Household;
use App\Models\Project;
use App\Models\Incident;
use App\Models\HealthProgram;

class DashboardController extends Controller
{
    /**
     * Main dashboard index - redirects to role-based dashboard
     */
    public function index()
    {
        $user = Auth::user();
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Barangay Captain Dashboard
     */
    public function captain()
    {
        $user = Auth::user();
        
        try {
            $stats = [
                'registered_residents' => Resident::count() ?? 0,
                'documents_processed' => Document::count() ?? 0,
                'active_households' => Household::where('status', 'active')->count() ?? 0,
                'monthly_budget' => 150000,
                'budget_remaining' => 35000,
                'pending_documents' => Document::where('status', 'pending')->count() ?? 0,
                'documents_completed_today' => Document::whereDate('completed_at', today())->count() ?? 0,
                'active_projects' => Project::where('status', 'active')->count() ?? 0,
                'projects_near_completion' => Project::where('progress', '>=', 80)->count() ?? 0,
                'recent_incidents' => Incident::where('created_at', '>=', now()->subDays(7))->count() ?? 0,
                'resolved_incidents' => Incident::where('status', 'resolved')->count() ?? 0,
                'health_programs' => HealthProgram::count() ?? 0,
                'ongoing_programs' => HealthProgram::where('status', 'ongoing')->count() ?? 0,
            ];
        } catch (\Exception $e) {
            $stats = [
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'monthly_budget' => 150000,
                'budget_remaining' => 35000,
                'pending_documents' => 0,
                'documents_completed_today' => 0,
                'active_projects' => 0,
                'projects_near_completion' => 0,
                'recent_incidents' => 0,
                'resolved_incidents' => 0,
                'health_programs' => 0,
                'ongoing_programs' => 0,
            ];
        }

        return view('dashboard.captain', compact('user', 'stats'));
    }
    

    /**
     * Secretary Dashboard
     */
    public function secretary()
    {
        $user = Auth::user();
        
        try {
            $stats = [
                'registered_residents' => Resident::count() ?? 0,
                'documents_processed' => Document::count() ?? 0,
                'active_households' => Household::where('status', 'active')->count() ?? 0,
                'pending_documents' => Document::where('status', 'pending')->count() ?? 0,
                'documents_today' => Document::whereDate('created_at', today())->count() ?? 0,
            ];
        } catch (\Exception $e) {
            $stats = [
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'pending_documents' => 0,
                'documents_today' => 0,
            ];
        }

        return view('dashboard.secretary', compact('user', 'stats'));
    }

    /**
     * Treasurer Dashboard
     */
    public function treasurer()
    {
        $user = Auth::user();
        
        $stats = [
            'registered_residents' => Resident::count() ?? 0,
            'documents_processed' => Document::count() ?? 0,
            'active_households' => Household::where('status', 'active')->count() ?? 0,
            'total_revenue' => 250000,
            'total_expenses' => 180000,
            'monthly_budget' => 150000,
            'budget_spent' => 115000,
        ];

        return view('dashboard.treasurer', compact('user', 'stats'));
    }

    /**
     * Kagawad Dashboard
     */
    public function kagawad()
    {
        $user = Auth::user();
        
        try {
            $stats = [
                'registered_residents' => Resident::count() ?? 0,
                'documents_processed' => Document::count() ?? 0,
                'active_households' => Household::where('status', 'active')->count() ?? 0,
                'active_projects' => Project::where('status', 'active')->count() ?? 0,
                'completed_projects' => Project::where('status', 'completed')->count() ?? 0,
                'community_programs' => 8,
            ];
        } catch (\Exception $e) {
            $stats = [
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'active_projects' => 0,
                'completed_projects' => 0,
                'community_programs' => 8,
            ];
        }

        return view('dashboard.kagawad', compact('user', 'stats'));
    }

    /**
     * Health Worker Dashboard
     */
    public function health()
    {
        $user = Auth::user();
        
        try {
            $stats = [
                'registered_residents' => Resident::count() ?? 0,
                'documents_processed' => Document::count() ?? 0,
                'active_households' => Household::where('status', 'active')->count() ?? 0,
                'health_programs' => HealthProgram::count() ?? 0,
                'ongoing_programs' => HealthProgram::where('status', 'ongoing')->count() ?? 0,
                'completed_programs' => HealthProgram::where('status', 'completed')->count() ?? 0,
                'beneficiaries_served' => 245,
                'scheduled_activities' => 12,
            ];
        } catch (\Exception $e) {
            $stats = [
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'health_programs' => 0,
                'ongoing_programs' => 0,
                'completed_programs' => 0,
                'beneficiaries_served' => 245,
                'scheduled_activities' => 12,
            ];
        }

        return view('dashboard.health', compact('user', 'stats'));
    }

    /**
     * Tanod Dashboard
     */
    public function tanod()
    {
        $user = Auth::user();
        
        try {
            $stats = [
                'registered_residents' => Resident::count() ?? 0,
                'documents_processed' => Document::count() ?? 0,
                'active_households' => Household::where('status', 'active')->count() ?? 0,
                'recent_incidents' => Incident::where('created_at', '>=', now()->subDays(7))->count() ?? 0,
                'resolved_incidents' => Incident::where('status', 'resolved')->count() ?? 0,
                'pending_incidents' => Incident::where('status', 'pending')->count() ?? 0,
                'resolution_rate' => 98,
            ];
        } catch (\Exception $e) {
            $stats = [
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'recent_incidents' => 0,
                'resolved_incidents' => 0,
                'pending_incidents' => 0,
                'resolution_rate' => 98,
            ];
        }

        return view('dashboard.tanod', compact('user', 'stats'));
    }

    /**
     * Redirect user to their role-based dashboard
     */
    protected function redirectBasedOnRole($user)
    {
        if (method_exists($user, 'isBarangayCaptain') && $user->isBarangayCaptain()) {
            return redirect()->route('dashboard.captain');
        }
        
        if (method_exists($user, 'isSecretary') && $user->isSecretary()) {
            return redirect()->route('dashboard.secretary');
        }
        
        if (method_exists($user, 'isTreasurer') && $user->isTreasurer()) {
            return redirect()->route('dashboard.treasurer');
        }
        
        if (method_exists($user, 'isKagawad') && $user->isKagawad()) {
            return redirect()->route('dashboard.kagawad');
        }
        
        if (method_exists($user, 'isHealthWorker') && $user->isHealthWorker()) {
            return redirect()->route('dashboard.health');
        }
        
        if (method_exists($user, 'isTanod') && $user->isTanod()) {
            return redirect()->route('dashboard.tanod');
        }
        
        abort(403, 'Unauthorized access - user role not recognized.');
    }
}