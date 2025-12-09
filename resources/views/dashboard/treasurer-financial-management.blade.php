@extends('layouts.dashboard-layout')

@section('title', 'Financial Management')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('treasurer.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('treasurer.financial') }}" class="nav-link active">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('treasurer.announcements.index') }}" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* Specific styles for Financial page */
    .finance-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; /* 2/3 for utilization, 1/3 for revenue */
        gap: 24px;
        margin-bottom: 30px;
    }
    .finance-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        height: 100%; /* Ensure equal height in grid */
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .finance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F3F4F6;
    }
    .finance-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1F2937;
    }
    
    /* Scrollable content for lists that might grow */
    .scrollable-list {
        flex: 1;
        overflow-y: auto;
        max-height: 350px; 
        padding-right: 5px; 
    }
    .scrollable-list::-webkit-scrollbar { width: 5px; }
    .scrollable-list::-webkit-scrollbar-track { background: #f1f1f1; }
    .scrollable-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }

    .progress-item { margin-bottom: 16px; }
    .progress-labels {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4B5563;
    }
    .progress-bg {
        width: 100%;
        height: 8px;
        background: #F3F4F6;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: #2B5CE6;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    
    /* Buttons */
    .action-btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
        font-weight: 500;
    }
    .action-btn:hover { opacity: 0.9; color: white; }
    .btn-blue { background: #3B82F6; }
    .btn-green { background: #10B981; }
    .btn-purple { background: #8B5CF6; }
    .btn-orange { background: #F59E0B; }
    .btn-secondary { background: #6B7280; color: white; border: none; padding: 8px 16px; border-radius: 6px; }
    .btn-primary { background: #3B82F6; color: white; border: none; padding: 8px 16px; border-radius: 6px; }
    .btn-danger { background: #EF4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
    .btn-icon-only { padding: 6px 8px; }
    
    /* Table Styling */
    .table-container { overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .custom-table th { 
        text-align: left; 
        padding: 14px 16px; 
        border-bottom: 2px solid #E5E7EB; 
        color: #6B7280; 
        font-size: 0.8rem; 
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #F9FAFB;
    }
    .custom-table td { padding: 16px; border-bottom: 1px solid #F3F4F6; color: #374151; font-size: 0.95rem; }
    .custom-table tr:hover { background: #F9FAFB; }
    
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .status-pending { background: #FEF3C7; color: #D97706; }
    .status-approved { background: #D1FAE5; color: #059669; }
    .status-rejected { background: #FEE2E2; color: #DC2626; }
    
    .filter-bar { 
        display: flex; 
        gap: 15px; 
        margin-bottom: 24px; 
        background: white; 
        padding: 20px; 
        border-radius: 12px; 
        align-items: center; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        flex-wrap: wrap;
    }
    .filter-input { padding: 10px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.9rem; outline: none; }
    .filter-input:focus { border-color: #3B82F6; }

    .edit-budget-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: transparent;
        border: 1px solid #E5E7EB;
        color: #6B7280;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .edit-budget-btn:hover { background: #F3F4F6; color: #3B82F6; border-color: #3B82F6; }

    @media (max-width: 1024px) {
        .finance-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr !important; }
        .header-section { text-align: center; justify-content: center; }
        .header-section > div { justify-content: center; }
    }
</style>

{{-- Header --}}
<div class="header-section" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; margin-bottom: 6px;">Financial Records</div>
            <div style="opacity: 0.9; font-size: 0.95rem;">
                <i class="fas fa-sync-alt"></i> Data synchronized with Document Services
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="action-btn btn-green" onclick="openModal('revenueModal')">
                <i class="fas fa-plus-circle"></i> Record Revenue
            </button>
            <button class="action-btn btn-blue" onclick="openModal('expenseModal')">
                <i class="fas fa-file-invoice-dollar"></i> Record Expense
            </button>
            <button class="action-btn btn-purple" onclick="openModal('exportModal')">
                <i class="fas fa-file-export"></i> Export Reports
            </button>
        </div>
    </div>
</div>

{{-- Top Stats Cards (4 Columns) --}}
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px;">
    
    {{-- Total Budget --}}
    <div class="finance-card" style="height: auto;">
        <button class="edit-budget-btn" onclick="openModal('budgetModal')" title="Edit Budget">
            <i class="fas fa-pen" style="font-size: 0.8rem;"></i>
        </button>
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; margin: 0 0 5px 0; text-transform: uppercase;">Annual Budget</p>
            <h3 style="font-size: 1.75rem; margin: 0; color: #111827;">₱{{ number_format($annualBudget) }}</h3>
        </div>
        <div style="margin-top: 15px; color: #3B82F6; display: flex; justify-content: space-between; align-items: flex-end;">
            <span style="font-size: 0.8rem; background: #EFF6FF; padding: 4px 8px; border-radius: 6px;">FY {{ date('Y') }}</span>
            <i class="fas fa-wallet fa-2x" style="opacity: 0.2;"></i>
        </div>
    </div>

    {{-- Total Spent --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; margin: 0 0 5px 0; text-transform: uppercase;">Total Expenses</p>
            <h3 style="font-size: 1.75rem; margin: 0; color: #111827;">₱{{ number_format($totalSpent) }}</h3>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: flex-end;">
            <span style="font-size: 0.85rem; color: #DC2626; background: #FEF2F2; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                {{ $annualBudget > 0 ? number_format(($totalSpent / $annualBudget) * 100, 1) : 0 }}% Used
            </span>
            <i class="fas fa-receipt fa-2x text-warning" style="opacity: 0.2;"></i>
        </div>
    </div>

    {{-- Available Budget --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; margin: 0 0 5px 0; text-transform: uppercase;">Available Balance</p>
            <h3 style="font-size: 1.75rem; margin: 0; color: #10B981;">₱{{ number_format($availableBudget) }}</h3>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: flex-end;">
            <span style="font-size: 0.8rem; color: #059669;">Cash on Hand</span>
            <i class="fas fa-money-bill-wave fa-2x text-success" style="opacity: 0.2;"></i>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="finance-card" style="height: auto;">
        <div class="stat-info">
            <p style="color: #6B7280; font-size: 0.85rem; font-weight: 600; margin: 0 0 5px 0; text-transform: uppercase;">Total Collections</p>
            <h3 style="font-size: 1.75rem; margin: 0; color: #111827;">₱{{ number_format($totalRevenue) }}</h3>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: flex-end;">
            <span style="font-size: 0.8rem; color: #6B7280;">Includes Docs</span>
            <i class="fas fa-chart-line fa-2x text-purple" style="opacity: 0.2; color: #8B5CF6;"></i>
        </div>
    </div>
</div>

{{-- Charts Grid --}}
<div class="finance-grid">
    
    {{-- Budget Utilization --}}
    <div class="finance-card">
        <div class="finance-header">
            <h3><i class="fas fa-chart-pie text-primary me-2"></i> Budget Allocation & Utilization</h3>
        </div>
        <div class="scrollable-list">
            @foreach($utilization as $item)
            <div class="progress-item">
                <div class="progress-labels">
                    <span>{{ $item['name'] }}</span>
                    <span>₱{{ number_format($item['spent']) }} <span style="color:#9CA3AF; font-weight:400;">/ ₱{{ number_format($item['limit']) }}</span></span>
                </div>
                <div class="progress-bg">
                    <div class="progress-fill" style="width: {{ min($item['percentage'], 100) }}%; background-color: {{ $item['percentage'] > 90 ? '#EF4444' : ($item['percentage'] > 70 ? '#F59E0B' : '#3B82F6') }}"></div>
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
            <h3><i class="fas fa-chart-bar text-success me-2"></i> Collection Sources</h3>
            <span style="font-size: 0.75rem; background: #ECFDF5; color: #065F46; padding: 2px 8px; border-radius: 10px;">Live Data</span>
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

{{-- PENDING APPROVALS SECTION (NEW FEATURE FOR SECRETARY REQUESTS) --}}
@if($pendingRequests->count() > 0)
<div class="finance-card" style="margin-bottom: 24px; border: 1px solid #F59E0B;">
    <div class="finance-header" style="background: #FFFBEB; margin: -24px -24px 20px -24px; padding: 15px 24px; border-bottom: 1px solid #FDE68A; border-radius: 12px 12px 0 0;">
        <h3 style="color: #92400E;"><i class="fas fa-exclamation-circle me-2"></i> Pending Approvals</h3>
        <span style="background: #F59E0B; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem; font-weight: bold;">Action Required</span>
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Requested By</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingRequests as $req)
                <tr>
                    <td style="font-weight: 600;">{{ $req->title }}</td>
                    <td>{{ $req->category }}</td>
                    <td>{{ $req->requested_by }}</td>
                    <td style="color: #DC2626; font-weight: 600;">₱{{ number_format($req->amount, 2) }}</td>
                    <td>{{ $req->transaction_date->format('M d') }}</td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            {{-- Approve Button --}}
                            <form action="{{ route('treasurer.transaction.updateStatus', $req->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="action-btn btn-green btn-sm">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>

                            {{-- Reject Button --}}
                            <form action="{{ route('treasurer.transaction.updateStatus', $req->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="action-btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Filter Bar --}}
<form action="{{ route('treasurer.financial') }}" method="GET" class="filter-bar">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-weight: 600; color: #374151; font-size: 0.95rem;"><i class="fas fa-filter text-muted"></i> Filters:</span>
    </div>
    
    <select name="type" class="filter-input" onchange="this.form.submit()">
        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Transaction Types</option>
        <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>Revenue Only</option>
        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expenses Only</option>
    </select>
    
    <input type="month" name="month" class="filter-input" value="{{ request('month') }}" onchange="this.form.submit()">
    
    @if(request('type') || request('month'))
        <a href="{{ route('treasurer.financial') }}" style="color: #EF4444; font-size: 0.9rem; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 5px;">
            <i class="fas fa-times"></i> Clear
        </a>
    @endif
</form>

{{-- Transaction History Table --}}
<div class="finance-card" style="padding: 0; overflow: hidden;">
    <div class="finance-header" style="padding: 20px 24px; border-bottom: 1px solid #E5E7EB; margin-bottom: 0;">
        <h3><i class="fas fa-history text-secondary me-2"></i> Recent Transactions</h3>
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Recorded By</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr>
                    <td style="font-weight: 600; color: #111827;">{{ $transaction->title }}</td>
                    <td>
                        <span style="background: #F3F4F6; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; color: #4B5563;">
                            {{ $transaction->category }}
                        </span>
                    </td>
                    <td>{{ $transaction->requested_by ?? 'System' }}</td>
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
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <button onclick="editTransaction({{ json_encode($transaction) }})" class="action-btn btn-blue btn-icon-only" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('treasurer.transaction.destroy', $transaction->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-secondary btn-icon-only" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px; color: #6B7280;">
                        <i class="fas fa-folder-open fa-2x" style="margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                        No transactions found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 20px;">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- 1. Add/Edit Revenue Modal --}}
<div id="revenueModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items: center; justify-content: center;">
    <div style="background:white; width:450px; margin: 50px auto; padding:25px; border-radius:16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <h3 id="revModalTitle" style="margin-top: 0; margin-bottom: 20px; color: #111827;">Record Revenue</h3>
        <form id="revForm" action="{{ route('treasurer.transaction.store') }}" method="POST">
            @csrf
            <div id="revMethod"></div> 
            <input type="hidden" name="type" value="revenue">
            
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Source/Title</label>
                <input type="text" id="revTitle" name="title" class="form-control" placeholder="e.g. Daily Clearance Collection" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Category</label>
                <select id="revCategory" name="category" class="form-control" style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
                    <option>Barangay Clearance</option>
                    <option>Business Permits</option>
                    <option>Community Tax</option>
                    <option>Government IRA</option>
                    <option>Donations</option>
                    <option>Other Fees</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Amount (₱)</label>
                <input type="number" id="revAmount" name="amount" class="form-control" step="0.01" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="margin-bottom:20px;">
                 <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Date Received</label>
                 <input type="date" id="revDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="text-align:right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('revenueModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Revenue</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Add/Edit Expense Modal --}}
<div id="expenseModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:450px; margin: 50px auto; padding:25px; border-radius:16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <h3 id="expModalTitle" style="margin-top: 0; margin-bottom: 20px; color: #111827;">Record Expense</h3>
        <form id="expForm" action="{{ route('treasurer.transaction.store') }}" method="POST">
            @csrf
            <div id="expMethod"></div> 
            <input type="hidden" name="type" value="expense">
            
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Description</label>
                <input type="text" id="expTitle" name="title" class="form-control" placeholder="e.g. Office Supplies" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Category</label>
                <select id="expCategory" name="category" class="form-control" style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
                    <option>Infrastructure</option>
                    <option>Health Programs</option>
                    <option>Education</option>
                    <option>Environmental</option>
                    <option>Emergency Fund</option>
                    <option>Office Supplies</option>
                    <option>Utilities</option>
                    <option>Honorarium</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Amount (₱)</label>
                <input type="number" id="expAmount" name="amount" class="form-control" step="0.01" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="margin-bottom:20px;">
                 <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 6px;">Date Spent</label>
                 <input type="date" id="expDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:8px;">
            </div>
            <div style="text-align:right; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('expenseModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Record</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Export Reports Modal --}}
<div id="exportModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:400px; margin: 100px auto; padding:25px; border-radius:16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #111827;">Export Reports</h3>
        <p style="color: #666; font-size: 0.9rem;">Select the report type and period you want to download.</p>
        <div style="display: grid; gap: 10px;">
            <a href="{{ route('treasurer.export', ['report_type' => 'transactions']) }}" class="action-btn btn-blue" style="justify-content: center; width: 100%;">
                <i class="fas fa-file-pdf"></i> Monthly Financial Statement
            </a>
            <a href="{{ route('treasurer.export', ['report_type' => 'transactions']) }}" class="action-btn btn-green" style="justify-content: center; width: 100%;">
                <i class="fas fa-file-excel"></i> Annual Budget Report
            </a>
            <a href="{{ route('treasurer.export', ['report_type' => 'transactions']) }}" class="action-btn btn-purple" style="justify-content: center; width: 100%;">
                <i class="fas fa-list"></i> Transaction History (CSV)
            </a>
        </div>
        <div style="text-align:right; margin-top: 20px;">
            <button type="button" onclick="closeModal('exportModal')" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>

{{-- 4. Budget Modal (Updated: Dynamic Allocations) --}}
<div id="budgetModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
    <div style="background:white; width:450px; margin: 60px auto; padding:25px; border-radius:16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 85vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #111827;">Update Budget Allocations</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Adjust the annual budget and limits per category.</p>
        
        <form action="{{ route('treasurer.budget.update') }}" method="POST">
            @csrf
            
            {{-- Global Budget --}}
            <div style="margin-bottom:20px; padding-bottom: 20px; border-bottom: 1px solid #E5E7EB;">
                <label style="display: block; font-weight: 700; font-size: 0.95rem; margin-bottom: 6px; color: #111827;">Total Annual Budget</label>
                <div style="display: flex; align-items: center;">
                    <span style="background: #F3F4F6; padding: 10px; border: 1px solid #D1D5DB; border-right: none; border-radius: 8px 0 0 8px; color: #6B7280;">₱</span>
                    <input type="number" name="annual_budget" class="form-control" value="{{ $annualBudget }}" required style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius: 0 8px 8px 0;">
                </div>
            </div>

            {{-- Dynamic Category Inputs --}}
            <h4 style="font-size: 0.9rem; font-weight: 700; color: #4B5563; margin-bottom: 15px;">Category Limits</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                @foreach($utilization as $item)
                    @php
                        $inputName = 'budget_' . strtolower(str_replace(' ', '_', $item['name']));
                    @endphp
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; color: #374151;">{{ $item['name'] }}</label>
                        <input type="number" name="{{ $inputName }}" class="form-control" 
                               value="{{ $item['limit'] }}" 
                               style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px; font-size: 0.9rem;">
                    </div>
                @endforeach
            </div>

            <div style="text-align:right; margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid #E5E7EB;">
                <button type="button" onclick="closeModal('budgetModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Allocations</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { 
        // Reset form for "Add New"
        if(id === 'revenueModal') {
            document.getElementById('revForm').action = "{{ route('treasurer.transaction.store') }}";
            document.getElementById('revMethod').innerHTML = '';
            document.getElementById('revModalTitle').innerText = 'Record Revenue';
            document.getElementById('revTitle').value = '';
            document.getElementById('revAmount').value = '';
            document.getElementById('revDate').value = "{{ date('Y-m-d') }}";
        }
        if(id === 'expenseModal') {
            document.getElementById('expForm').action = "{{ route('treasurer.transaction.store') }}";
            document.getElementById('expMethod').innerHTML = '';
            document.getElementById('expModalTitle').innerText = 'Record Expense';
            document.getElementById('expTitle').value = '';
            document.getElementById('expAmount').value = '';
            document.getElementById('expDate').value = "{{ date('Y-m-d') }}";
        }
        document.getElementById(id).style.display = 'block'; 
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    // JS to populate modal for editing
    function editTransaction(data) {
        let modalId = data.type === 'revenue' ? 'revenueModal' : 'expenseModal';
        let formId = data.type === 'revenue' ? 'revForm' : 'expForm';
        let prefix = data.type === 'revenue' ? 'rev' : 'exp';

        // Update Form Action
        let updateUrl = "{{ route('treasurer.transaction.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', data.id);
        document.getElementById(formId).action = updateUrl;

        // Add Hidden PUT Method
        document.getElementById(prefix + 'Method').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Update Title
        document.getElementById(prefix + 'ModalTitle').innerText = 'Edit ' + (data.type === 'revenue' ? 'Revenue' : 'Expense');

        // Populate Fields
        document.getElementById(prefix + 'Title').value = data.title;
        document.getElementById(prefix + 'Amount').value = data.amount;
        document.getElementById(prefix + 'Category').value = data.category;
        
        // Format Date
        let dateVal = data.transaction_date.substring(0, 10);
        document.getElementById(prefix + 'Date').value = dateVal;

        document.getElementById(modalId).style.display = 'block';
    }

    window.onclick = function(event) {
        if (event.target.id == 'revenueModal') closeModal('revenueModal');
        if (event.target.id == 'expenseModal') closeModal('expenseModal');
        if (event.target.id == 'exportModal') closeModal('exportModal');
        if (event.target.id == 'budgetModal') closeModal('budgetModal');
    }
</script>

@endsection