<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialTransaction;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Carbon\Carbon;

class TreasurerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $manualRevenue = FinancialTransaction::where('type', 'revenue')->where('status', 'approved')->sum('amount');
        $documentRevenue = DocumentRequest::where('payment_status', 'Paid')->sum('price');
        $totalRevenue = $manualRevenue + $documentRevenue;

        $totalExpenses = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->sum('amount');
        $monthlyBudget = DB::table('settings')->where('key', 'monthly_budget')->value('value') ?? 150000;

        $expensesThisMonth = FinancialTransaction::where('type', 'expense')->where('status', 'approved')
            ->whereMonth('transaction_date', $currentMonth)->whereYear('transaction_date', $currentYear)->sum('amount');

        $stats = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'monthly_budget' => $monthlyBudget,
            'expenses_this_month' => $expensesThisMonth,
            'available_balance' => ($monthlyBudget + $totalRevenue) - $totalExpenses
        ];

        $recentTransactions = FinancialTransaction::latest()->take(5)->get();
        $pendingCount = FinancialTransaction::where('status', 'pending')->count();

        return view('dashboard.treasurer', compact('user', 'stats', 'recentTransactions', 'pendingCount'));
    }

    public function financialManagement(Request $request)
    {
        $user = Auth::user();
        
        // ** FIX: Variable defined here **
        $pendingRequests = FinancialTransaction::where('type', 'expense')->where('status', 'pending')->latest()->get();

        $query = FinancialTransaction::latest();
        if ($request->has('month') && $request->month != '') {
            $query->whereMonth('transaction_date', Carbon::parse($request->month)->month);
            $query->whereYear('transaction_date', Carbon::parse($request->month)->year);
        }
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        $transactions = $query->paginate(10)->withQueryString();

        $annualBudget = DB::table('settings')->where('key', 'annual_budget')->value('value') ?? 2000000;
        
        $manualRevenue = FinancialTransaction::where('type', 'revenue')->where('status', 'approved')->sum('amount');
        $documentRevenue = DocumentRequest::where('payment_status', 'Paid')->sum('price');
        $totalRevenue = $manualRevenue + $documentRevenue;

        $totalSpent = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->sum('amount');
        $availableBudget = ($annualBudget + $totalRevenue) - $totalSpent;

        $expenseCategories = ['Infrastructure', 'Health Programs', 'Education', 'Environmental', 'Others'];
        $utilization = [];

        foreach ($expenseCategories as $cat) {
            $spent = FinancialTransaction::where('type', 'expense')->where('status', 'approved')->where('category', $cat)->sum('amount');
            $settingKey = 'budget_' . strtolower(str_replace(' ', '_', $cat));
            $limit = DB::table('settings')->where('key', $settingKey)->value('value') ?? 100000;
            $utilization[] = ['name' => $cat, 'spent' => $spent, 'limit' => $limit, 'percentage' => ($limit > 0 ? ($spent/$limit)*100 : 0)];
        }

        $revenuePerformance = [];
        $iraCollected = FinancialTransaction::where('category', 'Government IRA')->sum('amount');
        $revenuePerformance[] = ['name' => 'Government IRA', 'target' => 1500000, 'collected' => $iraCollected, 'percentage' => ($iraCollected / 1500000) * 100];

        $documentTypes = DocumentType::all();
        foreach($documentTypes as $type) {
            $collected = DocumentRequest::where('document_type', $type->id)->where('payment_status', 'Paid')->sum('price');
            $target = 50000; 
            $revenuePerformance[] = ['name' => $type->name, 'collected' => $collected, 'target' => $target, 'percentage' => ($target > 0 ? ($collected / $target) * 100 : 0)];
        }
        
        // ** FIX: Variable passed here **
        return view('dashboard.treasurer-financial-management', compact(
            'user', 'transactions', 'pendingRequests', 'annualBudget', 'totalRevenue', 
            'totalSpent', 'availableBudget', 'utilization', 'revenuePerformance'
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
        ]);

        $status = ($validated['type'] == 'revenue') ? 'approved' : 'pending';

        FinancialTransaction::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'status' => $status,
            'requested_by' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'transaction_date' => $validated['transaction_date'],
        ]);

        return redirect()->back()->with('success', 'Transaction recorded successfully.');
    }

    public function updateTransaction(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
        ]);
        $transaction->update($validated);
        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }

    public function destroyTransaction($id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $transaction->delete();
        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }

    public function updateTransactionStatus(Request $request, $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);
        $status = $request->input('status'); 
        if(in_array($status, ['approved', 'rejected'])) {
            $transaction->status = $status;
            $transaction->save();
            return redirect()->back()->with('success', 'Transaction has been ' . ucfirst($status) . '.');
        }
        return redirect()->back()->with('error', 'Invalid status provided.');
    }

    public function updateBudget(Request $request)
    {
        $validated = $request->validate([
            'annual_budget' => 'required|numeric|min:0',
            'infrastructure_budget' => 'nullable|numeric|min:0',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'annual_budget'],
            ['value' => $validated['annual_budget'], 'created_at' => now(), 'updated_at' => now()]
        );

        if($request->has('infrastructure_budget')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'budget_infrastructure'],
                ['value' => $request->input('infrastructure_budget'), 'created_at' => now(), 'updated_at' => now()]
            );
        }
        return redirect()->back()->with('success', 'Budget allocations updated successfully.');
    }

    public function exportReports(Request $request)
    {
        $type = $request->input('report_type', 'transactions');
        if ($type === 'transactions') {
            $data = FinancialTransaction::orderBy('transaction_date', 'desc')->get();
            $filename = "transactions_report_" . date('Y-m-d') . ".csv";
            $headers = [ "Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0" ];
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
}