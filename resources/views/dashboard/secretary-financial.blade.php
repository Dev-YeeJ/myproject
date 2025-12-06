@extends('layouts.dashboard-layout')

@section('title', 'Financial Overview')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('secretary.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i> <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.resident-profiling') }}" class="nav-link">
            <i class="fas fa-users"></i> <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i> <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- This route name matches the web.php I provided --}}
        <a href="{{ route('secretary.financial-management') }}" class="nav-link active">
            <i class="fas fa-coins"></i> <span>Financials</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.announcements.index') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i> <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i> <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* Styling */
    .finance-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 30px; }
    .finance-card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); height: 100%; display: flex; flex-direction: column; }
    .finance-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F3F4F6; padding-bottom: 10px; }
    .finance-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1F2937; }
    
    .scrollable-list { flex: 1; overflow-y: auto; max-height: 350px; padding-right: 5px; }
    .scrollable-list::-webkit-scrollbar { width: 5px; }
    .scrollable-list::-webkit-scrollbar-track { background: #f1f1f1; }
    .scrollable-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }
    
    .progress-item { margin-bottom: 16px; }
    .progress-labels { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.85rem; font-weight: 600; color: #4B5563; }
    .progress-bg { width: 100%; height: 8px; background: #F3F4F6; border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
    
    .action-btn { padding: 8px 16px; border-radius: 6px; border: none; color: white; cursor: pointer; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
    .action-btn:hover { opacity: 0.9; color: white; }
    .btn-blue { background: #3B82F6; }
    .btn-green { background: #10B981; }
    .btn-purple { background: #8B5CF6; }
    .btn-secondary { background: #6B7280; }
    
    .custom-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .custom-table th { text-align: left; padding: 14px 16px; border-bottom: 2px solid #E5E7EB; color: #6B7280; font-size: 0.8rem; text-transform: uppercase; background: #F9FAFB; }
    .custom-table td { padding: 16px; border-bottom: 1px solid #F3F4F6; color: #374151; font-size: 0.95rem; }
    .table-container { overflow-x: auto; }
    
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .status-pending { background: #FEF3C7; color: #D97706; }
    .status-approved { background: #D1FAE5; color: #059669; }
    .status-rejected { background: #FEE2E2; color: #DC2626; }

    @media (max-width: 1024px) { .finance-grid { grid-template-columns: 1fr; } .stats-grid { grid-template-columns: 1fr 1fr !important; } }
    @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr !important; } }
</style>

{{-- Header --}}
<div style="background: linear-gradient(135deg, #4B5563 0%, #1F2937 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; margin-bottom: 6px;">Financial Overview</div>
            <div style="opacity: 0.9; font-size: 0.95rem;">
                <i class="fas fa-user-shield"></i> Secretary View (Restricted Access)
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="action-btn btn-green" onclick="openModal('revenueModal')">
                <i class="fas fa-plus-circle"></i> Record Revenue
            </button>
            <button class="action-btn btn-blue" onclick="openModal('expenseModal')">
                <i class="fas fa-paper-plane"></i> Request Expense
            </button>
            <button class="action-btn btn-purple" onclick="openModal('exportModal')">
                <i class="fas fa-file-export"></i> Export Data
            </button>
        </div>
    </div>
</div>

{{-- Top Stats Cards --}}
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px;">
    {{-- Annual Budget --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin: 0;">Annual Budget</p>
            <h3 style="font-size: 1.75rem; margin: 5px 0 0 0; color: #111827;">₱{{ number_format($annualBudget) }}</h3>
        </div>
        <div style="margin-top: 15px; color: #3B82F6; display: flex; justify-content: space-between;">
            <span style="font-size: 0.8rem; background: #EFF6FF; padding: 4px 8px; border-radius: 6px;">FY {{ date('Y') }}</span>
        </div>
    </div>

    {{-- Total Spent --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin: 0;">Total Expenses</p>
            <h3 style="font-size: 1.75rem; margin: 5px 0 0 0; color: #111827;">₱{{ number_format($totalSpent) }}</h3>
        </div>
        <div style="margin-top: 15px;">
            <span style="font-size: 0.85rem; color: #DC2626; background: #FEF2F2; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                {{ $annualBudget > 0 ? number_format(($totalSpent / $annualBudget) * 100, 1) : 0 }}% Used
            </span>
        </div>
    </div>

    {{-- Available --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin: 0;">Available Funds</p>
            <h3 style="font-size: 1.75rem; margin: 5px 0 0 0; color: #10B981;">₱{{ number_format($availableBudget) }}</h3>
        </div>
    </div>

    {{-- Revenue --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin: 0;">Total Collections</p>
            <h3 style="font-size: 1.75rem; margin: 5px 0 0 0; color: #111827;">₱{{ number_format($totalRevenue) }}</h3>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="finance-grid">
    {{-- Budget Utilization --}}
    <div class="finance-card">
        <div class="finance-header">
            <h3><i class="fas fa-chart-pie text-primary me-2"></i> Budget Utilization</h3>
        </div>
        <div class="scrollable-list">
            @foreach($utilization as $item)
            <div class="progress-item">
                <div class="progress-labels">
                    <span>{{ $item['name'] }}</span>
                    <span>₱{{ number_format($item['spent']) }} / ₱{{ number_format($item['limit']) }}</span>
                </div>
                <div class="progress-bg">
                    <div class="progress-fill" style="width: {{ min($item['percentage'], 100) }}%; background-color: {{ $item['percentage'] > 90 ? '#EF4444' : '#3B82F6' }}"></div>
                </div>
                <div style="text-align: right; font-size: 0.75rem; color: #6B7280; margin-top: 4px;">
                    {{ number_format($item['percentage'], 1) }}% Utilized
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Revenue Performance --}}
    <div class="finance-card">
        <div class="finance-header">
            <h3><i class="fas fa-chart-bar text-success me-2"></i> Revenue Sources</h3>
        </div>
        <div class="scrollable-list">
            @foreach($revenuePerformance as $item)
            <div class="progress-item">
                <div class="progress-labels">
                    <span>{{ $item['name'] }}</span>
                    <span style="color: #10B981;">₱{{ number_format($item['collected']) }}</span>
                </div>
                <div class="progress-bg">
                    <div class="progress-fill" style="width: {{ min($item['percentage'], 100) }}%; background-color: #10B981;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size: 0.75rem; color: #6B7280; margin-top: 4px;">
                    <span>Target: ₱{{ number_format($item['target']) }}</span>
                    <span>{{ number_format($item['percentage'], 0) }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Filters --}}
<form action="{{ route('secretary.financial-management') }}" method="GET" style="margin-bottom: 24px; background: white; padding: 20px; border-radius: 12px; display: flex; gap: 15px; align-items: center;">
    <span style="font-weight: 600; color: #374151;"><i class="fas fa-filter text-muted"></i> Filters:</span>
    <select name="type" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #D1D5DB;">
        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Transactions</option>
        <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>Revenue Only</option>
        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expenses Only</option>
    </select>
</form>

{{-- Table --}}
<div class="finance-card" style="padding: 0; overflow: hidden;">
    <div class="finance-header" style="padding: 20px 24px; border-bottom: 1px solid #E5E7EB; margin-bottom: 0;">
        <h3><i class="fas fa-history text-secondary me-2"></i> Transaction History</h3>
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Requested By</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td style="font-weight: 600;">{{ $transaction->title }}</td>
                    <td><span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">{{ $transaction->category }}</span></td>
                    <td>{{ $transaction->requested_by }}</td>
                    <td style="font-weight: 600; {{ $transaction->type == 'revenue' ? 'color:#059669' : 'color:#DC2626' }}">
                        {{ $transaction->type == 'revenue' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                    <td>
                        @if($transaction->status == 'approved')
                            <span class="status-badge status-approved">Approved</span>
                        @elseif($transaction->status == 'rejected')
                            <span class="status-badge status-rejected">Rejected</span>
                        @else
                            <span class="status-badge status-pending">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding: 20px; color: #666;">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- MODALS --}}

{{-- 1. Add Revenue (Direct Approval) --}}
<div id="revenueModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; width:450px; margin: 50px auto; padding:25px; border-radius:16px;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Record Revenue</h3>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Use this to record walk-in payments or manual collections.</p>
        <form action="{{ route('secretary.financial.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="revenue">
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Source/Title</label>
                <input type="text" name="title" class="form-control" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Category</label>
                <select name="category" class="form-control" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                    <option>Barangay Clearance</option>
                    <option>Business Permits</option>
                    <option>Community Tax</option>
                    <option>Other Fees</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Amount (₱)</label>
                <input type="number" name="amount" step="0.01" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:20px;">
                 <label style="display:block; font-weight:600; margin-bottom:5px;">Date</label>
                 <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="closeModal('revenueModal')" class="action-btn btn-secondary">Cancel</button>
                <button type="submit" class="action-btn btn-green">Save Record</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Request Expense (Submission only) --}}
<div id="expenseModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; width:450px; margin: 50px auto; padding:25px; border-radius:16px;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Request Expense</h3>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">This request will be sent to the Treasurer/Captain for approval.</p>
        <form action="{{ route('secretary.financial.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="expense">
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Description</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Office Supplies" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Category</label>
                <select name="category" class="form-control" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                    <option>Office Supplies</option>
                    <option>Utilities</option>
                    <option>Education</option>
                    <option>Health Programs</option>
                    <option>Others</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Amount Needed (₱)</label>
                <input type="number" name="amount" step="0.01" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:20px;">
                 <label style="display:block; font-weight:600; margin-bottom:5px;">Date Needed</label>
                 <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="closeModal('expenseModal')" class="action-btn btn-secondary">Cancel</button>
                <button type="submit" class="action-btn btn-blue">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Export Modal --}}
<div id="exportModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:400px; margin: 100px auto; padding:25px; border-radius:16px;">
        <h3 style="margin-top: 0; margin-bottom: 20px;">Export Data</h3>
        <p style="color: #666; font-size: 0.9rem;">Download financial records.</p>
        <a href="{{ route('secretary.financial.export', ['report_type' => 'transactions']) }}" class="action-btn btn-purple" style="display:block; text-align:center; margin-bottom:10px;">
            <i class="fas fa-list"></i> Download Transaction History (CSV)
        </a>
        <div style="text-align:right; margin-top: 20px;">
            <button type="button" onclick="closeModal('exportModal')" class="action-btn btn-secondary">Close</button>
        </div>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'block'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    window.onclick = function(event) {
        if (event.target.id == 'revenueModal') closeModal('revenueModal');
        if (event.target.id == 'expenseModal') closeModal('expenseModal');
        if (event.target.id == 'exportModal') closeModal('exportModal');
    }
</script>
@endsection