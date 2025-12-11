<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Resident;
use App\Models\Project;
use App\Models\SkOfficial;
use App\Models\FinancialTransaction; 
use Carbon\Carbon;

class SkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getFinancialSummary()
    {
        // Get budget or default to 0 to prevent crash
        $barangayBudget = DB::table('settings')->where('key', 'annual_budget')->value('value') ?? 0;
        $skAllocation = $barangayBudget * 0.10; 

        $committedFunds = Project::where('category', 'SK Project')
            ->where('status', '!=', 'Cancelled')
            ->sum('budget');

        $actualSpent = Project::sum('amount_spent'); // Simple fallback for spending

        return [
            'allocation' => $skAllocation,
            'committed'  => $committedFunds,
            'spent'      => $actualSpent,
            'remaining_commitment' => $skAllocation - $committedFunds,
            'cash_on_hand' => $skAllocation - $actualSpent
        ];
    }

    // 1. DASHBOARD
    public function index()
    {
        $user = Auth::user();
        $finances = $this->getFinancialSummary();

        $minDate = Carbon::now()->subYears(15)->format('Y-m-d');
        $maxDate = Carbon::now()->subYears(30)->format('Y-m-d');

        $stats = [
            'total_youth'       => Resident::where('is_active', true)->whereBetween('date_of_birth', [$maxDate, $minDate])->count(),
            'registered_voters' => Resident::where('is_active', true)->whereBetween('date_of_birth', [$maxDate, $minDate])->where('is_registered_voter', true)->count(),
            'students'          => Resident::where('is_active', true)->whereBetween('date_of_birth', [$maxDate, $minDate])->where('occupation', 'Student')->count(),
            'active_projects'   => Project::where('category', 'SK Project')->where('status', 'In Progress')->count(),
            'budget_allocation' => $finances['allocation'],
            'budget_remaining'  => $finances['cash_on_hand'], 
            'utilization_rate'  => $finances['allocation'] > 0 ? ($finances['spent'] / $finances['allocation']) * 100 : 0
        ];

        $upcomingEvents = Project::where('category', 'SK Project')
            ->where('status', '!=', 'Cancelled')
            ->where('start_date', '>=', Carbon::now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.sk-dashboard', compact('user', 'stats', 'upcomingEvents'));
    }

    // 2. YOUTH PROFILING
    public function youthProfiling(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $filter = $request->input('filter'); 

        $minDate = Carbon::now()->subYears(15)->format('Y-m-d');
        $maxDate = Carbon::now()->subYears(30)->format('Y-m-d');

        $query = Resident::where('is_active', true)
             ->whereBetween('date_of_birth', [$maxDate, $minDate])
             ->with('household');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($filter) {
            switch ($filter) {
                case 'student': $query->where('occupation', 'Student'); break;
                case 'working': $query->where('monthly_income', '>', 0); break;
                case 'voter': $query->where('is_registered_voter', true); break;
                case 'female': $query->where('gender', 'Female'); break;
                case 'male': $query->where('gender', 'Male'); break;
            }
        }

        $youths = $query->orderBy('last_name')->paginate(10)->withQueryString();
        
        $youths->getCollection()->transform(function ($resident) {
            $resident->calculated_age = Carbon::parse($resident->date_of_birth)->age;
            return $resident;
        });

        return view('dashboard.sk-youth-profiling', compact('user', 'youths'));
    }

    // 3. PROJECTS
    public function projects(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'All');
        
        $query = Project::where('category', 'SK Project');

        if($status !== 'All') {
            $query->where('status', $status);
        }

        $projects = $query->latest()->paginate(10);
        $finances = $this->getFinancialSummary();

        return view('dashboard.sk-projects', [
            'user' => $user,
            'projects' => $projects,
            'status' => $status,
            'remainingBudget' => $finances['remaining_commitment']
        ]);
    }

    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'required|string',
        ]);

        $finances = $this->getFinancialSummary();
        
        // Prevent negative budget but allow if allocation is 0 (for testing)
        if ($finances['allocation'] > 0 && $validated['budget'] > $finances['remaining_commitment']) {
            return redirect()->back()
                ->withErrors(['budget' => 'Insufficient Funds! Available: ₱' . number_format($finances['remaining_commitment'], 2)])
                ->withInput();
        }

        Project::create(array_merge($validated, [
            'category' => 'SK Project',
            'status' => 'Planning',
            'progress' => 0,
            'amount_spent' => 0
        ]));

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string',
            'budget' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'required|string',
            'status' => 'required',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        if ($validated['progress'] == 100) $validated['status'] = 'Completed';

        $project->update($validated);
        return redirect()->back()->with('success', 'Project updated.');
    }

    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return redirect()->back()->with('success', 'Project deleted.');
    }

    // 4. OFFICIALS
    public function manageOfficials() {
        $user = Auth::user();
        $officials = SkOfficial::with('resident')->where('is_active', true)->get();
        $existingIds = SkOfficial::where('is_active', true)->pluck('resident_id');
        
        $minDate = Carbon::now()->subYears(15)->format('Y-m-d');
        $maxDate = Carbon::now()->subYears(30)->format('Y-m-d');

        $eligible = Resident::whereBetween('date_of_birth', [$maxDate, $minDate])
            ->whereNotIn('id', $existingIds)
            ->where('is_active', true) 
            ->orderBy('last_name')
            ->get();

        return view('dashboard.sk-officials', compact('user', 'officials', 'eligible'));
    }

    public function storeOfficial(Request $request) {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'position' => 'required',
            'committee' => 'nullable|string',
            'term_start' => 'required|date',
            'term_end' => 'required|date|after:term_start',
        ]);

        if(SkOfficial::where('resident_id', $validated['resident_id'])->where('is_active', true)->exists()) {
             return back()->withErrors(['resident_id' => 'This resident is already an official.']);
        }

        SkOfficial::create(array_merge($validated, ['is_active' => true]));
        return back()->with('success', 'Official Appointed');
    }

    public function updateOfficial(Request $request, $id) {
        $official = SkOfficial::findOrFail($id);
        $official->update($request->only(['position', 'committee', 'term_start', 'term_end']));
        return back()->with('success', 'Official Updated');
    }
    
    public function destroyOfficial($id) {
        SkOfficial::destroy($id);
        return back()->with('success', 'Official Removed');
    }

    // 5. PRINT LIST (FIXES YOUR 500 ERROR)
    public function printYouthList(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter'); 

        $minDate = Carbon::now()->subYears(15)->format('Y-m-d');
        $maxDate = Carbon::now()->subYears(30)->format('Y-m-d');

        $query = Resident::where('is_active', true)
             ->whereBetween('date_of_birth', [$maxDate, $minDate])
             ->with('household');

        if ($filter) {
            switch ($filter) {
                case 'student': $query->where('occupation', 'Student'); break;
                case 'working': $query->where('monthly_income', '>', 0); break;
                case 'voter': $query->where('is_registered_voter', true); break;
                case 'female': $query->where('gender', 'Female'); break;
                case 'male': $query->where('gender', 'Male'); break;
            }
        }

        $youths = $query->orderBy('last_name')->get(); 
        
        $youths->transform(function ($resident) {
            $resident->calculated_age = Carbon::parse($resident->date_of_birth)->age;
            return $resident;
        });

        return view('dashboard.sk-print-youth', compact('user', 'youths', 'filter'));
    }
}