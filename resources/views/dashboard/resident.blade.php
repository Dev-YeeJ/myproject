{{-- resources/views/dashboard/resident.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Resident Dashboard')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link active">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        {{-- Link to the resident's document services page --}}
        <a href="{{ route('resident.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('resident.health-services') }}" class="nav-link {{ request()->routeIs('resident.health-services') ? 'active' : '' }}">
        <i class="fas fa-heartbeat"></i>
        <span>Health Services</span>
    </a>
</li>
    
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
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
{{-- Resident Dashboard Content --}}

<style>
    /* Header Sizing */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px; 
    }
    .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .header-subtitle {
        opacity: 0.95;
        font-size: 1rem;
        margin-bottom: 15px; 
    }
    .barangay-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 165, 0, 0.2);
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }
    .barangay-badge .badge-icon {
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
    .header-date-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 4px;
    }
    .header-date-value {
        font-size: 2.5rem; 
        font-weight: 700;
    }

    /* Stats Grid Sizing */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 columns */
        gap: 20px; 
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
        border-radius: 12px; 
        padding: 24px; 
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-info h3 {
        font-size: 2.5rem; 
        font-weight: 700; 
        margin: 0 0 8px 0; 
    }
    .stat-info p {
        color: #666; 
        margin: 0 0 8px 0; 
        font-size: 0.95rem; 
    }
    .stat-trend {
        font-size: 0.85rem; 
        display: flex;
        align-items: center;
        gap: 6px; 
        color: #666;
    }
    .stat-trend.text-success { color: #10B981; }
    .stat-trend.text-primary { color: #2B5CE6; }
    .stat-trend.text-danger { color: #EF4444; }

    .stat-icon {
        width: 70px; 
        height: 70px; 
        border-radius: 12px; 
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem; 
        color: white;
    }
    .icon-blue { background: #2B5CE6; }
    .icon-orange { background: #FFA500; }
    .icon-green { background: #10B981; }
    .icon-purple { background: #A855F7; }
    .icon-pink { background: #EC4899; }

    /* Recent Activities & Upcoming Events */
    .activities-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 columns */
        gap: 20px;
    }
    .activity-card {
        border-radius: 12px; 
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .activity-header {
        padding: 20px 24px; 
        display: flex;
        align-items: center;
        gap: 12px; 
        color: white;
        font-size: 1.1rem; 
        font-weight: 600;
        background: #2B5CE6; /* Solid Blue */
        border-top-left-radius: 12px; 
        border-top-right-radius: 12px; 
        margin-bottom: 20px; 
    }
    .activity-header h3 {
        font-size: inherit;
        font-weight: inherit;
        margin: 0;
    }
    .activity-header.orange {
        background: linear-gradient(135deg, #FCD34D 0%, #F59E0B 100%); 
    }

    .activity-content-wrapper {
        padding: 0 24px; /* Only horizontal padding */
    }

    /* My Request History Item */
    .activity-item {
        display: flex;
        align-items: flex-start; 
        gap: 16px; 
        padding: 12px 0; 
        border-bottom: 1px solid #F3F4F6;
    }
    .activity-content-wrapper > .activity-item:last-of-type {
        border-bottom: none; 
        padding-bottom: 0;
    }
    .activity-content-wrapper > .activity-item:first-of-type {
        padding-top: 0;
    }

    .activity-icon {
        width: 10px; 
        height: 10px; 
        border-radius: 50%;
        background: #FBBF24; /* Amber-400 */
        margin-top: 7px; 
        flex-shrink: 0;
    }
    .activity-content .activity-title {
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 4px; 
        font-size: 0.95rem; 
    }
    .activity-content .activity-meta {
        color: #6B7280;
        font-size: 0.85rem; 
    }

    /* Upcoming Event Item */
    .event-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 12px 0; 
        border-bottom: 1px solid #F3F4F6;
    }
    .activity-content-wrapper > .event-item:last-of-type {
        border-bottom: none; 
        padding-bottom: 0;
    }
    .activity-content-wrapper > .event-item:first-of-type {
        padding-top: 0;
    }
    
    .event-icon {
        width: 40px;
        height: 40px;
        background: #EFF6FF; /* Light blue background */
        color: #2B5CE6; /* Blue icon */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .event-details .event-title {
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 4px;
        font-size: 0.95rem;
    }
    .event-details .event-time {
        color: #6B7280;
        font-size: 0.85rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 991px) { 
        .activities-grid {
            grid-template-columns: 1fr; /* Stack activities on medium screens */
        }
        .activity-card {
            margin-bottom: 20px; 
        }
        .activities-grid > .activity-card:last-of-type {
            margin-bottom: 0;
        }
    }
    @media (max-width: 768px) {
        .header-section {
            padding: 30px; 
        }
        .header-date-block {
            position: static;
            margin-top: 20px;
            text-align: left;
        }
        .header-date-value {
            font-size: 1.25rem;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

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

{{-- Stats Grid (3 Columns) - Resident Specific --}}
<div class="stats-grid">
    {{-- 1. My Pending Documents --}}
    <div class="stat-card">
        <div class="stat-info">
            {{-- Controller needs to pass $stats['my_pending_documents'] --}}
            <h3>{{ number_format($stats['my_pending_documents'] ?? 0) }}</h3>
            <p>My Pending Documents</p>
            <div class="stat-trend {{ ($stats['my_completed_documents'] ?? 0) > 0 ? 'text-success' : '' }}">
                <i class="fas {{ ($stats['my_completed_documents'] ?? 0) > 0 ? 'fa-check' : 'fa-clock' }}"></i>
                {{ $stats['my_completed_documents'] ?? 0 }} completed total
            </div>
        </div>
        <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
    </div>

    {{-- 2. My Household --}}
    <div class="stat-card">
        <div class="stat-info">
            {{-- Controller needs to pass $stats['my_household_members'] --}}
            <h3>{{ number_format($stats['my_household_members'] ?? 0) }}</h3>
            <p>My Household Members</p>
            <div class="stat-trend"><i class="fas fa-users"></i> In record</div>
        </div>
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
    </div>

    {{-- 3. New Announcements --}}
    <div class="stat-card">
        <div class="stat-info">
            {{-- Controller needs to pass $stats['new_announcements'] --}}
            <h3>{{ number_format($stats['new_announcements'] ?? 0) }}</h3>
            <p>New Announcements</p>
            <div class="stat-trend">
                <i class="fas fa-bell"></i>
                {{ $stats['unread_announcements'] ?? 0 }} unread
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
            {{-- Placeholder content - Replace with dynamic data --}}
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
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Barangay Clearance Request</div>
                    <div class="activity-meta">Status: Completed • 2 weeks ago</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div>
        <div class="activity-header orange"> {{-- Orange header --}}
            <i class="fas fa-calendar-alt"></i>
            <h3>Upcoming Events</h3>
        </div>
        <div class="activity-content-wrapper">
            {{-- Placeholder content - This is relevant for residents --}}
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
            <div class="event-item">
                <div class="event-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="event-details">
                    <div class="event-title">Year-end Financial Review</div>
                    <div class="event-time">Dec 28, 2024 at 10:00 AM</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection