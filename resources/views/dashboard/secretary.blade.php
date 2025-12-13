@extends('layouts.dashboard-layout')

@section('title', 'Secretary Dashboard')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('secretary.dashboard') }}" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.resident-profiling') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.financial-management') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.incident-blotter') }}" class="nav-link">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.project-monitoring') }}" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.announcements.index') }}" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.sk-overview') }}" class="nav-link">
            <i class="fas fa-user-graduate"></i>
            <span>SK Module</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- Main Layout & Header (Matched to Captain) --- */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px;
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
        background: #FFA500;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 0.9rem;
    }

    .header-date-block {
        position: absolute;
        top: 40px;
        right: 40px;
        text-align: right;
    }
    .header-date-value { font-size: 2.2rem; font-weight: 700; line-height: 1; }
    .header-date-label { font-size: 0.9rem; opacity: 0.8; margin-top: 5px; }

    /* --- Stats Grid --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #f0f0f0;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
    
    .stat-info h3 { font-size: 2rem; font-weight: 700; margin: 0; color: #111827; }
    .stat-info p { color: #6B7280; margin: 4px 0 12px 0; font-size: 0.9rem; font-weight: 500; }
    
    .stat-trend {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #F3F4F6;
        color: #4B5563;
        font-weight: 500;
    }
    .stat-trend.text-success { background: #ECFDF5; color: #059669; }
    .stat-trend.text-primary { background: #EFF6FF; color: #2563EB; }
    .stat-trend.text-danger { background: #FEF2F2; color: #DC2626; }

    .stat-icon {
        width: 64px; height: 64px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; color: white;
        flex-shrink: 0;
    }
    .icon-blue { background: linear-gradient(135deg, #3B82F6, #2563EB); box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
    .icon-orange { background: linear-gradient(135deg, #F59E0B, #D97706); box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2); }
    .icon-green { background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2); }
    .icon-purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); box-shadow: 0 4px 10px rgba(124, 58, 237, 0.2); }
    .icon-pink { background: linear-gradient(135deg, #EC4899, #DB2777); box-shadow: 0 4px 10px rgba(219, 39, 119, 0.2); }

    /* --- Activities & Events Grid --- */
    .dashboard-splits {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .dashboard-panel {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Panel Headers */
    .panel-header {
        padding: 20px 24px;
        display: flex; align-items: center; gap: 12px;
        color: white; font-weight: 600; font-size: 1.1rem;
    }
    .panel-header h3 { margin: 0; font-size: inherit; font-weight: inherit; }
    .header-blue { background: linear-gradient(to right, #2563EB, #1D4ED8); }
    .header-orange { background: linear-gradient(to right, #F59E0B, #D97706); }

    /* Lists */
    .panel-body { padding: 0; }
    
    /* Activity Item */
    .activity-item {
        display: flex; align-items: flex-start; gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.2s;
    }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #F9FAFB; }
    
    .activity-dot {
        width: 12px; height: 12px; border-radius: 50%;
        margin-top: 6px; flex-shrink: 0;
        box-shadow: 0 0 0 4px rgba(255,255,255,1);
    }

    .item-content h4 { font-size: 0.95rem; font-weight: 600; color: #1F2937; margin: 0 0 4px 0; }
    .item-content p { font-size: 0.85rem; color: #6B7280; margin: 0; }
    .item-time { font-size: 0.75rem; color: #9CA3AF; margin-top: 4px; display: block; }

    /* Event Item */
    .event-item {
        display: flex; align-items: center; gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.2s;
    }
    .event-item:last-child { border-bottom: none; }
    .event-item:hover { background: #F9FAFB; }

    .event-date-box {
        background: #EFF6FF;
        color: #2563EB;
        border-radius: 10px;
        width: 50px; height: 50px;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
        line-height: 1.1;
    }
    .event-day { font-size: 1.1rem; }
    .event-month { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; }

    .empty-state {
        padding: 40px;
        text-align: center;
        color: #9CA3AF;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 991px) { .dashboard-splits { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .header-section { padding: 24px; }
        .header-date-block { position: relative; top: 0; right: 0; text-align: left; margin-top: 20px; }
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

    {{-- HEADER --}}
    <div class="header-section">
        <div class="header-title">Secretary Dashboard</div>
        <div class="header-subtitle">Integrated Barangay Management Information System (iBMIS)</div>
        
        <div class="barangay-badge">
            <span class="badge-icon">PH</span>
            <span>Brgy. Calbueg, Malasiqui, Pangasinan</span>
        </div>

        <div class="header-date-block">
            <div class="header-date-value">{{ now()->format('d') }}</div>
            <div class="header-date-label">{{ now()->format('F Y, l') }}</div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="stats-grid">
        {{-- 1. Total Residents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents'] ?? 0) }}</h3>
                <p>Total Active Residents</p>
                <div class="stat-trend text-primary">
                    <i class="fas fa-database"></i> Registered
                </div>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>

        {{-- 2. Budget (Read Only View) --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['budget_remaining'] ?? 0) }}</h3>
                <p>Current Available Funds</p>
                <div class="stat-trend {{ ($stats['budget_remaining'] < 100000) ? 'text-danger' : 'text-success' }}">
                    <i class="fas fa-wallet"></i> 
                    {{ ($stats['budget_remaining'] < 100000) ? 'Low Funds' : 'Healthy Status' }}
                </div>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-peso-sign"></i></div>
        </div>

        {{-- 3. Documents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['pending_documents'] ?? 0) }}</h3>
                <p>Pending Requests</p>
                <div class="stat-trend {{ ($stats['documents_completed_today'] > 0) ? 'text-success' : 'text-primary' }}">
                    <i class="fas fa-check-circle"></i> {{ $stats['documents_completed_today'] }} done today
                </div>
            </div>
            <div class="stat-icon icon-green"><i class="far fa-file-alt"></i></div>
        </div>

        {{-- 4. Projects --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_projects'] ?? 0) }}</h3>
                <p>Ongoing Projects</p>
                <div class="stat-trend text-primary">
                    <i class="fas fa-spinner"></i> {{ $stats['projects_near_completion'] }} near completion
                </div>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-hard-hat"></i></div>
        </div>

        {{-- 5. Incidents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['recent_incidents'] ?? 0) }}</h3>
                <p>New Incidents (7 Days)</p>
                <div class="stat-trend text-success">
                    <i class="fas fa-gavel"></i> {{ $stats['resolved_incidents'] }} Total Resolved
                </div>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-balance-scale"></i></div>
        </div>

        {{-- 6. Health --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['health_programs'] ?? 0) }}</h3>
                <p>Active Health Programs</p>
                <div class="stat-trend text-primary">
                    <i class="fas fa-notes-medical"></i> Monitoring
                </div>
            </div>
            <div class="stat-icon icon-pink"><i class="fas fa-heartbeat"></i></div>
        </div>
    </div>

    {{-- FEED & EVENTS --}}
    <div class="dashboard-splits">
        
        {{-- Recent Activities (Latest Document Requests) --}}
        <div class="dashboard-panel">
            <div class="panel-header header-blue">
                <i class="fas fa-history"></i>
                <h3>Recent Document Requests</h3>
            </div>
            <div class="panel-body">
                @forelse($activities as $item)
                    <div class="activity-item">
                        <div class="activity-dot" style="background-color: #2563EB"></div>
                        <div class="item-content">
                            <h4>{{ $item->documentType->name ?? 'Document Request' }}</h4>
                            <p>Requested by: {{ $item->resident->first_name ?? 'Resident' }} {{ $item->resident->last_name ?? '' }}</p>
                            <span class="item-time">
                                <i class="far fa-clock"></i> {{ $item->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No recent document requests.</div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Events --}}
        <div class="dashboard-panel">
            <div class="panel-header header-orange">
                <i class="fas fa-bullhorn"></i>
                <h3>Barangay Announcements</h3>
            </div>
            <div class="panel-body">
                @forelse($upcomingEvents as $event)
                    <div class="event-item">
                        <div class="event-date-box">
                            <span class="event-day">{{ $event->created_at->format('d') }}</span>
                            <span class="event-month">{{ $event->created_at->format('M') }}</span>
                        </div>
                        <div class="item-content">
                            <h4>{{ $event->title }}</h4>
                            <p>{{ Str::limit($event->content, 50) }}</p>
                            <span class="item-time">
                                <i class="fas fa-users"></i> {{ $event->audience ?? 'All' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No upcoming announcements.</div>
                @endforelse
            </div>
        </div>

    </div>
@endsection