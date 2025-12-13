@extends('layouts.dashboard-layout')

@section('title', 'Treasurer Dashboard')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('treasurer.dashboard') }}" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('treasurer.financial') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('treasurer.announcements.index') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- Main Layout & Header (Matched to Captain Style - Blue Theme) --- */
    .header-section {
        /* UPDATED: Captain's Blue Gradient */
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px;
        /* UPDATED: Captain's shadow */
        box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
    }
    .header-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .header-subtitle { opacity: 0.9; font-size: 1rem; margin-bottom: 20px; }
    
    .barangay-badge {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 500;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .badge-icon {
        /* UPDATED: Matched exact Captain orange hex */
        background: #FFA500; 
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white; font-size: 0.9rem;
    }

    .header-date-block {
        position: absolute; top: 40px; right: 40px; text-align: right;
    }
    .header-date-value { font-size: 2.2rem; font-weight: 700; line-height: 1; }
    .header-date-label { font-size: 0.9rem; opacity: 0.8; margin-top: 5px; }

    /* --- Stats Grid --- */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 24px; margin-bottom: 30px;
    }
    .stat-card {
        background: white; border-radius: 16px; padding: 24px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #f0f0f0;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    
    .stat-info h3 { font-size: 1.8rem; font-weight: 700; margin: 0; color: #111827; }
    .stat-info p { color: #6B7280; margin: 4px 0 12px 0; font-size: 0.9rem; font-weight: 500; }
    
    .stat-trend {
        font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px; border-radius: 6px; background: #F3F4F6; color: #4B5563; font-weight: 500;
    }
    /* UPDATED: Matched Captain's trend colors */
    .stat-trend.text-success { background: #ECFDF5; color: #059669; }
    .stat-trend.text-danger { background: #FEF2F2; color: #DC2626; }
    .stat-trend.text-warning { background: #FFFBEB; color: #B45309; }
    .stat-trend.text-primary { background: #EFF6FF; color: #2563EB; }

    .stat-icon {
        width: 64px; height: 64px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; color: white; flex-shrink: 0;
    }
    /* UPDATED: All icons now use Captain's gradient style and shadow */
    .icon-green { background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2); }
    .icon-red { background: linear-gradient(135deg, #EF4444, #DC2626); box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2); }
    .icon-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
    .icon-orange { background: linear-gradient(135deg, #F59E0B, #D97706); box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2); }

    /* --- Activity Panels --- */
    .dashboard-splits {
        display: grid; grid-template-columns: 2fr 1fr; gap: 24px;
    }
    .dashboard-panel {
        background: white; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0; overflow: hidden;
        display: flex; flex-direction: column; height: 100%;
    }
    .panel-header {
        padding: 20px 24px; display: flex; align-items: center; gap: 12px;
        color: white; font-weight: 600; font-size: 1.1rem;
    }
    /* UPDATED: Changed Green header to Captain's Blue header */
    .header-blue { background: linear-gradient(to right, #2563EB, #1D4ED8); }
    /* Orange stays the same as it matches Captain's second panel */
    .header-orange { background: linear-gradient(to right, #F59E0B, #D97706); }

    .panel-body { padding: 0; }
    
    .transaction-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 24px; border-bottom: 1px solid #F3F4F6;
        transition: background 0.2s;
    }
    .transaction-item:last-child { border-bottom: none; }
    .transaction-item:hover { background: #F9FAFB; }
    
    .trans-icon {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 0.9rem; margin-right: 15px; flex-shrink: 0;
    }
    /* Keep semantic colors for transactions (Green=Income, Red=Expense) but match hex codes */
    .bg-inc { background: #10B981; }
    .bg-exp { background: #EF4444; }
    
    .trans-info h4 { margin: 0 0 2px; font-size: 0.95rem; color: #1F2937; }
    .trans-info span { font-size: 0.8rem; color: #6B7280; }
    .trans-amount { font-weight: 700; font-size: 0.95rem; }
    .text-inc { color: #059669; }
    .text-exp { color: #DC2626; }

    .pending-count-box {
        padding: 30px; text-align: center;
    }
    .pending-number { font-size: 3rem; font-weight: 800; color: #D97706; line-height: 1; margin-bottom: 10px; }
    .pending-label { color: #6B7280; font-weight: 600; margin-bottom: 20px; }
    
    .empty-state { padding: 40px; text-align: center; color: #9CA3AF; font-style: italic; }

    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { 
        .header-section { padding: 24px; }
        .header-date-block { position: relative; top: 0; right: 0; text-align: left; margin-top: 20px; }
        .stats-grid { grid-template-columns: 1fr; }
        .dashboard-splits { grid-template-columns: 1fr; }
    }
</style>

    {{-- HEADER --}}
    <div class="header-section">
        <div class="header-title">Treasurer Dashboard</div>
        <div class="header-subtitle">Financial Monitoring & Budget Allocation</div>
        
        <div class="barangay-badge">
            <span class="badge-icon">₱</span>
            <span>Brgy. Calbueg, Malasiqui</span>
        </div>

        <div class="header-date-block">
            <div class="header-date-value">{{ now()->format('d') }}</div>
            <div class="header-date-label">{{ now()->format('F Y, l') }}</div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="stats-grid">
        {{-- 1. Total Revenue --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['total_revenue']) }}</h3>
                <p>Total Revenue</p>
                <div class="stat-trend text-success">
                    <i class="fas fa-arrow-up"></i> Collected
                </div>
            </div>
            {{-- Uses new gradient style --}}
            <div class="stat-icon icon-green"><i class="fas fa-hand-holding-usd"></i></div>
        </div>

        {{-- 2. Total Expenses --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['total_expenses']) }}</h3>
                <p>Total Expenses</p>
                <div class="stat-trend text-danger">
                    <i class="fas fa-chart-line"></i> This Year
                </div>
            </div>
            {{-- Uses new gradient style --}}
            <div class="stat-icon icon-red"><i class="fas fa-file-invoice-dollar"></i></div>
        </div>

        {{-- 3. Available Balance --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['available_balance']) }}</h3>
                <p>Available Funds</p>
                <div class="stat-trend text-primary">
                    <i class="fas fa-wallet"></i> Cash on Hand
                </div>
            </div>
            {{-- Uses new gradient style --}}
            <div class="stat-icon icon-blue"><i class="fas fa-university"></i></div>
        </div>

        {{-- 4. Expenses This Month --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['expenses_this_month']) }}</h3>
                <p>Expenses ({{ now()->format('M') }})</p>
                <div class="stat-trend text-warning">
                    <i class="far fa-calendar-alt"></i> Monthly
                </div>
            </div>
            {{-- Uses new gradient style --}}
            <div class="stat-icon icon-orange"><i class="fas fa-history"></i></div>
        </div>
    </div>

    {{-- SPLIT VIEW: Recent Transactions & Pending Approvals --}}
    <div class="dashboard-splits">
        
        {{-- Recent Transactions --}}
        <div class="dashboard-panel">
            {{-- UPDATED: header-blue --}}
            <div class="panel-header header-blue">
                <i class="fas fa-exchange-alt"></i>
                <h3>Recent Transactions</h3>
            </div>
            <div class="panel-body">
                @forelse($recentTransactions as $transaction)
                    <div class="transaction-item">
                        <div style="display: flex; align-items: center;">
                            <div class="trans-icon {{ $transaction->type == 'revenue' ? 'bg-inc' : 'bg-exp' }}">
                                <i class="fas {{ $transaction->type == 'revenue' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                            </div>
                            <div class="trans-info">
                                <h4>{{ $transaction->title }}</h4>
                                <span>{{ $transaction->category }} • {{ $transaction->transaction_date->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="trans-amount {{ $transaction->type == 'revenue' ? 'text-inc' : 'text-exp' }}">
                            {{ $transaction->type == 'revenue' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No recent transactions recorded.</div>
                @endforelse
                
                @if($recentTransactions->count() > 0)
                <div style="padding: 15px; text-align: center; border-top: 1px solid #eee;">
                    {{-- UPDATED: Link color to blue --}}
                    <a href="{{ route('treasurer.financial') }}" style="text-decoration: none; color: #2563EB; font-weight: 600; font-size: 0.9rem;">
                        View All Transactions &rarr;
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Pending Approvals Widget --}}
        <div class="dashboard-panel">
            {{-- Orange header matches Captain's second panel style --}}
            <div class="panel-header header-orange">
                <i class="fas fa-clock"></i>
                <h3>Pending Approvals</h3>
            </div>
            <div class="panel-body">
                <div class="pending-count-box">
                    <div class="pending-number">{{ $pendingCount }}</div>
                    <div class="pending-label">Transactions Waiting Review</div>
                    
                    @if($pendingCount > 0)
                        <a href="{{ route('treasurer.financial-management') }}" class="btn btn-warning text-white fw-bold w-100" style="background: #F59E0B; border: none; padding: 12px; border-radius: 8px;">
                            Review Requests
                        </a>
                    @else
                        <div style="color: #10B981; font-weight: 600; background: #D1FAE5; padding: 10px; border-radius: 8px;">
                            <i class="fas fa-check-circle"></i> All caught up!
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection