@extends('layouts.dashboard-layout')

@section('title', 'Kagawad Dashboard')

@section('nav-items')
    {{-- COMPLETE KAGAWAD NAVIGATION --}}
    <li class="nav-item">
        <a href="{{ route('kagawad.dashboard') }}" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.residents') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">
            <i class="fas fa-tasks"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">
            <i class="fas fa-gavel"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Committees</span>
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
    /* REUSED CSS FROM CAPTAIN DASHBOARD FOR CONSISTENCY */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px;
    }
    .header-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .header-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2);
        padding: 8px 16px; border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white; font-size: 0.9rem;
    }
    .header-date-block { position: absolute; top: 40px; right: 40px; text-align: right; }
    .header-date-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .header-date-value { font-size: 2.5rem; font-weight: 700; }

    /* Stats Grid */
    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;
    }
    .stat-card {
        background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 12px; padding: 24px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .stat-info h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-info p { color: #666; margin: 0; font-size: 0.95rem; }
    
    .stat-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue { background: #2B5CE6; }
    .icon-orange { background: #FFA500; }
    .icon-green { background: #10B981; }
    .icon-pink { background: #EC4899; }

    /* Activity Grid */
    .activities-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    
    .activity-header {
        padding: 20px 24px; display: flex; align-items: center; gap: 12px;
        color: white; font-size: 1.1rem; font-weight: 600;
        border-top-left-radius: 12px; border-top-right-radius: 12px; margin-bottom: 0;
    }
    .activity-header.blue { background: #2B5CE6; }
    .activity-header.orange { background: linear-gradient(135deg, #FCD34D 0%, #F59E0B 100%); }

    .activity-content-wrapper {
        background: white; border: 1px solid #E5E7EB; border-top: none;
        border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;
        padding: 0 24px;
    }

    .activity-item, .event-item {
        display: flex; align-items: flex-start; gap: 16px; padding: 16px 0;
        border-bottom: 1px solid #F3F4F6;
    }
    .activity-item:last-child, .event-item:last-child { border-bottom: none; }

    .activity-icon-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #FBBF24; margin-top: 7px; flex-shrink: 0;
    }
    
    .event-date-box {
        background: #EFF6FF; color: #2B5CE6;
        width: 50px; height: 50px; border-radius: 8px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        font-weight: 700; line-height: 1;
    }
    .event-day { font-size: 1.2rem; }
    .event-month { font-size: 0.7rem; text-transform: uppercase; }

    /* Responsive */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-grid, .activities-grid { grid-template-columns: 1fr; } }
</style>

    {{-- HEADER --}}
    <div class="header-section">
        <div class="header-title">Kagawad Dashboard</div>
        <div class="header-subtitle">Community Development & Project Monitoring</div>
        <div class="barangay-badge">
            <span class="badge-icon">PH</span>
            <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
        </div>
        <div class="header-date-block">
            <div class="header-date-label">Current Date</div>
            <div class="header-date-value">{{ now()->format('m/d/Y') }}</div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_projects']) }}</h3>
                <p>Active Projects</p>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents']) }}</h3>
                <p>Total Residents</p>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_households']) }}</h3>
                <p>Active Households</p>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-home"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['pending_incidents']) }}</h3>
                <p>Pending Cases (Lupon)</p>
            </div>
            <div class="stat-icon icon-pink">
                <i class="fas fa-gavel"></i>
            </div>
        </div>
    </div>

    {{-- ACTIVITIES & EVENTS --}}
    <div class="activities-grid">
        {{-- Active Projects List --}}
        <div>
            <div class="activity-header blue">
                <i class="fas fa-project-diagram"></i>
                <h3>Priority Projects</h3>
            </div>
            <div class="activity-content-wrapper">
                @forelse($projects as $project)
                <div class="activity-item">
                    <div class="activity-icon-dot"></div>
                    <div class="activity-content">
                        <div style="font-weight: 600; color: #1F2937;">{{ $project->title }}</div>
                        <div style="color: #6B7280; font-size: 0.85rem;">
                            Progress: {{ $project->progress }}% • Due: {{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <span class="text-muted">No active projects at the moment.</span>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Events --}}
        <div>
            <div class="activity-header orange">
                <i class="fas fa-calendar-check"></i>
                <h3>Upcoming Announcements</h3>
            </div>
            <div class="activity-content-wrapper">
                @forelse($upcomingEvents as $event)
                <div class="event-item">
                    <div class="event-date-box">
                        <div class="event-day">{{ $event->created_at->format('d') }}</div>
                        <div class="event-month">{{ $event->created_at->format('M') }}</div>
                    </div>
                    <div class="event-details">
                        <div style="font-weight: 600; color: #1F2937;">{{ $event->title }}</div>
                        <div style="color: #6B7280; font-size: 0.85rem;">
                            {{ Str::limit($event->content, 50) }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="event-item">
                    <span class="text-muted">No upcoming announcements.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection