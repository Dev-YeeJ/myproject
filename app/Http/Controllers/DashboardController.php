<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

// Models
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Project;
use App\Models\Incident; // Or BlotterRecord
use App\Models\BlotterRecord;
use App\Models\DocumentRequest;
use App\Models\Announcements;
use App\Models\FinancialTransaction;
use App\Models\HealthProgram;
use App\Models\SkOfficial;

class DashboardController extends Controller
{
    /**
     * Middleware to ensure user is logged in
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Main dashboard index - redirects to role-based dashboard
     */
    public function index()
    {
        $user = Auth::user();
        return $this->redirectBasedOnRole($user);
    }

    // ==========================================
    // 1. CAPTAIN DASHBOARD
    // ==========================================
    public function captain()
    {
        $user = Auth::user();

        try {
            // Financial Calculations
            $monthlyBudget = $this->getMonthlyBudget();
            $totalExpenses = $this->getTotalExpenses();
            $budgetRemaining = ($monthlyBudget * 12) - $totalExpenses;

            $stats = [
                'registered_residents'      => $this->getResidentCount(),
                'active_households'         => $this->safeCount(Household::class),
                
                // Documents
                'documents_processed'       => $this->safeCount(DocumentRequest::class),
                'pending_documents'         => $this->safeCountWhere(DocumentRequest::class, 'status', 'Pending'),
                'documents_completed_today' => $this->safeCountDate(DocumentRequest::class, 'updated_at', 'Completed'),
                
                // Financials
                'monthly_budget'            => $monthlyBudget,
                'budget_remaining'          => $budgetRemaining,
                
                // Projects
                'active_projects'           => $this->safeCountWhere(Project::class, 'status', 'In Progress'),
                'projects_near_completion'  => $this->safeCountWhere(Project::class, 'progress', 80, '>='),
                
                // Incidents
                'recent_incidents'          => $this->safeCountRecent(BlotterRecord::class, 7),
                'resolved_incidents'        => $this->safeCountWhere(BlotterRecord::class, 'status', 'Resolved'),
                
                // Health
                'health_programs'           => $this->safeCount(Project::class), 
                'ongoing_programs'          => $this->safeCountWhere(Project::class, 'category', 'Health'),
            ];
        } catch (\Exception $e) {
            Log::error('Captain Dashboard Error: ' . $e->getMessage());
            $stats = $this->getFallbackStats();
        }

        // Feeds
        $activities = $this->getRecentActivities();
        $upcomingEvents = $this->getUpcomingEvents();

        return view('dashboard.captain', compact('user', 'stats', 'activities', 'upcomingEvents'));
    }

    // ==========================================
    // 2. SECRETARY DASHBOARD (Mirrors Captain)
    // ==========================================
    public function secretary()
    {
        $user = Auth::user();

        try {
            // Financial Calculations (Read Only)
            $monthlyBudget = $this->getMonthlyBudget();
            $totalExpenses = $this->getTotalExpenses();
            $budgetRemaining = ($monthlyBudget * 12) - $totalExpenses;

            $stats = [
                'registered_residents'      => $this->getResidentCount(),
                'active_households'         => $this->safeCount(Household::class),
                
                // Documents
                'documents_processed'       => $this->safeCount(DocumentRequest::class),
                'pending_documents'         => $this->safeCountWhere(DocumentRequest::class, 'status', 'Pending'),
                'documents_completed_today' => $this->safeCountDate(DocumentRequest::class, 'updated_at', 'Completed'),
                
                // Financials
                'monthly_budget'            => $monthlyBudget,
                'budget_remaining'          => $budgetRemaining,
                
                // Projects
                'active_projects'           => $this->safeCountWhere(Project::class, 'status', 'In Progress'),
                'projects_near_completion'  => $this->safeCountWhere(Project::class, 'progress', 80, '>='),
                
                // Incidents
                'recent_incidents'          => $this->safeCountRecent(BlotterRecord::class, 7),
                'resolved_incidents'        => $this->safeCountWhere(BlotterRecord::class, 'status', 'Resolved'),
                
                // Health
                'health_programs'           => $this->safeCount(Project::class), 
                'ongoing_programs'          => $this->safeCountWhere(Project::class, 'category', 'Health'),
            ];
        } catch (\Exception $e) {
            Log::error('Secretary Dashboard Error: ' . $e->getMessage());
            $stats = $this->getFallbackStats();
        }

        $activities = $this->getRecentActivities();
        $upcomingEvents = $this->getUpcomingEvents();

        return view('dashboard.secretary', compact('user', 'stats', 'activities', 'upcomingEvents'));
    }

    // ==========================================
    // 3. TREASURER DASHBOARD
    // ==========================================
    public function treasurer()
    {
        $user = Auth::user();

        try {
            $monthlyBudget = $this->getMonthlyBudget();
            $totalRevenue = 0;
            $totalExpenses = 0;

            if (class_exists(FinancialTransaction::class)) {
                $totalRevenue = FinancialTransaction::where('type', 'revenue')->where('status', 'approved')->sum('amount');
                $totalExpenses = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->sum('amount');
            }

            $stats = [
                'registered_residents' => $this->getResidentCount(),
                'active_households'    => $this->safeCount(Household::class),
                'total_revenue'        => $totalRevenue,
                'total_expenses'       => $totalExpenses,
                'monthly_budget'       => $monthlyBudget,
                'budget_spent'         => $totalExpenses, 
                'pending_expenses'     => $this->safeCountWhere(FinancialTransaction::class, 'status', 'pending'),
            ];
        } catch (\Exception $e) {
            Log::error('Treasurer Dashboard Error: ' . $e->getMessage());
            $stats = $this->getFallbackStats();
        }

        return view('dashboard.treasurer', compact('user', 'stats'));
    }

    // ==========================================
    // 4. KAGAWAD DASHBOARD
    // ==========================================
    public function kagawad()
    {
        $user = Auth::user();

        try {
            $stats = [
                'registered_residents' => $this->getResidentCount(),
                'active_households'    => $this->safeCount(Household::class),
                'active_projects'      => $this->safeCountWhere(Project::class, 'status', 'In Progress'),
                'completed_projects'   => $this->safeCountWhere(Project::class, 'status', 'Completed'),
                'pending_proposals'    => $this->safeCountWhere(Project::class, 'status', 'Proposed'),
                'my_proposals'         => 0, // Placeholder for filtering by user_id if needed
            ];
        } catch (\Exception $e) {
            $stats = $this->getFallbackStats();
        }

        $upcomingEvents = $this->getUpcomingEvents();

        return view('dashboard.kagawad', compact('user', 'stats', 'upcomingEvents'));
    }

   public function health()
    {
        $user = Auth::user();

        try {
            $stats = [
                // Existing stats
                'registered_residents' => $this->getResidentCount(),
                'active_households'    => $this->safeCount(Household::class),
                'health_programs'      => $this->safeCountWhere(Project::class, 'category', 'Health'),
                
                // ADDED: Missing keys required by health.blade.php
                'ongoing_programs'     => $this->safeCountWhere(Project::class, 'status', 'In Progress'),
                'completed_programs'   => $this->safeCountWhere(Project::class, 'status', 'Completed'),
                'scheduled_activities' => $this->safeCount(Announcements::class), // Or use your specific Activity model
                'beneficiaries_served' => 0, // Placeholder: Add logic if you have a Beneficiary model
                
                // Keeping these if you use them elsewhere, though not in the view snippet provided
                'seniors'              => $this->safeCountWhere(Resident::class, 'is_senior_citizen', true),
                'pwd'                  => $this->safeCountWhere(Resident::class, 'is_pwd', true),
                'medical_requests'     => 0, 
            ];
        } catch (\Exception $e) {
            // Log the error so you can debug later if needed
            Log::error('Health Dashboard Error: ' . $e->getMessage());
            
            // Get basic fallbacks and ensure new keys exist to prevent crash
            $stats = $this->getFallbackStats();
            $stats['ongoing_programs'] = 0;
            $stats['completed_programs'] = 0;
            $stats['scheduled_activities'] = 0;
            $stats['beneficiaries_served'] = 0;
        }

        return view('dashboard.health', compact('user', 'stats'));
    }
    // ==========================================
    // 6. TANOD DASHBOARD
    // ==========================================
    public function tanod()
    {
        $user = Auth::user();

        try {
            $totalIncidents = $this->safeCount(BlotterRecord::class);
            $resolved = $this->safeCountWhere(BlotterRecord::class, 'status', 'Resolved');
            $rate = ($totalIncidents > 0) ? round(($resolved / $totalIncidents) * 100) : 0;

            $stats = [
                'registered_residents' => $this->getResidentCount(),
                'recent_incidents'     => $this->safeCountRecent(BlotterRecord::class, 7),
                'resolved_incidents'   => $resolved,
                'pending_incidents'    => $this->safeCountWhere(BlotterRecord::class, 'status', 'Open'),
                'resolution_rate'      => $rate,
            ];
        } catch (\Exception $e) {
            $stats = $this->getFallbackStats();
        }

        return view('dashboard.tanod', compact('user', 'stats'));
    }

    // ==========================================
    // 7. SK OFFICIAL DASHBOARD
    // ==========================================
    public function sk()
    {
        $user = Auth::user();
        try {
            // Filter residents aged 15-30
            $minDate = Carbon::now()->subYears(15)->format('Y-m-d');
            $maxDate = Carbon::now()->subYears(30)->format('Y-m-d');
            
            $youthCount = 0;
            if(class_exists(Resident::class)) {
                $youthCount = Resident::whereDate('date_of_birth', '<=', $minDate)
                                      ->whereDate('date_of_birth', '>=', $maxDate)
                                      ->count();
            }

            $stats = [
                'total_youth'     => $youthCount,
                'sk_projects'     => $this->safeCountWhere(Project::class, 'category', 'SK Project'),
                'upcoming_events' => $this->safeCountWhere(Announcements::class, 'audience', 'SK Officials'),
            ];
        } catch (\Exception $e) {
            $stats = ['total_youth' => 0, 'sk_projects' => 0, 'upcoming_events' => 0];
        }

        return view('dashboard.sk', compact('user', 'stats'));
    }

    // ==========================================
    // 8. RESIDENT DASHBOARD
    // ==========================================
    public function resident()
    {
        $user = Auth::user();
        $myRequests = [];
        
        try {
            if(class_exists(DocumentRequest::class)) {
                // Assuming Resident is linked via user_id or similar logic
                // This is a placeholder as Resident linkage logic depends on your User model
                $residentRecord = Resident::where('user_id', $user->id)->first();
                if($residentRecord) {
                    $myRequests = DocumentRequest::where('resident_id', $residentRecord->id)->get();
                }
            }
        } catch (\Exception $e) { /* Ignore */ }

        return view('dashboard.resident', compact('user', 'myRequests'));
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function getResidentCount()
    {
        try {
            return class_exists(Resident::class) ? Resident::where('is_active', true)->count() : 0;
        } catch (\Exception $e) { return 0; }
    }

    private function safeCount($model)
    {
        try { return class_exists($model) ? $model::count() : 0; } catch (\Exception $e) { return 0; }
    }

    private function safeCountWhere($model, $col, $val, $op = '=')
    {
        try { return class_exists($model) ? $model::where($col, $op, $val)->count() : 0; } catch (\Exception $e) { return 0; }
    }

    private function safeCountDate($model, $col, $status = null)
    {
        try {
            if (!class_exists($model)) return 0;
            $q = $model::whereDate($col, Carbon::today());
            if ($status) $q->where('status', $status);
            return $q->count();
        } catch (\Exception $e) { return 0; }
    }

    private function safeCountRecent($model, $days)
    {
        try {
            return class_exists($model) ? $model::where('created_at', '>=', Carbon::now()->subDays($days))->count() : 0;
        } catch (\Exception $e) { return 0; }
    }

    private function getMonthlyBudget()
    {
        try {
            return DB::table('settings')->where('key', 'annual_budget')->value('value') / 12 ?? 150000;
        } catch (\Exception $e) { return 150000; }
    }

    private function getTotalExpenses()
    {
        try {
            return class_exists(FinancialTransaction::class) 
                ? FinancialTransaction::where('type', 'expense')->sum('amount') 
                : 0;
        } catch (\Exception $e) { return 0; }
    }

    private function getRecentActivities()
    {
        try {
            return class_exists(DocumentRequest::class) 
                ? DocumentRequest::with('resident', 'documentType')->latest()->take(5)->get() 
                : collect([]);
        } catch (\Exception $e) { return collect([]); }
    }

    private function getUpcomingEvents()
    {
        try {
            return class_exists(Announcements::class) 
                ? Announcements::latest()->take(4)->get() 
                : collect([]);
        } catch (\Exception $e) { return collect([]); }
    }

    private function getFallbackStats()
    {
        return [
            'registered_residents' => 0, 'documents_processed' => 0, 'active_households' => 0,
            'monthly_budget' => 0, 'budget_remaining' => 0, 'pending_documents' => 0,
            'documents_completed_today' => 0, 'active_projects' => 0, 'projects_near_completion' => 0,
            'recent_incidents' => 0, 'resolved_incidents' => 0, 'health_programs' => 0, 'ongoing_programs' => 0,
        ];
    }

    /**
     * Redirect user to their role-based dashboard
     */
    protected function redirectBasedOnRole($user)
    {
        // Ensure these methods exist in your User model
        if ($user->isBarangayCaptain()) return redirect()->route('captain.dashboard');
        if ($user->isSecretary())       return redirect()->route('secretary.dashboard');
        if ($user->isTreasurer())       return redirect()->route('treasurer.dashboard');
        if ($user->isKagawad())         return redirect()->route('kagawad.dashboard');
        if ($user->isHealthWorker())    return redirect()->route('health.dashboard');
        if ($user->isTanod())           return redirect()->route('tanod.dashboard');
        if ($user->isSkofficial())      return redirect()->route('sk.dashboard');
        if ($user->isResident())        return redirect()->route('resident.dashboard');
        
        Auth::logout();
        return redirect('/login')->with('error', 'Unable to determine user role or dashboard.');
    }
}