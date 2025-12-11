@extends('layouts.dashboard-layout')

@section('title', 'Resident Dashboard')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('resident.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('resident.health-services') }}" class="nav-link">
        <i class="fas fa-heartbeat"></i>
        <span>Health Services</span>
    </a>
</li>

{{-- NEW LINK HERE --}}
<li class="nav-item">
    <a href="{{ route('resident.incidents.index') }}" class="nav-link {{ request()->routeIs('resident.incidents.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident Reports</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('resident.announcements.index') }}" class="nav-link">
        <i class="fas fa-bullhorn"></i>
        <span>Announcements</span>
    </a>
</li>

@endsection

@section('content')
    <div class="header-section">
        <div class="header-title">Hi, {{ $user->first_name }} {{ $user->last_name }}</div>
        <div class="header-subtitle">Welcome to the Integrated Barangay Management Information System</div>
        <div class="barangay-badge">
            <span class="badge-icon">PH</span>
            <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
        </div>
        <div class="header-date-block">
            <div class="header-date-label">Current Date</div>
            <div class="header-date-value">{{ now()->format('m/d/Y') }}</div>
        </div>
    </div>

    <div class="stats-grid">
        {{-- 1. My Pending Documents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['my_pending_documents'] ?? 0) }}</h3>
                <p>My Pending Documents</p>
                <div class="stat-trend {{ ($stats['my_completed_documents'] ?? 0) > 0 ? 'text-success' : '' }}">
                    <i class="fas {{ ($stats['my_completed_documents'] ?? 0) > 0 ? 'fa-check' : 'fa-clock' }}"></i>
                    {{ $stats['my_completed_documents'] ?? 0 }} completed total
                </div>
            </div>
            <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
        </div>

        {{-- 2. My Household Members --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['my_household_members'] ?? 0) }}</h3>
                <p>My Household Members</p>
                <div class="stat-trend"><i class="fas fa-users"></i> In record</div>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>

        {{-- 3. New Announcements (Dynamic Data) --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['new_announcements'] ?? 0) }}</h3>
                <p>New Announcements</p>
                <div class="stat-trend">
                    <i class="fas fa-bullhorn"></i>
                    {{ $stats['unread_announcements'] ?? 0 }} total available
                </div>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-bell"></i></div>
        </div>
    </div>

    {{-- ACTIVITIES/EVENTS GRID --}}
    <div class="activities-grid">
        
        {{-- My Request History --}}
        <div>
            <div class="activity-header"> {{-- Blue header --}}
                <i class="fas fa-list"></i>
                <h3>My Request History</h3>
            </div>
            <div class="activity-content-wrapper">
                {{-- Placeholder content - Replace with dynamic data loop in future iterations --}}
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Barangay Clearance Request</div>
                        <div class="activity-meta">Status: Pending • 2 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Indigency Certificate</div>
                        <div class="activity-meta">Status: Completed • 1 day ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Business Permit Request</div>
                        <div class="activity-meta">Status: Denied • 3 days ago</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Upcoming Events (Static Placeholder for now) --}}
        <div>
            <div class="activity-header orange"> {{-- Orange header --}}
                <i class="fas fa-calendar-alt"></i>
                <h3>Upcoming Events</h3>
            </div>
            <div class="activity-content-wrapper">
                <div class="event-item">
                    <div class="event-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="event-details">
                        <div class="event-title">Monthly Barangay Assembly</div>
                        <div class="event-time">Dec 15, 2024 at 2:00 PM</div>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="event-details">
                        <div class="event-title">SK Sports Festival</div>
                        <div class="event-time">Dec 20, 2024 at 8:00 AM</div>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="event-details">
                        <div class="event-title">Health Check-up Program</div>
                        <div class="event-time">Dec 22, 2024 at 9:00 AM</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection