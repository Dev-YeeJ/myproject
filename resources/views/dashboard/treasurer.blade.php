@extends('layouts.dashboard-layout')

@section('title', 'Treasurer Dashboard')

@section('nav-items')
    {{-- 1. Dashboard --}}
    <li class="nav-item">
        <a href="{{ route('treasurer.dashboard') }}" class="nav-link {{ request()->routeIs('treasurer.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- 2. Financial Management --}}
    <li class="nav-item">
        <a href="{{ route('treasurer.financial') }}" class="nav-link {{ request()->routeIs('treasurer.financial*') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>

    {{-- 3. Announcements --}}
    <li class="nav-item">
        <a href="{{ route('treasurer.announcements.index') }}" class="nav-link {{ request()->routeIs('treasurer.announcements.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* Specific styles for Treasurer Dashboard */
    .header-section {
        background: linear-gradient(135deg, #059669 0%, #047857 100%); /* Green Theme */
        color: white;
        border-radius: 16px;
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
    }
    .header-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .header-subtitle { opacity: 0.9; font-size: 1rem; }
    .date-badge {
        position: absolute;
        top: 40px; right: 40px;
        background: rgba(255,255,255,0.2);
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-info h3 { font-size: 1.8rem; font-weight: 700; margin: 0 0 4px 0; }
    .stat-info p { color: #6B7280; margin: 0; font-size: 0.9rem; }
    .stat-trend { margin-top: 8px; font-size: 0.85rem; color: #10B981; display: flex; align-items: center; gap: 4px; }
    .stat-icon {
        width: 60px; height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem; color: white;
    }
    .icon-green { background: #10B981; }
    .icon-orange { background: #F59E0B; }
    .icon-blue { background: #3B82F6; }
    .icon-purple { background: #8B5CF6; }

    /* Activities Grid */
    .activities-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .activity-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .activity-header {
        padding: 20px;
        display: flex; align-items: center; gap: 10px;
        color: white; font-weight: 600; font-size: 1.1rem;
    }
    .activity-header.blue { background: #3B82F6; }
    .activity-header.orange { background: #F59E0B; }
    
    .activity-item {
        padding: 15px 20px;
        border-bottom: 1px solid #F3F4F6;
        display: flex; align-items: flex-start; gap: 15px;
    }
    .activity-icon {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: #D1D5DB;
        margin-top: 6px;
    }
    .activity-title { font-weight: 600; color: #1F2937; margin-bottom: 2px; }
    .activity-meta { font-size: 0.85rem; color: #6B7280; }
    .activity-amount { margin-left: auto; font-weight: 600; font-size: 0.9rem; }
    .text-green { color: #10B981; }
    .text-red { color: #EF4444; }

    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { 
        .stats-grid { grid-template-columns: 1fr; } 
        .activities-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="header-section">
    <div class="header-title">Financial Dashboard</div>
    <div class="header-subtitle">Budget & Financial Management</div>
    <div class="date-badge">{{ now()->format('F d, Y') }}</div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>₱{{ number_format($stats['total_revenue']) }}</h3>
            <p>Total Revenue</p>
            <div class="stat-trend">
                <i class="fas fa-coins"></i>
                <span>Collected</span>
            </div>
        </div>
        <div class="stat-icon icon-green">
            <i class="fas fa-arrow-trending-up"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>₱{{ number_format($stats['total_expenses']) }}</h3>
            <p>Total Expenses</p>
            <div class="stat-trend" style="color: #F59E0B;">
                <i class="fas fa-chart-line"></i>
                <span>₱{{ number_format($stats['expenses_this_month']) }} this month</span>
            </div>
        </div>
        <div class="stat-icon icon-orange">
            <i class="fas fa-arrow-trending-down"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>₱{{ number_format($stats['available_balance']) }}</h3>
            <p>Available Balance</p>
        </div>
        <div class="stat-icon icon-blue">
            <i class="fas fa-wallet"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>₱{{ number_format($stats['monthly_budget']) }}</h3>
            <p>Monthly Budget</p>
        </div>
        <div class="stat-icon icon-purple">
            <i class="fas fa-chart-pie"></i>
        </div>
    </div>
</div>

<div class="activities-grid">
    {{-- Reports Card --}}
    <div class="activity-card">
        <div class="activity-header blue">
            <i class="fas fa-file-pdf"></i>
            <h3>Financial Reports</h3>
        </div>
        {{-- Static Placeholders or Dynamic if available --}}
        <div class="activity-item">
            <div class="activity-icon" style="background: #3B82F6;"></div>
            <div class="activity-content">
                <div class="activity-title">Monthly Budget Report - {{ now()->subMonth()->format('F') }}</div>
                <div class="activity-meta">Generated 2 days ago</div>
            </div>
            <div style="margin-left: auto;">
                <a href="#" class="btn btn-sm btn-light text-primary"><i class="fas fa-download"></i></a>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #3B82F6;"></div>
            <div class="activity-content">
                <div class="activity-title">Expenditure Summary</div>
                <div class="activity-meta">Generated 5 days ago</div>
            </div>
             <div style="margin-left: auto;">
                <a href="#" class="btn btn-sm btn-light text-primary"><i class="fas fa-download"></i></a>
            </div>
        </div>
        <div class="activity-item">
            <div class="activity-icon" style="background: #3B82F6;"></div>
            <div class="activity-content">
                <div class="activity-title">Fund Allocation Report</div>
                <div class="activity-meta">Generated 1 week ago</div>
            </div>
             <div style="margin-left: auto;">
                <a href="#" class="btn btn-sm btn-light text-primary"><i class="fas fa-download"></i></a>
            </div>
        </div>
        <div style="padding: 15px; text-align: center;">
            <a href="{{ route('treasurer.financial') }}" style="text-decoration: none; color: #3B82F6; font-weight: 600; font-size: 0.9rem;">
                View All Reports &rarr;
            </a>
        </div>
    </div>

    {{-- Transactions Card (Dynamic) --}}
    <div class="activity-card">
        <div class="activity-header orange">
            <i class="fas fa-money-bill-wave"></i>
            <h3>Recent Transactions</h3>
        </div>
        
        @forelse($recentTransactions as $transaction)
            <div class="activity-item">
                <div class="activity-icon" style="background: {{ $transaction->type == 'revenue' ? '#10B981' : '#EF4444' }}"></div>
                <div class="activity-content">
                    <div class="activity-title">{{ $transaction->title }}</div>
                    <div class="activity-meta">
                        {{ ucfirst($transaction->category) }} • {{ $transaction->transaction_date->diffForHumans() }}
                    </div>
                </div>
                <div class="activity-amount {{ $transaction->type == 'revenue' ? 'text-green' : 'text-red' }}">
                    {{ $transaction->type == 'revenue' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                </div>
            </div>
        @empty
            <div class="activity-item">
                <div class="activity-content">
                    <div class="activity-meta">No recent transactions found.</div>
                </div>
            </div>
        @endforelse

        <div style="padding: 15px; text-align: center;">
            <a href="{{ route('treasurer.financial') }}" style="text-decoration: none; color: #F59E0B; font-weight: 600; font-size: 0.9rem;">
                Manage All Finances &rarr;
            </a>
        </div>
    </div>
</div>
@endsection