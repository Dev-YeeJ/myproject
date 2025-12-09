@extends('layouts.dashboard-layout')

@section('title', 'Financial Management')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.resident-profiling') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new document services route --}}
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.financial') }}" class="nav-link active {{ request()->routeIs('captain.financial*') ? 'active' : '' }}">
        <i class="fas fa-dollar-sign"></i>
        <span>Financial Management</span>
    </a>
</li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new health services route --}}
        <a href="{{ route('captain.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.incident.index') }}" class="nav-link {{ request()->routeIs('captain.incident.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident & Blotter</span>
    </a>
</li>
    <li class="nav-item">
        <a href="{{ route('captain.project.monitoring') }}" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.announcements.index') }}" class="nav-link {{ request()->routeIs('captain.announcements.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>Announcements</span>
    </a>
</li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-check-circle"></i>
            <span>SK Module</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- Base styles from Resident Profiling --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 40px; border-radius: 16px;
        margin-bottom: 30px; position: relative;
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }
    .total-registered {
        position: absolute; top: 40px; right: 40px; text-align: right;
    }
    .total-registered-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .total-registered-count { font-size: 2.5rem; font-weight: 700; }
    .total-registered-sublabel { font-size: 0.85rem; opacity: 0.9; }

    /* Stats Grid */
    .stats-row {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 20px; margin-bottom: 30px;
    }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
        position: relative;
    }
    .stat-content h3 { font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; color: #1F2937; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; font-weight: 600; }
    
    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }
    .stat-badge.purple { color: #A855F7; }

    .stat-box-icon {
        width: 60px; height: 60px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; color: white;
    }
    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-purple-bg { background: #A855F7; }

    /* Action Buttons */
    .action-buttons { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
    .btn-action {
        padding: 12px 24px; border-radius: 10px; border: none;
        font-weight: 600; display: flex; align-items: center;
        gap: 10px; cursor: pointer; transition: all 0.3s;
        font-size: 0.95rem; text-decoration: none;
    }
    .btn-revenue { background: #10B981; color: white; } /* Green */
    .btn-revenue:hover { background: #059669; color: white; transform: translateY(-2px); }
    
    .btn-expense { background: #EF4444; color: white; } /* Red */
    .btn-expense:hover { background: #DC2626; color: white; transform: translateY(-2px); }
    
    .btn-sync { background: #0D9488; color: white; } /* Teal */
    .btn-sync:hover { background: #0F766E; color: white; transform: translateY(-2px); }

    .btn-export { background: white; color: #374151; border: 2px solid #E5E7EB; }
    .btn-export:hover { border-color: #2B5CE6; color: #2B5CE6; transform: translateY(-2px); }

    /* Directory Header Style for Tables */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .directory-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 700; }
    
    .filters-section { display: flex; align-items: center; gap: 10px; }
    .filter-select {
        padding: 8px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.9rem; background: white; color: #374151;
        outline: none; cursor: pointer;
    }

    /* Table Styles */
    .table-container {
        background: white; border-radius: 0 0 12px 12px;
        overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        margin-bottom: 30px;
    }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th {
        padding: 16px 20px; font-weight: 700; color: #1F2937;
        font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;
        text-align: left; border-bottom: 2px solid #E5E7EB; background: #F9FAFB;
    }
    .custom-table td {
        padding: 16px 20px; vertical-align: middle;
        border-bottom: 1px solid #F3F4F6; text-align: left; font-size: 0.95rem;
    }
    .custom-table tbody tr:hover { background: #F9FAFB; }

    /* Financial Specific Styles */
    .finance-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 30px; }
    .finance-card {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07); height: 100%;
        display: flex; flex-direction: column;
    }
    .finance-card-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6;
    }
    .finance-card-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1F2937; }

    /* Progress Bars */
    .progress-item { margin-bottom: 16px; }
    .progress-labels { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.85rem; font-weight: 600; color: #4B5563; }
    .progress-bg { width: 100%; height: 8px; background: #F3F4F6; border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; background: #2B5CE6; border-radius: 4px; transition: width 0.5s ease; }
    
    /* Edit Button on Stat Box */
    .edit-budget-btn {
        position: absolute; top: 10px; right: 10px;
        background: transparent; border: none; color: #9CA3AF;
        cursor: pointer; transition: color 0.2s;
    }
    .edit-budget-btn:hover { color: #2B5CE6; }

    /* Badges */
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
    .status-approved { background: #D1FAE5; color: #065F46; }
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-rejected { background: #FEE2E2; color: #991B1B; }

    /* Pending Approval Box */
    .pending-box {
        border: 1px solid #FCD34D; background: #FFFBEB;
        border-radius: 12px; overflow: hidden; margin-bottom: 30px;
    }
    .pending-header {
        background: #FDE68A; color: #92400E; padding: 15px 20px;
        font-weight: 700; display: flex; justify-content: space-between; align-items: center;
    }

    /* Modal Styles (Matches Profiling) */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 30px; border-radius: 12px; max-width: 450px; width: 90%; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.95rem; margin-top: 5px; }
    
    @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { 
        .stats-row { grid-template-columns: 1fr; } 
        .finance-grid { grid-template-columns: 1fr; }
        .total-registered { position: static; margin-top: 20px; text-align: left; }
    }
</style>

{{-- Notifications --}}
@if(session('success'))
<div class="alert alert-success" style="background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #6EE7B7; display: flex; align-items: center; gap: 10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- 1. Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Financial Management</div>
    <div class="profiling-subtitle">Track barangay revenues, expenses, and budget allocations.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
    </div>
    <div class="total-registered">
        <div class="total-registered-label">Available Balance</div>
        <div class="total-registered-count">₱{{ number_format($availableBudget) }}</div>
        <div class="total-registered-sublabel">Cash on Hand</div>
    </div>
</div>

{{-- 2. Stats Row --}}
<div class="stats-row">
    {{-- Annual Budget --}}
    <div class="stat-box">
        <button class="edit-budget-btn" onclick="openModal('budgetModal')" title="Edit Budget">
            <i class="fas fa-pen"></i>
        </button>
        <div class="stat-content">
            <h3>₱{{ number_format($annualBudget) }}</h3>
            <p>Annual Budget</p>
            <div class="stat-badge blue">
                <i class="fas fa-calendar-alt"></i>
                <span>FY {{ date('Y') }}</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-wallet"></i>
        </div>
    </div>

    {{-- Total Expenses --}}
    <div class="stat-box">
        <div class="stat-content">
            <h3>₱{{ number_format($totalSpent) }}</h3>
            <p>Total Expenses</p>
            <div class="stat-badge orange">
                <i class="fas fa-chart-pie"></i>
                <span>{{ $annualBudget > 0 ? number_format(($totalSpent / $annualBudget) * 100, 1) : 0 }}% Used</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="stat-box">
        <div class="stat-content">
            <h3>₱{{ number_format($totalRevenue) }}</h3>
            <p>Total Collections</p>
            <div class="stat-badge purple">
                <i class="fas fa-arrow-up"></i>
                <span>Includes Docs</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
    </div>

    {{-- Transaction Count (New Stat for balance layout) --}}
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $transactions->total() }}</h3>
            <p>Transactions</p>
            <div class="stat-badge green">
                <i class="fas fa-check"></i>
                <span>Recorded</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-list-ol"></i>
        </div>
    </div>
</div>

{{-- 3. Action Buttons --}}
<div class="action-buttons">
    <button class="btn-action btn-revenue" onclick="openModal('revenueModal')">
        <i class="fas fa-plus-circle"></i>
        <span>Record Revenue</span>
    </button>
    <button class="btn-action btn-expense" onclick="openModal('expenseModal')">
        <i class="fas fa-minus-circle"></i>
        <span>Record Expense</span>
    </button>
    <a href="{{ route('captain.financial.sync') }}" class="btn-action btn-sync">
        <i class="fas fa-sync-alt"></i>
        <span>Sync Docs</span>
    </a>
    <button class="btn-action btn-export" onclick="openModal('exportModal')">
        <i class="fas fa-file-download"></i>
        <span>Export Reports</span>
    </button>
</div>

{{-- 4. Pending Approvals (If Any) --}}
@if($pendingRequests->count() > 0)
<div class="pending-box">
    <div class="pending-header">
        <span><i class="fas fa-exclamation-circle"></i> Pending Approvals</span>
        <span style="background: white; color: #92400E; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem;">Action Required</span>
    </div>
    <div class="table-container" style="margin-bottom: 0; border-radius: 0;">
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
                    <td style="color: #DC2626; font-weight: 700;">₱{{ number_format($req->amount, 2) }}</td>
                    <td>{{ $req->transaction_date->format('M d') }}</td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <form action="{{ route('captain.transaction.updateStatus', $req->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn-action btn-revenue" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('captain.transaction.updateStatus', $req->id) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn-action btn-expense" style="padding: 6px 12px; font-size: 0.8rem;">
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

{{-- 5. Charts / Progress Bars --}}
<div class="finance-grid">
    {{-- Budget Utilization --}}
    <div class="finance-card">
        <div class="finance-card-header">
            <h3><i class="fas fa-chart-pie text-primary me-2"></i> Budget Utilization</h3>
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

    {{-- Revenue Sources --}}
    <div class="finance-card">
        <div class="finance-card-header">
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

{{-- 6. Recent Transactions Table (Styled like Directory) --}}
<div class="directory-header">
    <div class="directory-title">
        <i class="fas fa-history"></i>
        <span>Transaction History</span>
    </div>
    <form action="{{ route('captain.financial') }}" method="GET" class="filters-section">
        <select name="type" class="filter-select" onchange="this.form.submit()">
            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
            <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>Revenue</option>
            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
        </select>
        <input type="month" name="month" class="filter-select" value="{{ request('month') }}" onchange="this.form.submit()">
    </form>
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
                    @if($transaction->project_id)
                        <br><a href="{{ route('captain.project.monitoring') }}" style="font-size: 0.75rem; color: #2563EB; text-decoration: none;"><i class="fas fa-link"></i> {{ $transaction->project->title }}</a>
                    @endif
                </td>
                <td>{{ $transaction->requested_by ?? 'System' }}</td>
                <td style="font-weight: 700; {{ $transaction->type == 'revenue' ? 'color:#059669' : 'color:#DC2626' }}">
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
                    <div style="display: flex; gap: 8px;">
                        <button onclick="editTransaction({{ json_encode($transaction) }})" style="border:none; background:none; color: #2B5CE6; cursor: pointer;" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('captain.transaction.destroy', $transaction->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this transaction record?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:none; background:none; color: #EF4444; cursor: pointer;" title="Delete">
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
                    No transactions found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 20px;">
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- MODALS (Kept functional logic, updated styles) --}}

{{-- 1. Revenue Modal --}}
<div id="revenueModal" class="modal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; margin-bottom: 20px;">
            <h3 id="revModalTitle" style="margin:0; color:#1F2937;">Record Revenue</h3>
            <span style="cursor:pointer;" onclick="closeModal('revenueModal')">&times;</span>
        </div>
        <form id="revForm" action="{{ route('captain.transaction.store') }}" method="POST">
            @csrf
            <div id="revMethod"></div> 
            <input type="hidden" name="type" value="revenue">
            
            <label style="font-weight:600; font-size:0.9rem;">Source/Title</label>
            <input type="text" id="revTitle" name="title" class="form-control" placeholder="e.g. Daily Clearance Collection" required>
            
            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Category</label>
            <select id="revCategory" name="category" class="form-control">
                <option>Barangay Clearance</option>
                <option>Business Permits</option>
                <option>Community Tax</option>
                <option>Government IRA</option>
                <option>Donations</option>
                <option>Other Fees</option>
            </select>

            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Amount (₱)</label>
            <input type="number" id="revAmount" name="amount" class="form-control" step="0.01" required>

            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Date Received</label>
            <input type="date" id="revDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>

            <div style="text-align:right; margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('revenueModal')" class="btn-action btn-export" style="border:none; background:#F3F4F6;">Cancel</button>
                <button type="submit" class="btn-action btn-revenue">Save Revenue</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Expense Modal --}}
<div id="expenseModal" class="modal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; margin-bottom: 20px;">
            <h3 id="expModalTitle" style="margin:0; color:#1F2937;">Record Expense</h3>
            <span style="cursor:pointer;" onclick="closeModal('expenseModal')">&times;</span>
        </div>
        <form id="expForm" action="{{ route('captain.transaction.store') }}" method="POST">
            @csrf
            <div id="expMethod"></div> 
            <input type="hidden" name="type" value="expense">
            
            <label style="font-weight:600; font-size:0.9rem;">Description</label>
            <input type="text" id="expTitle" name="title" class="form-control" placeholder="e.g. Office Supplies" required>
            
            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Category</label>
            <select id="expCategory" name="category" class="form-control">
                <option>Infrastructure</option>
                <option>Health Programs</option>
                <option>Education</option>
                <option>Environmental</option>
                <option>Social Services</option>
                <option>Emergency Fund</option>
                <option>Office Supplies</option>
                <option>Utilities</option>
                <option>Honorarium</option>
                <option>Others</option>
            </select>

            <div style="margin-top:15px; background: #F9FAFB; padding: 10px; border-radius: 8px; border: 1px dashed #D1D5DB;">
                <label style="font-weight:600; font-size:0.85rem; color:#4B5563;"><i class="fas fa-link"></i> Link to Project (Optional)</label>
                <select name="project_id" id="expProject" class="form-control" style="margin-top:5px;">
                    <option value="">-- None --</option>
                    @if(isset($activeProjects) && count($activeProjects) > 0)
                        @foreach($activeProjects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Amount (₱)</label>
            <input type="number" id="expAmount" name="amount" class="form-control" step="0.01" required>

            <label style="font-weight:600; font-size:0.9rem; margin-top:15px; display:block;">Date Spent</label>
            <input type="date" id="expDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>

            <div style="text-align:right; margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeModal('expenseModal')" class="btn-action btn-export" style="border:none; background:#F3F4F6;">Cancel</button>
                <button type="submit" class="btn-action btn-expense">Save Expense</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Export Modal --}}
<div id="exportModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #111827;">Export Reports</h3>
        <div style="display: grid; gap: 10px;">
            <a href="{{ route('captain.export', ['report_type' => 'transactions']) }}" class="btn-action btn-export" style="justify-content: center;">
                <i class="fas fa-file-pdf"></i> Monthly Financial Statement
            </a>
            <a href="{{ route('captain.export', ['report_type' => 'transactions']) }}" class="btn-action btn-export" style="justify-content: center;">
                <i class="fas fa-file-excel"></i> Annual Budget Report
            </a>
            <a href="{{ route('captain.export', ['report_type' => 'transactions']) }}" class="btn-action btn-export" style="justify-content: center;">
                <i class="fas fa-list"></i> Transaction History (CSV)
            </a>
        </div>
        <div style="text-align:right; margin-top: 20px;">
            <button type="button" onclick="closeModal('exportModal')" class="btn-action" style="background:#F3F4F6; color:#374151;">Close</button>
        </div>
    </div>
</div>

{{-- 4. Budget Modal --}}
<div id="budgetModal" class="modal">
    <div class="modal-content" style="max-height: 85vh; overflow-y: auto;">
        <h3 style="margin-top: 0; margin-bottom: 20px; color: #111827;">Update Budget Allocations</h3>
        <form action="{{ route('captain.budget.update') }}" method="POST">
            @csrf
            
            <div style="margin-bottom:20px; padding-bottom: 20px; border-bottom: 1px solid #E5E7EB;">
                <label style="font-weight:700; font-size:0.95rem;">Total Annual Budget</label>
                <div style="display: flex; align-items: center;">
                    <span style="background: #F3F4F6; padding: 10px; border: 1px solid #D1D5DB; border-right: none; border-radius: 8px 0 0 8px; color: #6B7280;">₱</span>
                    <input type="number" name="annual_budget" class="form-control" value="{{ $annualBudget }}" required style="border-radius: 0 8px 8px 0; margin-top:0;">
                </div>
            </div>

            <h4 style="font-size: 0.9rem; font-weight: 700; color: #4B5563; margin-bottom: 15px;">Category Limits</h4>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                @foreach($utilization as $item)
                    @php $inputName = 'budget_' . strtolower(str_replace(' ', '_', $item['name'])); @endphp
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; color: #374151;">{{ $item['name'] }}</label>
                        <input type="number" name="{{ $inputName }}" class="form-control" value="{{ $item['limit'] }}">
                    </div>
                @endforeach
            </div>

            <div style="text-align:right; margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid #E5E7EB;">
                <button type="button" onclick="closeModal('budgetModal')" class="btn-action btn-export" style="border:none; background:#F3F4F6;">Cancel</button>
                <button type="submit" class="btn-action btn-revenue" style="background:#2B5CE6;">Save Allocations</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { 
        if(id === 'revenueModal') {
            document.getElementById('revForm').action = "{{ route('captain.transaction.store') }}";
            document.getElementById('revMethod').innerHTML = '';
            document.getElementById('revModalTitle').innerText = 'Record Revenue';
            document.getElementById('revTitle').value = '';
            document.getElementById('revAmount').value = '';
            document.getElementById('revDate').value = "{{ date('Y-m-d') }}";
        }
        if(id === 'expenseModal') {
            document.getElementById('expForm').action = "{{ route('captain.transaction.store') }}";
            document.getElementById('expMethod').innerHTML = '';
            document.getElementById('expModalTitle').innerText = 'Record Expense';
            document.getElementById('expTitle').value = '';
            document.getElementById('expAmount').value = '';
            document.getElementById('expDate').value = "{{ date('Y-m-d') }}";
            if(document.getElementById('expProject')) document.getElementById('expProject').value = "";
        }
        document.getElementById(id).style.display = 'flex'; 
    }

    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    
    function editTransaction(data) {
        let modalId = data.type === 'revenue' ? 'revenueModal' : 'expenseModal';
        let formId = data.type === 'revenue' ? 'revForm' : 'expForm';
        let prefix = data.type === 'revenue' ? 'rev' : 'exp';

        let updateUrl = "{{ route('captain.transaction.update', ':id') }}";
        updateUrl = updateUrl.replace(':id', data.id);
        document.getElementById(formId).action = updateUrl;

        document.getElementById(prefix + 'Method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById(prefix + 'ModalTitle').innerText = 'Edit ' + (data.type === 'revenue' ? 'Revenue' : 'Expense');

        document.getElementById(prefix + 'Title').value = data.title;
        document.getElementById(prefix + 'Amount').value = data.amount;
        document.getElementById(prefix + 'Category').value = data.category;
        
        let dateVal = data.transaction_date.substring(0, 10);
        document.getElementById(prefix + 'Date').value = dateVal;

        if (data.type === 'expense') {
            let projectSelect = document.getElementById('expProject');
            if (projectSelect) {
                projectSelect.value = data.project_id ? data.project_id : "";
            }
        }

        document.getElementById(modalId).style.display = 'flex';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

@endsection