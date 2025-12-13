<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialTransaction;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\Announcements;
use App\Models\Resident;
use App\Models\Household;
use Carbon\Carbon;

class TreasurerController extends Controller
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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Calculate Totals (Aligned with Captain)
        $manualRevenue = FinancialTransaction::where('type', 'revenue')->where('status', 'approved')->sum('amount');
        $documentRevenue = DocumentRequest::where('payment_status', 'Paid')->sum('price');
        $totalRevenue = $manualRevenue + $documentRevenue;

        $totalExpenses = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->sum('amount');
        
        // Fetch Annual Budget (Note: Treasurer usually manages Annual, but your view requested Monthly logic for dashboard card)
        $annualBudget = DB::table('settings')->where('key', 'annual_budget')->value('value') ?? 2000000;
        $monthlyBudget = $annualBudget / 12; 

        // 2. Specific Stats
        $expensesThisMonth = FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'monthly_budget' => $monthlyBudget,
            'expenses_this_month' => $expensesThisMonth,
            'available_balance' => ($annualBudget + $totalRevenue) - $totalExpenses
        ];

        // 3. Recent & Pending
        $recentTransactions = FinancialTransaction::latest()->take(5)->get();
        $pendingCount = FinancialTransaction::where('status', 'pending')->count();

        return view('dashboard.treasurer', compact('user', 'stats', 'recentTransactions', 'pendingCount'));
    }

    /**
     * =========================================================================
     * 2. FINANCIAL MANAGEMENT (Captain's Logic applied to Treasurer)
     * =========================================================================
     */
    public function financialManagement(Request $request)
    {
        $user = Auth::user();
        
        // Retrieve Pending Requests (e.g. from Kagawads/Secretary)
        $pendingRequests = FinancialTransaction::where('type', 'expense')->where('status', 'pending')->latest()->get();

        // Transaction List with Filters
        $query = FinancialTransaction::with('project')->latest();
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('transaction_date', Carbon::parse($request->month)->month);
            $query->whereYear('transaction_date', Carbon::parse($request->month)->year);
        }
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        $transactions = $query->paginate(10)->withQueryString();

        // Budget Calculations
        $annualBudget = DB::table('settings')->where('key', 'annual_budget')->value('value') ?? 2000000;
        
        $totalRevenue = FinancialTransaction::where('type', 'revenue')->where('status', 'approved')->sum('amount');
        // Note: Captain's controller does NOT add DocumentRequest sum here in the $totalRevenue variable calculation for the *view*, 
        // because usually those are auto-recorded as FinancialTransactions when paid. 
        // If your system relies on auto-sync, stick to FinancialTransaction sum to avoid double counting.
        
        // Detailed Revenue Breakdown (Captain's Logic)
        $targets = [
            'Government IRA'    => 1500000, 
            'Community Tax'     => 20000, 
            'Document Services' => 15000,
            'Donations'         => 50000, 
            'Other Fees'        => 10000
        ];

        $revenuePerformance = [];
        foreach($targets as $category => $target) {
            $collected = FinancialTransaction::where('type', 'revenue')
                ->where('status', 'approved')
                ->where('category', $category)
                ->sum('amount');
            
            $revenuePerformance[] = [
                'name' => $category,
                'collected' => $collected,
                'target' => $target,
                'percentage' => ($target > 0 ? ($collected / $target) * 100 : 0)
            ];
        }

        $totalSpent = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->sum('amount');
        $availableBudget = ($annualBudget + $totalRevenue) - $totalSpent;

        // Expense Categories (Aligned with Captain)
        $expenseCategories = [
            'Infrastructure', 'Health Programs', 'Education', 'Environmental', 
            'Social Services', 'Emergency Fund', 'Office Supplies', 'Utilities', 'Honorarium', 'Others'
        ];
        
        $utilization = [];
        foreach ($expenseCategories as $cat) {
            $spent = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->where('category', $cat)->sum('amount');
            $settingKey = 'budget_' . strtolower(str_replace(' ', '_', $cat));
            $limit = DB::table('settings')->where('key', $settingKey)->value('value') ?? 100000;
            
            $utilization[] = [
                'name' => $cat, 'spent' => $spent, 'limit' => $limit, 
                'percentage' => ($limit > 0 ? ($spent/$limit)*100 : 0)
            ];
        }

        // Active Projects for Expense Linking (Treasurer needs this for dropdowns)
        $activeProjects = Project::where('status', '!=', 'Completed')->orderBy('title')->get();

        return view('dashboard.treasurer-financial-management', compact(
            'user', 'transactions', 'pendingRequests', 'annualBudget', 'totalRevenue', 
            'totalSpent', 'availableBudget', 'utilization', 'revenuePerformance', 'activeProjects'
        ));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:revenue,expense',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        // Treasurer is trusted, so status is Approved by default (unlike Secretary)
        $status = 'approved';

        $transaction = FinancialTransaction::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'status' => $status,
            'requested_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'transaction_date' => $validated['transaction_date'],
            'project_id' => $validated['project_id'] ?? null,
        ]);

        // Auto-update Project Spend if linked
        if ($validated['project_id']) {
            $this->recalculateProjectSpend($validated['project_id']);
        }

        return redirect()->back()->with('success', 'Transaction recorded and approved.');
    }

    public function updateTransaction(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $oldProjectId = $transaction->project_id;

        $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $transaction->update($validated);

        // Recalculate old and new project totals if changed
        if ($oldProjectId && $oldProjectId != $transaction->project_id) {
            $this->recalculateProjectSpend($oldProjectId);
        }
        if ($transaction->project_id) {
            $this->recalculateProjectSpend($transaction->project_id);
        }

        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }

    public function destroyTransaction($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $projectId = $transaction->project_id;
        $transaction->delete();

        if ($projectId) {
            $this->recalculateProjectSpend($projectId);
        }

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }

    public function updateTransactionStatus(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $status = $request->input('status'); 
        
        if(in_array($status, ['approved', 'rejected'])) {
            $transaction->status = $status;
            $transaction->save();
            
            // Recalculate if approved expense is linked to a project
            if ($status == 'approved' && $transaction->project_id) {
                $this->recalculateProjectSpend($transaction->project_id);
            }

            return redirect()->back()->with('success', 'Transaction has been ' . ucfirst($status) . '.');
        }
        return redirect()->back()->with('error', 'Invalid status provided.');
    }

    public function updateBudget(Request $request)
    {
        $validated = $request->validate(['annual_budget' => 'required|numeric']);
        DB::table('settings')->updateOrInsert(['key' => 'annual_budget'], ['value' => $validated['annual_budget']]);

        // Categories must match the view/financialManagement list
        $categories = [
            'Infrastructure', 'Health Programs', 'Education', 'Environmental', 
            'Social Services', 'Emergency Fund', 'Office Supplies', 'Utilities', 'Honorarium', 'Others'
        ];
        
        foreach($categories as $cat) {
            $key = 'budget_' . strtolower(str_replace(' ', '_', $cat));
            if($request->has($key)) {
                DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $request->input($key)]);
            }
        }
        return redirect()->back()->with('success', 'Budgets updated.');
    }

    public function exportReports(Request $request)
    {
        $type = $request->input('report_type', 'transactions');

        if ($type === 'transactions') {
            $data = FinancialTransaction::orderBy('transaction_date', 'desc')->get();
            $filename = "transactions_report_" . date('Y-m-d') . ".csv";
            
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
            
            $columns = ['ID', 'Title', 'Type', 'Category', 'Amount', 'Status', 'Recorded By', 'Date'];

            $callback = function() use ($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach($data as $row) {
                    fputcsv($file, [ $row->id, $row->title, $row->type, $row->category, $row->amount, $row->status, $row->requested_by, $row->transaction_date ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
        return redirect()->back()->with('error', 'Export type not supported yet.');
    }

    // ============================================
    // 3. ANNOUNCEMENTS (Treasurer can post financial updates)
    // ============================================
    public function announcements(Request $request)
    {
        $user = Auth::user();
        $query = Announcements::latest();
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%");
        }
        $announcements = $query->paginate(9);
        return view('dashboard.treasurer-announcements', compact('user', 'announcements'));
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    private function recalculateProjectSpend($projectId)
    {
        $project = Project::find($projectId);
        if ($project) {
            $totalSpent = FinancialTransaction::where('project_id', $projectId)
                ->where('type', 'expense')
                ->where('status', 'approved')
                ->sum('amount');
            
            $project->amount_spent = $totalSpent;
            $project->save();
        }
    }
}