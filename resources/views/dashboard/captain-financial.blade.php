@extends('layouts.dashboard-layout')

@section('title', 'Financial Management')

@section('nav-items')
    {{-- CAPTAIN NAVIGATION --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link">
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
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- ACTIVE TAB --}}
        <a href="{{ route('captain.financial-management') }}" class="nav-link active">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.announcements.index') }}" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* Shared Finance Styles */
    .finance-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; 
        gap: 24px;
        margin-bottom: 30px;
    }
    .finance-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        height: 100%;
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
    
    .scrollable-list {
        flex: 1;
        overflow-y: auto;
        max-height: 350px; 
        padding-right: 5px; 
    }
    .scrollable-list::-webkit-scrollbar { width: 5px; }
    .scrollable-list::-webkit-scrollbar-track { background: #f1f1f1; }
    .scrollable-list::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }

    /* Progress Bars */
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
    .btn-dark { background: #1F2937; }
    .btn-danger { background: #EF4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; }
    .btn-secondary { background: #6B7280; color: white; border: none; padding: 8px 16px; border-radius: 6px; }
    .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
    
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
<div class="header-section" style="background: linear-gradient(135deg, #111827 0%, #374151 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <div style="font-size: 1.8rem; font-weight: 800; margin-bottom: 6px;">Financial Management</div>
            <div style="opacity: 0.9; font-size: 0.95rem;">
                <i class="fas fa-chart-line"></i> Executive Oversight & Budget Control
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="action-btn btn-green" onclick="openModal('budgetModal')">
                <i class="fas fa-sliders-h"></i> Adjust Budget
            </button>
            <button class="action-btn btn-blue" onclick="openModal('revenueModal')">
                <i class="fas fa-plus-circle"></i> Manual Entry
            </button>
        </div>
    </div>
</div>

{{-- Top Stats Cards --}}
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 30px;">
    
    {{-- Total Budget --}}
    <div class="finance-card" style="height: auto;">
        <button class="edit-budget-btn" onclick="openModal('budgetModal')" title="Adjust Budget">
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
            <h3><i class="fas fa-chart-bar text-success me-2"></i> Revenue Sources</h3>
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

{{-- PENDING APPROVALS SECTION (Priority for Captain) --}}
@if($pendingRequests->count() > 0)
<div class="finance-card" style="margin-bottom: 24px; border: 1px solid #F59E0B;">
    <div class="finance-header" style="background: #FFFBEB; margin: -24px -24px 20px -24px; padding: 15px 24px; border-bottom: 1px solid #FDE68A; border-radius: 12px 12px 0 0;">
        <h3 style="color: #92400E;"><i class="fas fa-exclamation-circle me-2"></i> Expense Approvals Pending</h3>
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
                    <th>Executive Action</th>
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
                            <form action="{{ route('captain.financial.status', $req->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="action-btn btn-green btn-sm">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>

                            {{-- Reject Button --}}
                            <form action="{{ route('captain.financial.status', $req->id) }}" method="POST">
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

{{-- Transaction History Table --}}
<div class="finance-card" style="padding: 0; overflow: hidden;">
    <div class="finance-header" style="padding: 20px 24px; border-bottom: 1px solid #E5E7EB; margin-bottom: 0;">
        <h3><i class="fas fa-history text-secondary me-2"></i> Recent Activity Log</h3>
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
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding: 40px; color: #6B7280;">
                        No transactions found.
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

{{-- MODALS --}}

{{-- 1. Budget Settings Modal (Main Captain Feature) --}}
<div id="budgetModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; width:500px; margin: 50px auto; padding:25px; border-radius:12px;">
        <h3 style="margin-top:0;">Adjust Fiscal Budget</h3>
        <p style="color: #6B7280; font-size: 0.9rem;">Changes here will reflect across the entire system immediately.</p>
        
        <form action="{{ route('captain.financial.budget.update') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight:600; font-size:0.9rem;">Total Annual Budget</label>
                <input type="number" name="annual_budget" value="{{ $annualBudget }}" class="form-control" style="width:100%; padding:10px; border:1px solid #D1D5DB; border-radius:6px;">
            </div>
            <hr style="border: 0; border-top: 1px solid #E5E7EB; margin: 15px 0;">
            <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom: 10px;">Category Allocations (Limits):</label>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <small>Infrastructure</small>
                    <input type="number" name="budget_infrastructure" placeholder="Limit" style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px;">
                </div>
                <div>
                    <small>Health Programs</small>
                    <input type="number" name="budget_health_programs" placeholder="Limit" style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px;">
                </div>
                <div>
                    <small>Education</small>
                    <input type="number" name="budget_education" placeholder="Limit" style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px;">
                </div>
                <div>
                    <small>Emergency Fund</small>
                    <input type="number" name="budget_emergency_fund" placeholder="Limit" style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px;">
                </div>
                <div>
                    <small>Environmental</small>
                    <input type="number" name="budget_environmental" placeholder="Limit" style="width:100%; padding:8px; border:1px solid #D1D5DB; border-radius:6px;">
                </div>
            </div>

            <div style="text-align:right; margin-top:20px;">
                <button type="button" onclick="closeModal('budgetModal')" class="action-btn btn-secondary">Cancel</button>
                <button type="submit" class="action-btn btn-dark">Save Adjustments</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Manual Revenue Entry (Optional for Captain) --}}
<div id="revenueModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; width:400px; margin: 100px auto; padding:20px; border-radius:12px;">
        <h3>Add Revenue</h3>
        <p style="color:#666; font-size:0.9rem;">Manual entry of funds.</p>
        <form action="{{ route('captain.financial.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="revenue">
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:0.9rem;">Source/Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Donation" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:0.9rem;">Category</label>
                <select name="category" class="form-control" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
                    <option>Donations</option>
                    <option>Government IRA</option>
                    <option>Other Fees</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; font-weight:600; font-size:0.9rem;">Amount (₱)</label>
                <input type="number" name="amount" class="form-control" step="0.01" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
            </div>
            <div style="text-align:right;">
                <button type="button" onclick="closeModal('revenueModal')" class="action-btn btn-secondary">Cancel</button>
                <button type="submit" class="action-btn btn-green">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    window.onclick = function(event) {
        if (event.target.id == 'budgetModal') closeModal('budgetModal');
        if (event.target.id == 'revenueModal') closeModal('revenueModal');
    }
</script>

@endsection