<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resident;
use App\Models\Household;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Template;
use App\Models\Announcements;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\FinancialTransaction;
use App\Models\DocumentRequirement;

class SecretaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ============================================
    // RESIDENT PROFILING
    // ============================================

    public function residentProfiling(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'residents');
        $search = $request->input('search');
        $status = $request->input('status');
        $gender = $request->input('gender');
        $filter = $request->input('filter');

        $residents = null;
        $households = null;

        if ($view === 'residents') {
            $query = Resident::with(['household', 'user']) 
                ->where('is_active', true)
                ->search($search)
                ->byHouseholdStatus($status)
                ->byGender($gender);

            if ($filter) {
                switch ($filter) {
                    case 'seniors': $query->where('is_senior_citizen', true); break;
                    case 'pwd': $query->where('is_pwd', true); break;
                    case '4ps': $query->where('is_4ps', true); break;
                    case 'voters': $query->where('is_registered_voter', true); break;
                }
            }
            $residents = $query->orderBy('last_name')->paginate(10);
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
            if ($status && $status !== 'All Status' && in_array($status, ['complete', 'incomplete'])) {
                $query->where('status', $status);
            }
            $households = $query->orderBy('household_number')->paginate(5);
        }

        $totalResidents = Resident::where('is_active', true)->count();
        $totalHouseholds = Household::count();
        $completeHouseholds = Household::where('status', 'complete')->count();
        $seniorCitizens = Resident::where('is_senior_citizen', true)->where('is_active', true)->count();
        $minors = Resident::where('age', '<', 18)->where('is_active', true)->count();
        $totalPwd = Resident::where('is_active', true)->where('is_pwd', true)->count();
        $total4ps = Resident::where('is_active', true)->where('is_4ps', true)->count();
        $totalVoters = Resident::where('is_active', true)->where('is_registered_voter', true)->count();
        $incompleteHouseholds = $totalHouseholds - $completeHouseholds;

        return view('dashboard.secretary-resident-profiling', compact(
            'user', 'view', 'residents', 'households', 'totalResidents', 'totalHouseholds', 
            'completeHouseholds', 'seniorCitizens', 'minors', 'filter', 
            'totalPwd', 'total4ps', 'totalVoters', 'incompleteHouseholds'
        ));
    }

    public function createResident(Request $request)
    {
        $user = Auth::user();
        $households = Household::orderBy('household_number')->get();
        $selectedHousehold = $request->input('household_id', null);
        return view('dashboard.secretary-resident-add', compact('user', 'households', 'selectedHousehold'));
    }

    public function storeResident(Request $request)
    {
        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

        if ($request->input('occupation') === 'Student') {
            $request->merge(['monthly_income' => null]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'household_id' => 'nullable|exists:households,id',
            'household_status' => 'required|in:Household Head,Spouse,Child,Member',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:residents,email|unique:users,email',
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
            'precinct_number' => 'nullable|required_if:is_registered_voter,true|string|max:100',
            'pwd_id_number' => 'nullable|required_if:is_pwd,true|string|max:100',
            'disability_type' => 'nullable|required_if:is_pwd,true|string|max:255',
        ]);

        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;
        $validated['is_active'] = true;
        
        if (!$validated['is_registered_voter']) $validated['precinct_number'] = null;
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }
        
        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        $resident = Resident::create($validated); 

        if ($resident->household_id) {
            $household = Household::find($resident->household_id);
            if ($household) {
                $household->updateTotalMembers();
                $household->updateHouseholdStatus();
            }
        }

        return redirect()->route('secretary.resident-profiling', ['view' => 'residents'])
            ->with('success', 'Resident added successfully!');
    }

    public function showResident($id)
    {
        $user = Auth::user();
        $resident = Resident::with(['household', 'user'])->findOrFail($id);
        $defaultPassword = null;
        if ($resident->date_of_birth) {
            $lastName = Str::slug($resident->last_name, '');
            $birthdate = Carbon::parse($resident->date_of_birth)->format('Ymd');
            $defaultPassword = $lastName . $birthdate;
        }
        return view('dashboard.secretary-resident-view', compact('user', 'resident', 'defaultPassword'));
    }

    public function editResident($id)
    {
        $user = Auth::user();
        $resident = Resident::findOrFail($id);
        $households = Household::orderBy('household_number')->get();
        return view('dashboard.secretary-resident-edit', compact('user', 'resident', 'households'));
    }

    public function updateResident(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);
        $oldHouseholdId = $resident->household_id;

        $request->merge([
            'is_registered_voter' => $request->input('is_registered_voter', 0),
            'is_indigenous' => $request->input('is_indigenous', 0),
            'is_pwd' => $request->input('is_pwd', 0),
            'is_4ps' => $request->input('is_4ps', 0),
        ]);

        if ($request->input('occupation') === 'Student') {
            $request->merge(['monthly_income' => null]);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated,Divorced',
            'household_id' => 'nullable|exists:households,id',
            'household_status' => 'required|in:Household Head,Spouse,Child,Member',
            'address' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => [
                'nullable', 'email', 'max:255', 
                Rule::unique('residents')->ignore($id),
                Rule::unique('users')->ignore($resident->user_id)
            ],
            'occupation' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'is_registered_voter' => 'boolean',
            'is_indigenous' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps' => 'boolean',
            'precinct_number' => 'nullable|required_if:is_registered_voter,true|string|max:100',
            'pwd_id_number' => 'nullable|required_if:is_pwd,true|string|max:100',
            'disability_type' => 'nullable|required_if:is_pwd,true|string|max:255',
        ]);

        $birthDate = new \DateTime($validated['date_of_birth']);
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        $validated['age'] = $age;
        $validated['is_senior_citizen'] = $age >= 60;
        
        if (!$validated['is_registered_voter']) $validated['precinct_number'] = null;
        if (!$validated['is_pwd']) {
            $validated['pwd_id_number'] = null;
            $validated['disability_type'] = null;
        }

        if ($validated['household_status'] === 'Household Head' && $validated['household_id']) {
            Resident::where('household_id', $validated['household_id'])
                ->where('household_status', 'Household Head')
                ->where('id', '!=', $id) 
                ->where('is_active', true)
                ->update(['household_status' => 'Member']);
        }

        $resident->update($validated);
        $newHouseholdId = $resident->household_id; 

        if ($newHouseholdId) {
            $newHousehold = Household::find($newHouseholdId);
            if ($newHousehold) {
                $newHousehold->updateTotalMembers();
                $newHousehold->updateHouseholdStatus();
            }
        }

        if ($oldHouseholdId && $oldHouseholdId != $newHouseholdId) {
            $oldHousehold = Household::find($oldHouseholdId);
            if ($oldHousehold) {
                $oldHousehold->updateTotalMembers();
                $oldHousehold->updateHouseholdStatus();
            }
        }

        return redirect()->route('secretary.resident.show', $resident->id)
            ->with('success', 'Resident updated successfully!');
    }

    public function destroyResident($id)
    {
        $resident = Resident::findOrFail($id);
        $householdId = $resident->household_id;
        $resident->is_active = false;
        $resident->save(); 

        if ($householdId) {
            $household = Household::find($householdId);
            if ($household) {
                $household->updateTotalMembers();
                $household->updateHouseholdStatus();
            }
        }

        $view = request('view', 'residents');
        return redirect()->route('secretary.resident-profiling', ['view' => $view])
            ->with('success', 'Resident removed successfully!');
    }

    public function resetPassword(Resident $resident)
    {
        if ($resident->user) {
            $lastName = Str::slug($resident->last_name, '');
            $birthdate = Carbon::parse($resident->date_of_birth)->format('Ymd');
            $defaultPassword = $lastName . $birthdate;
            $resident->user->password = \Illuminate\Support\Facades\Hash::make($defaultPassword);
            $resident->user->save();
            return redirect()->back()->with('success', "Password for {$resident->user->username} has been reset to '{$defaultPassword}'.");
        }
        return redirect()->back()->with('error', 'This resident does not have a linked user account.');
    }

    // ============================================
    // HOUSEHOLD MANAGEMENT
    // ============================================

    public function createHousehold()
    {
        $user = Auth::user();
        $nextHouseholdNumber = \App\Models\Household::generateHouseholdNumber();
        return view('dashboard.secretary-household-create', compact('user', 'nextHouseholdNumber'));
    }

    public function storeHousehold(Request $request)
    {
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);
        Household::create($validated + ['total_members' => 0, 'status' => 'incomplete']);
        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household added successfully!');
    }

    public function editHousehold($id)
    {
        $user = Auth::user();
        $household = Household::findOrFail($id);
        return view('dashboard.secretary-household-edit', compact('user', 'household'));
    }

    public function updateHousehold(Request $request, $id)
    {
        $household = Household::findOrFail($id);
        $validated = $request->validate([
            'household_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
        ]);
        $validated['total_members'] = Resident::where('household_id', $id)->where('is_active', true)->count();
        $household->update($validated);
        $household->updateHouseholdStatus();
        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household updated successfully!');
    }

    public function destroyHousehold($id)
    {
        $household = Household::findOrFail($id);
        Resident::where('household_id', $id)->where('is_active', true)->update(['is_active' => false]);
        $household->delete();
        return redirect()->route('secretary.resident-profiling', ['view' => 'households'])
            ->with('success', 'Household and all associated residents removed successfully!');
    }
    
    public function showHousehold($id)
    {
        $user = Auth::user();
        $household = Household::with(['activeResidents', 'head'])->findOrFail($id);
        return view('dashboard.secretary-household-view', compact('user', 'household'));
    }

    // ============================================
    // DOCUMENT SERVICES 
    // ============================================
    
   public function documentServices(Request $request)
    {
        $user = Auth::user();
        $view = $request->input('view', 'requests');

        $stats = [
            'total_types' => DocumentType::count(),
            'total_templates' => Template::count(),
            'paid_documents' => DocumentType::where('requires_payment', true)->count(),
            'active_types' => DocumentType::where('is_active', true)->count(),
            'pending_requests' => DocumentRequest::whereIn('status', ['Pending', 'Processing', 'Under Review'])->count(),
            'requests_today' => DocumentRequest::whereDate('created_at', Carbon::today())->count(),
        ];

        $documentRequests = null;
        $documentTypes = null;
        $templates = null;

        if ($view === 'requests') {
            $requestsQuery = DocumentRequest::with(['resident', 'documentType', 'requirements'])->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                 $search = $request->search;
                 $requestsQuery->where(function ($q) use ($search) {
                     $q->where('tracking_number', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('resident', function ($q_res) use ($search) {
                            $q_res->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                        });
                 });
            }
            if ($request->filled('status') && $request->status !== 'All') {
                $requestsQuery->where('status', $request->status);
            }
            $documentRequests = $requestsQuery->paginate(10, ['*'], 'page')->appends($request->except('page'));
        
        } elseif ($view === 'types') {
            $typesQuery = DocumentType::query();
            if ($request->filled('search_types')) {
                $typesQuery->where('name', 'like', '%' . $request->search_types . '%');
            }
            $documentTypes = $typesQuery->orderBy('name')->paginate(9, ['*'], 'page')->appends($request->except('page'));
        
        } else { // $view === 'templates'
            $templatesQuery = Template::with('documentType'); 
            if ($request->filled('search_templates')) {
                $templatesQuery->where('name', 'like', '%' . $request->search_templates . '%');
            }
            $templates = $templatesQuery->orderBy('name')->paginate(10, ['*'], 'page')->appends($request->except('page'));
        }

        return view('dashboard.secretary-document-services', compact('user', 'stats', 'view', 'documentRequests', 'documentTypes', 'templates'));
    }

    public function showDocumentRequest($id)
    {
        $user = Auth::user();
        $documentRequest = DocumentRequest::with(['resident', 'documentType', 'requirements'])
                                          ->findOrFail($id);
        
        return view('dashboard.secretary-document-view', compact('user', 'documentRequest'));
    }

    public function updateDocumentRequest(Request $request, $id)
    {
        $documentRequest = DocumentRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Pending,Processing,Under Review,Ready for Pickup,Completed,Rejected,Cancelled',
            'payment_status' => 'required|in:Unpaid,Paid,Waived,Verification Pending',
            'remarks' => 'nullable|string|max:1000',
            'generated_file' => 'nullable|file|mimes:pdf,docx,doc|max:5120' // 5MB max
        ]);

        if ($request->hasFile('generated_file')) {
            if ($documentRequest->generated_file_path && Storage::disk('public')->exists($documentRequest->generated_file_path)) {
                Storage::disk('public')->delete($documentRequest->generated_file_path);
            }
            $filePath = $request->file('generated_file')->store('generated_documents', 'public');
            $documentRequest->generated_file_path = $filePath;
        }

        $documentRequest->status = $validated['status'];
        $documentRequest->payment_status = $validated['payment_status'];
        $documentRequest->remarks = $validated['remarks'];
        $documentRequest->save();

        return redirect()->route('secretary.document.show', $id)
                         ->with('success', 'Document request and payment status updated successfully.');
    }

    public function downloadRequirement($id)
    {
        $requirement = DocumentRequirement::findOrFail($id);
        if (Storage::disk('public')->exists($requirement->file_path)) {
            return Storage::disk('public')->download($requirement->file_path, $requirement->file_name);
        }
        return redirect()->back()->with('error', 'File not found.');
    }

    // ============================================
    // ANNOUNCEMENT MANAGEMENT
    // ============================================

    public function announcements(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Announcements::with('user')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(9);
        return view('dashboard.secretary-announcements', compact('user', 'announcements', 'search'));
    }

    public function createAnnouncement()
    {
        $user = Auth::user();
        return view('dashboard.secretary-announcements-create', compact('user'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audience' => 'required|in:All,Residents,Barangay Officials,SK Officials', 
            'is_published' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        Announcements::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'audience' => $validated['audience'],
            'is_published' => $request->has('is_published'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('secretary.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function editAnnouncement($id)
    {
        $user = Auth::user();
        $announcement = Announcements::findOrFail($id);
        return view('dashboard.secretary-announcements-edit', compact('user', 'announcement'));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcements::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audience' => 'required|in:All,Residents,Barangay Officials,SK Officials',
        ]);

        if ($request->hasFile('image')) {
            if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $announcement->image_path = $request->file('image')->store('announcements', 'public');
        }

        $announcement->title = $validated['title'];
        $announcement->content = $validated['content'];
        $announcement->audience = $validated['audience'];
        $announcement->is_published = $request->has('is_published');
        $announcement->save();

        return redirect()->route('secretary.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroyAnnouncement($id)
    {
        $announcement = Announcements::findOrFail($id);
        if ($announcement->image_path && Storage::disk('public')->exists($announcement->image_path)) {
            Storage::disk('public')->delete($announcement->image_path);
        }
        $announcement->delete();
        return redirect()->route('secretary.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    // ============================================
    // FINANCIAL MANAGEMENT
    // ============================================

    public function financialManagement(Request $request)
    {
        $user = Auth::user();

        // 1. Fetch Budget Settings (Read Only)
        $annualBudget = DB::table('settings')->where('key', 'annual_budget')->value('value') ?? 2000000;
        
        // 2. Calculate Totals
        // Revenue (Manual + Docs)
        $manualRevenue = FinancialTransaction::where('type', 'revenue')
            ->where('status', 'approved')
            ->sum('amount');
        $documentRevenue = DocumentRequest::where('payment_status', 'Paid')->sum('price');
        $totalRevenue = $manualRevenue + $documentRevenue;

        // Expenses
        $totalSpent = FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->sum('amount');
        
        $availableBudget = ($annualBudget + $totalRevenue) - $totalSpent;

        // 3. Utilization Data for Charts
        $expenseCategories = ['Infrastructure', 'Health Programs', 'Education', 'Environmental', 'Others'];
        $utilization = [];

        foreach ($expenseCategories as $cat) {
            $spent = FinancialTransaction::where('type', 'expense')
                ->where('status', 'approved')
                ->where('category', $cat)
                ->sum('amount');
            
            $settingKey = 'budget_' . strtolower(str_replace(' ', '_', $cat));
            $limit = DB::table('settings')->where('key', $settingKey)->value('value') ?? 100000;

            $utilization[] = [
                'name' => $cat, 
                'spent' => $spent, 
                'limit' => $limit, 
                'percentage' => ($limit > 0 ? ($spent/$limit)*100 : 0)
            ];
        }

        // 4. Revenue Performance
        $revenuePerformance = [];
        $iraCollected = FinancialTransaction::where('category', 'Government IRA')->sum('amount');
        $revenuePerformance[] = [
            'name' => 'Government IRA',
            'target' => 1500000, 
            'collected' => $iraCollected,
            'percentage' => ($iraCollected / 1500000) * 100
        ];

        // 5. Transaction List (Filter Logic)
        $query = FinancialTransaction::latest();

        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('dashboard.secretary-financial', compact(
            'user', 'transactions', 'annualBudget', 'totalRevenue', 
            'totalSpent', 'availableBudget', 'utilization', 'revenuePerformance'
        ));
    }

    public function storeFinancialTransaction(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:revenue,expense',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
        ]);

        // CRITICAL LOGIC FOR SECRETARY:
        // Revenue = Approved immediately (Collecting cash).
        // Expense = ALWAYS 'pending' (Must be approved by Treasurer/Captain).
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

        $msg = $validated['type'] == 'expense' 
            ? 'Expense request submitted for approval.' 
            : 'Revenue recorded successfully.';

        return redirect()->back()->with('success', $msg);
    }

    public function exportFinancialReports(Request $request)
    {
        $type = $request->input('report_type', 'transactions');

        if ($type === 'transactions') {
            $data = FinancialTransaction::orderBy('transaction_date', 'desc')->get();
            $filename = "secretary_report_" . date('Y-m-d') . ".csv";
            
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
                    fputcsv($file, [
                        $row->id, $row->title, $row->type, $row->category, 
                        $row->amount, $row->status, $row->requested_by, $row->transaction_date
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
        return redirect()->back()->with('error', 'Export type not supported.');
    }
}