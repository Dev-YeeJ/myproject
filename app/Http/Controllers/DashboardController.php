<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Import Schema facade
use Illuminate\Support\Facades\Log;
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
            // Get total residents count with multiple fallback methods
            $residentCount = $this->getResidentCount();
            $householdCount = $this->safeCount(Household::class); // Count all households

            $stats = [
                'registered_residents' => $residentCount,
                'documents_processed' => $this->safeCount(Document::class),
                // Updated to count all households instead of filtering by a potentially non-existent 'active' status
                'active_households' => $householdCount,
                'monthly_budget' => 150000, // Placeholder: fetch actual budget data
                'budget_remaining' => 35000, // Placeholder: fetch/calculate remaining budget
                'pending_documents' => $this->safeCountWhere(Document::class, 'status', 'pending'),
                'documents_completed_today' => $this->safeCountDate(Document::class, 'completed_at'), // Assuming 'completed_at' exists
                'active_projects' => $this->safeCountWhere(Project::class, 'status', 'active'), // Assuming 'status' exists
                'projects_near_completion' => $this->safeCountWhere(Project::class, 'progress', 80, '>='), // Assuming 'progress' exists
                'recent_incidents' => $this->safeCountRecent(Incident::class, 7), // Incidents in the last 7 days
                'resolved_incidents' => $this->safeCountWhere(Incident::class, 'status', 'resolved'), // Assuming 'status' exists
                'health_programs' => $this->safeCount(HealthProgram::class),
                'ongoing_programs' => $this->safeCountWhere(HealthProgram::class, 'status', 'ongoing'), // Assuming 'status' exists
            ];
        } catch (\Exception $e) {
            // Fallback stats if there's an error during data fetching
            Log::error('Captain Dashboard stats error: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            $stats = $this->getFallbackStats(); // Use a helper for fallback
        }

        return view('dashboard.captain', compact('user', 'stats'));
    }

    /**
     * Get resident count with multiple fallback strategies
     */
    private function getResidentCount()
    {
        try {
            // Check if residents table exists
            if (!Schema::hasTable('residents')) {
                Log::warning('Residents table does not exist');
                return 0;
            }

            // Check if is_active column exists
            $hasIsActive = Schema::hasColumn('residents', 'is_active');

            if ($hasIsActive) {
                // Count active residents
                return Resident::where('is_active', true)->count();
            } else {
                // No is_active column, count all residents
                 Log::warning('Residents table exists but missing is_active column. Counting all residents.');
                return Resident::count();
            }
        } catch (\Exception $e) {
            Log::error('Error counting residents: ' . $e->getMessage());
            return 0; // Return 0 on any error
        }
    }

    /**
     * Safe count helper method - Checks if model/table exists
     */
    private function safeCount($modelClass)
    {
        try {
            // Basic check if class exists, though autoloading usually handles this
            if (!class_exists($modelClass)) {
                 Log::warning("Model class {$modelClass} does not exist.");
                return 0;
            }
            $modelInstance = new $modelClass();
            $tableName = $modelInstance->getTable();
            if (!Schema::hasTable($tableName)) {
                Log::warning("Table {$tableName} for model {$modelClass} does not exist.");
                return 0;
            }
            return $modelClass::count();
        } catch (\Exception $e) {
            Log::error("Error counting {$modelClass}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Safe count with where clause - Checks table and column
     */
    private function safeCountWhere($modelClass, $column, $value, $operator = '=')
    {
        try {
             if (!class_exists($modelClass)) {
                 Log::warning("Model class {$modelClass} does not exist.");
                return 0;
            }
            $modelInstance = new $modelClass();
            $tableName = $modelInstance->getTable();
            if (!Schema::hasTable($tableName)) {
                 Log::warning("Table {$tableName} for model {$modelClass} does not exist.");
                return 0;
            }
            if (!Schema::hasColumn($tableName, $column)) {
                Log::warning("Column {$column} does not exist in table {$tableName}.");
                return 0; // Can't filter if column doesn't exist
            }
            return $modelClass::where($column, $operator, $value)->count();
        } catch (\Exception $e) {
            Log::error("Error counting {$modelClass} where {$column} {$operator} {$value}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Safe count for date filtering - Checks table and column
     */
    private function safeCountDate($modelClass, $column)
    {
        try {
            if (!class_exists($modelClass)) {
                 Log::warning("Model class {$modelClass} does not exist.");
                return 0;
            }
            $modelInstance = new $modelClass();
            $tableName = $modelInstance->getTable();
             if (!Schema::hasTable($tableName)) {
                 Log::warning("Table {$tableName} for model {$modelClass} does not exist.");
                return 0;
            }
            if (!Schema::hasColumn($tableName, $column)) {
                 Log::warning("Date column {$column} does not exist in table {$tableName}.");
                return 0; // Can't filter if column doesn't exist
            }
            return $modelClass::whereDate($column, today())->count();
        } catch (\Exception $e) {
            Log::error("Error counting {$modelClass} by date column {$column}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Safe count for recent records (uses created_at) - Checks table and column
     */
    private function safeCountRecent($modelClass, $days, $dateColumn = 'created_at')
    {
        try {
            if (!class_exists($modelClass)) {
                 Log::warning("Model class {$modelClass} does not exist.");
                return 0;
            }
             $modelInstance = new $modelClass();
            $tableName = $modelInstance->getTable();
             if (!Schema::hasTable($tableName)) {
                 Log::warning("Table {$tableName} for model {$modelClass} does not exist.");
                return 0;
            }
            if (!Schema::hasColumn($tableName, $dateColumn)) {
                Log::warning("Date column {$dateColumn} does not exist in table {$tableName} for recent count.");
                return 0; // Can't filter if column doesn't exist
            }
            return $modelClass::where($dateColumn, '>=', now()->subDays($days))->count();
        } catch (\Exception $e) {
            Log::error("Error counting recent {$modelClass} using column {$dateColumn}: " . $e->getMessage());
            return 0;
        }
    }

    /**
    * Provides a default set of stats with 0 values in case of errors.
    */
    private function getFallbackStats()
    {
        return [
            'registered_residents' => 0,
            'documents_processed' => 0,
            'active_households' => 0,
            'monthly_budget' => 150000, // Keep placeholders as they might not depend on DB
            'budget_remaining' => 35000, // Keep placeholders
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


    /**
     * Secretary Dashboard
     */
    public function secretary()
    {
        $user = Auth::user();

        try {
             $residentCount = $this->getResidentCount();
             $householdCount = $this->safeCount(Household::class);
             $stats = [
                'registered_residents' => $residentCount,
                'documents_processed' => $this->safeCount(Document::class),
                'active_households' => $householdCount, // Count all households
                'pending_documents' => $this->safeCountWhere(Document::class, 'status', 'pending'),
                'documents_today' => $this->safeCountDate(Document::class, 'created_at'), // Assuming documents have created_at
            ];
        } catch (\Exception $e) {
             Log::error('Secretary Dashboard stats error: ' . $e->getMessage());
             $stats = [ // Fallback
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

         try {
             $residentCount = $this->getResidentCount();
             $householdCount = $this->safeCount(Household::class);
             $stats = [
                'registered_residents' => $residentCount,
                'documents_processed' => $this->safeCount(Document::class), // Or maybe count financial docs?
                'active_households' => $householdCount,
                'total_revenue' => 250000, // Placeholder: fetch actual financial data
                'total_expenses' => 180000, // Placeholder
                'monthly_budget' => 150000, // Placeholder
                'budget_spent' => 115000, // Placeholder
            ];
         } catch (\Exception $e) {
             Log::error('Treasurer Dashboard stats error: ' . $e->getMessage());
              $stats = [ // Fallback
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'total_revenue' => 0,
                'total_expenses' => 0,
                'monthly_budget' => 0,
                'budget_spent' => 0,
             ];
         }


        return view('dashboards.treasurer', compact('user', 'stats'));
    }

    /**
     * Kagawad Dashboard
     */
    public function kagawad()
    {
        $user = Auth::user();

        try {
             $residentCount = $this->getResidentCount();
             $householdCount = $this->safeCount(Household::class);
             $stats = [
                'registered_residents' => $residentCount,
                'documents_processed' => $this->safeCount(Document::class),
                'active_households' => $householdCount,
                'active_projects' => $this->safeCountWhere(Project::class, 'status', 'active'),
                'completed_projects' => $this->safeCountWhere(Project::class, 'status', 'completed'),
                'community_programs' => 8, // Placeholder: fetch actual program data
            ];
        } catch (\Exception $e) {
            Log::error('Kagawad Dashboard stats error: ' . $e->getMessage());
            $stats = [ // Fallback
                'registered_residents' => 0,
                'documents_processed' => 0,
                'active_households' => 0,
                'active_projects' => 0,
                'completed_projects' => 0,
                'community_programs' => 0,
            ];
        }

        return view('dashboards.kagawad', compact('user', 'stats'));
    }

    /**
     * Health Worker Dashboard
     */
    public function health()
    {
        $user = Auth::user();

        try {
             $residentCount = $this->getResidentCount();
             $householdCount = $this->safeCount(Household::class);
             $stats = [
                'registered_residents' => $residentCount,
                // 'documents_processed' => $this->safeCount(Document::class), // Maybe less relevant?
                'active_households' => $householdCount,
                'health_programs' => $this->safeCount(HealthProgram::class),
                'ongoing_programs' => $this->safeCountWhere(HealthProgram::class, 'status', 'ongoing'),
                'completed_programs' => $this->safeCountWhere(HealthProgram::class, 'status', 'completed'),
                'beneficiaries_served' => 245, // Placeholder: fetch actual beneficiary data
                'scheduled_activities' => 12, // Placeholder: fetch actual schedule data
            ];
        } catch (\Exception $e) {
             Log::error('Health Worker Dashboard stats error: ' . $e->getMessage());
            $stats = [ // Fallback
                'registered_residents' => 0,
                // 'documents_processed' => 0,
                'active_households' => 0,
                'health_programs' => 0,
                'ongoing_programs' => 0,
                'completed_programs' => 0,
                'beneficiaries_served' => 0,
                'scheduled_activities' => 0,
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
            $residentCount = $this->getResidentCount();
            $householdCount = $this->safeCount(Household::class);
            $totalIncidents = $this->safeCount(Incident::class);
            $resolvedIncidents = $this->safeCountWhere(Incident::class, 'status', 'resolved');
            $resolutionRate = ($totalIncidents > 0) ? round(($resolvedIncidents / $totalIncidents) * 100) : 0;

             $stats = [
                'registered_residents' => $residentCount,
                // 'documents_processed' => $this->safeCount(Document::class), // Less relevant?
                'active_households' => $householdCount,
                'recent_incidents' => $this->safeCountRecent(Incident::class, 7), // Last 7 days
                'resolved_incidents' => $resolvedIncidents,
                'pending_incidents' => $this->safeCountWhere(Incident::class, 'status', 'pending'),
                'resolution_rate' => $resolutionRate, // Calculated rate
            ];
        } catch (\Exception $e) {
             Log::error('Tanod Dashboard stats error: ' . $e->getMessage());
            $stats = [ // Fallback
                'registered_residents' => 0,
                // 'documents_processed' => 0,
                'active_households' => 0,
                'recent_incidents' => 0,
                'resolved_incidents' => 0,
                'pending_incidents' => 0,
                'resolution_rate' => 0,
            ];
        }

        return view('dashboards.tanod', compact('user', 'stats'));
    }

    /**
     * Redirect user to their role-based dashboard
     * Assumes role checking methods (e.g., isBarangayCaptain()) exist on the User model
     */
    protected function redirectBasedOnRole($user)
    {
        // Check if the methods exist before calling them
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

        // Optional: Redirect residents or other roles to a default page or logout
         if (method_exists($user, 'isResident') && $user->isResident()) {
             // Example: Redirect residents to a profile page or just log them out
             Auth::logout();
             request()->session()->invalidate();
             request()->session()->regenerateToken();
             return redirect()->route('login')->with('error', 'Residents do not have dashboard access.');
         }

        // Fallback if role is not recognized or method doesn't exist
        Log::warning("User ID {$user->id} with role '{$user->role}' could not be redirected to a dashboard.");
        Auth::logout(); // Log out unrecognized roles
        return redirect()->route('login')->with('error', 'Your role does not have dashboard access.');
        // Or: abort(403, 'Unauthorized access - user role not recognized or dashboard not configured.');
    }
}
