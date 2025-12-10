{{-- resources/views/dashboard/sk-dashboard.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'SK Chairman Dashboard')

@section('nav-items')
    {{-- 1. Dashboard Overview --}}
    <li class="nav-item">
        <a href="{{ route('sk.dashboard') }}" class="nav-link {{ request()->routeIs('sk.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- 2. KK Youth Profiling --}}
    <li class="nav-item">
        <a href="{{ route('sk.youth-profiling') }}" class="nav-link {{ request()->routeIs('sk.youth-profiling') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>KK Profiling</span>
        </a>
    </li>

    {{-- 3. Projects & Events --}}
    <li class="nav-item">
        <a href="{{ route('sk.projects') }}" class="nav-link {{ request()->routeIs('sk.projects') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Projects & Events</span>
        </a>
    </li>

    {{-- 4. SK Officials --}}
    <li class="nav-item">
        <a href="{{ route('sk.officials') }}" class="nav-link {{ request()->routeIs('sk.officials') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span>SK Officials</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* SK Dashboard Theme - Matching Captain's Blue Gradient */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%); /* Blue Gradient */
        color: white;
        padding: 35px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(43, 92, 230, 0.2);
    }
    
    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 12px;
        background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .stat-body { padding: 25px; }
    
    /* Icon Boxes */
    .icon-box {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .bg-soft-red { background-color: #FEF2F2; color: #DC2626; }
    .bg-soft-blue { background-color: #EFF6FF; color: #2563EB; }
    .bg-soft-green { background-color: #ECFDF5; color: #059669; }
    .bg-soft-orange { background-color: #FFF7ED; color: #EA580C; }

    /* Badge Style */
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
        margin-top: 10px;
    }
    .barangay-badge .badge-icon {
        background: white; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #2B5CE6; /* Blue text */
    }
</style>

{{-- 1. Header Section --}}
<div class="header-section d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-1">SK Operations Center</h1>
        <p class="mb-0 opacity-90">Barangay Calbueg | Sangguniang Kabataan</p>
        
        <div class="barangay-badge">
            <span class="badge-icon">SK</span>
            <span>Barangay Calbueg, Malasiqui</span>
        </div>
    </div>
    <div class="text-right d-none d-md-block">
        <h2 class="font-weight-bold mb-0">{{ now()->format('h:i A') }}</h2>
        <p class="mb-0 opacity-75">{{ now()->format('l, F d, Y') }}</p>
    </div>
</div>

{{-- 2. Statistics Grid --}}
<div class="row">
    {{-- Total Youth (KK Members) --}}
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card stat-card">
            <div class="stat-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase font-weight-bold mb-1">KK Members</p>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['total_youth']) }}</h3>
                    </div>
                    <div class="icon-box bg-soft-blue"><i class="fas fa-users"></i></div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Aged 15-30 Years Old
                </div>
            </div>
        </div>
    </div>

    {{-- Registered Voters --}}
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card stat-card">
            <div class="stat-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase font-weight-bold mb-1">Youth Voters</p>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['registered_voters']) }}</h3>
                    </div>
                    <div class="icon-box bg-soft-green"><i class="fas fa-vote-yea"></i></div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    <i class="fas fa-check-circle mr-1 text-success"></i> Registered in COMELEC
                </div>
            </div>
        </div>
    </div>

    {{-- Active Projects --}}
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card stat-card">
            <div class="stat-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase font-weight-bold mb-1">Active Projects</p>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['active_projects']) }}</h3>
                    </div>
                    <div class="icon-box bg-soft-orange"><i class="fas fa-bullhorn"></i></div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    <i class="fas fa-clock mr-1 text-warning"></i> Currently In Progress
                </div>
            </div>
        </div>
    </div>

    {{-- Student Population --}}
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card stat-card">
            <div class="stat-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase font-weight-bold mb-1">Students</p>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ number_format($stats['students']) }}</h3>
                    </div>
                    <div class="icon-box bg-soft-red"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <div class="small text-muted border-top pt-2 mt-2">
                    <i class="fas fa-school mr-1"></i> Currently Enrolled
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Budget & Quick Actions Row --}}
<div class="row">
    {{-- Budget Overview Card --}}
    <div class="col-lg-8 mb-4">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg, #1F2937 0%, #111827 100%);">
            <div class="stat-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold mb-0"><i class="fas fa-wallet mr-2 text-primary"></i>SK Fund Status</h5>
                    <span class="badge badge-light text-dark">Fiscal Year {{ now()->year }}</span>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-md-6 border-right border-secondary">
                        <p class="text-gray-400 small text-uppercase mb-1">Total Allocation (10%)</p>
                        <h2 class="font-weight-bold mb-0">₱{{ number_format($stats['budget_allocation'], 2) }}</h2>
                        <div class="mt-3">
                            <p class="text-gray-400 small mb-1">Utilization Rate</p>
                            <div class="progress" style="height: 8px; background: rgba(255,255,255,0.1);">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $stats['utilization_rate'] }}%"></div>
                            </div>
                            <small class="d-block mt-1 text-right">{{ number_format($stats['utilization_rate'], 1) }}% Spent</small>
                        </div>
                    </div>
                    <div class="col-md-6 pl-md-4">
                        <div class="mb-3">
                            <p class="text-gray-400 small mb-1">Funds Committed (Projects)</p>
                            <h4 class="text-warning mb-0">₱{{ number_format($stats['budget_allocation'] - $stats['budget_remaining'], 2) }}</h4>
                        </div>
                        <div>
                            <p class="text-gray-400 small mb-1">Available Balance</p>
                            <h4 class="text-success mb-0">₱{{ number_format($stats['budget_remaining'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links / Actions --}}
    <div class="col-lg-4 mb-4">
        <div class="card stat-card bg-light border">
            <div class="stat-body">
                <h6 class="font-weight-bold mb-3 text-dark">Quick Actions</h6>
                <div class="list-group list-group-flush bg-transparent">
                    <a href="{{ route('sk.projects') }}" class="list-group-item list-group-item-action bg-transparent px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-plus-circle text-danger mr-2"></i> Create New Project</span>
                        <i class="fas fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="{{ route('sk.youth-profiling') }}" class="list-group-item list-group-item-action bg-transparent px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-search text-primary mr-2"></i> Search Youth Profile</span>
                        <i class="fas fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="{{ route('sk.officials') }}" class="list-group-item list-group-item-action bg-transparent px-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-tie text-success mr-2"></i> Update Officials</span>
                        <i class="fas fa-chevron-right small text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. Upcoming Events Table --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="font-weight-bold mb-0"><i class="fas fa-calendar-alt text-primary mr-2"></i>Upcoming Events</h5>
        <a href="{{ route('sk.projects') }}" class="small text-primary font-weight-bold">View All</a>
    </div>
    <div class="card-body px-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="pl-4">Project Title</th>
                        <th>Schedule</th>
                        <th>Budget</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingEvents as $event)
                    <tr>
                        <td class="pl-4 font-weight-bold text-dark">{{ $event->title }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="font-weight-bold">{{ $event->start_date->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $event->start_date->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td class="text-danger font-weight-bold">₱{{ number_format($event->budget, 2) }}</td>
                        <td>
                            @if($event->status == 'Planning') <span class="badge badge-warning">Planning</span>
                            @elseif($event->status == 'In Progress') <span class="badge badge-primary">In Progress</span>
                            @else <span class="badge badge-secondary">{{ $event->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" style="width: 50px; opacity: 0.5; margin-bottom: 10px;">
                            <p class="text-muted mb-0">No upcoming events scheduled for the next 30 days.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection