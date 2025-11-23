{{-- resources/views/dashboards/captain.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Captain Dashboard')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link active">
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
        <a href="#" class="nav-link"> {{-- Add route later --}}
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
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
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
{{-- Add specific CSS for sizing based on captain-resident-profiling.blade.php and image reference --}}
<style>
    /* Header Sizing - Adjusted to match Resident Profiling & Household Mapping, and Dashboard headers */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px; /* Base padding */
    }
    .header-title {
        font-size: 2rem; /* Match profiling-title */
        font-weight: 700;
        margin-bottom: 8px;
    }
    .header-subtitle {
        opacity: 0.95;
        font-size: 1rem;
        margin-bottom: 15px; /* Space before barangay info if it exists */
    }
    .barangay-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 165, 0, 0.2);
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        /* margin-top: 15px; */ /* Removed redundant margin */
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
        font-size: 0.9rem; /* Match ph-icon */
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
        font-size: 2.5rem; /* Sized to match profiling 'total-registered-count' */
        font-weight: 700;
    }

    /* Stats Grid Sizing - MODIFIED to 3 columns to match dashboard image */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Match dashboard image (3 columns) */
        gap: 20px; /* Match profiling stats-row */
        margin-bottom: 30px; /* Match profiling */
    }
    .stat-card {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Match stat-box shadow */
        border-radius: 12px; /* Match stat-box */
        padding: 24px; /* Match stat-box */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-info h3 {
        font-size: 2.5rem; /* Match stat-content h3 */
        font-weight: 700; /* Match stat-content h3 */
        margin: 0 0 8px 0; /* Match stat-content h3 */
    }
    .stat-info p {
        color: #666; /* Match stat-content p */
        margin: 0 0 8px 0; /* Match stat-content p */
        font-size: 0.95rem; /* Match stat-content p */
    }
    .stat-trend {
        font-size: 0.85rem; /* Match stat-badge */
        display: flex;
        align-items: center;
        gap: 6px; /* Match stat-badge */
        color: #666;
    }
    .stat-trend.text-success { color: #10B981; }
    .stat-trend.text-primary { color: #2B5CE6; }
    .stat-trend.text-danger { color: #EF4444; }

    .stat-icon {
        width: 70px; /* Match stat-box-icon */
        height: 70px; /* Match stat-box-icon */
        border-radius: 12px; /* Match stat-box-icon */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem; /* Match stat-box-icon */
        color: white;
    }
    /* Keep original icon colors */
    .icon-blue { background: #2B5CE6; }
    .icon-orange { background: #FFA500; }
    .icon-green { background: #10B981; }
    .icon-purple { background: #A855F7; }
    .icon-pink { background: #EC4899; }

    /* --- Recent Activities & Upcoming Events (Matching NEW screenshot) --- */
    .activities-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Keep 2 columns */
        gap: 20px;
    }
    /* Removed background, shadow, border, overflow from activity-card */
    .activity-card {
        border-radius: 12px; /* Keep radius for header */
        /* background: white; */ /* REMOVED */
        /* box-shadow: 0 4px 12px rgba(0,0,0,0.05); */ /* REMOVED */
        /* border: 1px solid #E5E7EB; */ /* REMOVED */
        /* overflow: hidden; */ /* REMOVED */
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .activity-header {
        padding: 20px 24px; /* More padding */
        display: flex;
        align-items: center;
        gap: 12px; /* Adjusted gap */
        color: white;
        font-size: 1.1rem; /* Adjusted font size */
        font-weight: 600;
        background: #2B5CE6; /* Solid Blue */
        border-top-left-radius: 12px; /* Apply radius to header top corners */
        border-top-right-radius: 12px; /* Apply radius to header top corners */
        margin-bottom: 20px; /* Add space below header */
    }
    .activity-header h3 {
        font-size: inherit;
        font-weight: inherit;
        margin: 0;
    }
    .activity-header.orange {
        background: linear-gradient(135deg, #FCD34D 0%, #F59E0B 100%); /* Adjusted to lighter gold gradient */
    }

    .activity-content-wrapper {
        /* flex-grow: 1; */ /* REMOVED */
        padding: 0 24px; /* Only horizontal padding */
    }

    /* Recent Activity Item */
    .activity-item {
        display: flex;
        align-items: flex-start; /* Align items to the top */
        gap: 16px; /* More gap */
        padding: 12px 0; /* Reduced vertical padding */
        border-bottom: 1px solid #F3F4F6;
    }
    /* Adjusted last/first item selectors for direct children of wrapper */
    .activity-content-wrapper > .activity-item:last-of-type {
        border-bottom: none; /* No border for the last item */
        padding-bottom: 0;
    }
    .activity-content-wrapper > .activity-item:first-of-type {
        padding-top: 0;
    }

    .activity-icon {
        width: 10px; /* Match image */
        height: 10px; /* Match image */
        border-radius: 50%;
        background: #FBBF24; /* Amber-400 */
        margin-top: 7px; /* Align with text */
        flex-shrink: 0;
    }
    .activity-content .activity-title {
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 4px; /* Adjusted margin */
        font-size: 0.95rem; /* Adjusted font size */
    }
    .activity-content .activity-meta {
        color: #6B7280;
        font-size: 0.85rem; /* Adjusted font size */
    }

    /* Upcoming Event Item */
    .event-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 12px 0; /* Reduced vertical padding */
        border-bottom: 1px solid #F3F4F6;
    }
    /* Adjusted last/first item selectors for direct children of wrapper */
    .activity-content-wrapper > .event-item:last-of-type {
        border-bottom: none; /* No border for the last item */
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

    /* REMOVED .view-all-button-container */


    /* Responsive Adjustments (Matching profiling) */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 991px) { /* Adjusted breakpoint for better fit */
        .activities-grid {
            grid-template-columns: 1fr; /* Stack activities on medium screens */
        }
        .activity-card {
            margin-bottom: 20px; /* Add space between stacked cards */
        }
        .activities-grid > .activity-card:last-of-type {
            margin-bottom: 0;
        }
    }

    @media (max-width: 768px) {
        .header-section {
            padding: 30px; /* Adjust header padding for smaller screens */
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
        <div class="header-title">Captain Dashboard</div>
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

    {{-- Stats Grid (3 Columns) --}}
    <div class="stats-grid">
        {{-- 1. Total Residents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents'] ?? 0) }}</h3>
                <p>Total Active Residents</p>
                <div class="stat-trend"><i class="fas fa-users"></i> In record</div>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>

        {{-- 2. Monthly Budget --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['monthly_budget'] ?? 0) }}</h3>
                <p>Monthly Budget</p>
                <div class="stat-trend {{ ($stats['budget_remaining'] ?? 0) < ($stats['monthly_budget'] ?? 0) * 0.2 ? 'text-danger' : 'text-primary' }}">
                    <i class="fas fa-wallet"></i> ₱{{ number_format($stats['budget_remaining'] ?? 0) }} remaining
                </div>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-peso-sign"></i></div>
        </div>

        {{-- 3. Pending Documents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['pending_documents'] ?? 0) }}</h3>
                <p>Pending Documents</p>
                <div class="stat-trend {{ ($stats['documents_completed_today'] ?? 0) > 0 ? 'text-success' : '' }}">
                    <i class="fas {{ ($stats['documents_completed_today'] ?? 0) > 0 ? 'fa-check' : 'fa-clock' }}"></i>
                    {{ $stats['documents_completed_today'] ?? 0 }} completed today
                </div>
            </div>
            <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
        </div>

        {{-- 4. Active Projects --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_projects'] ?? 0) }}</h3>
                <p>Active Projects</p>
                <div class="stat-trend">
                    <i class="fas fa-tasks"></i> {{ $stats['projects_near_completion'] ?? 0 }} near completion
                </div>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-folder-open"></i></div>
        </div>

        {{-- 5. Recent Incidents --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['recent_incidents'] ?? 0) }}</h3>
                <p>Recent Incidents (7 days)</p>
                <div class="stat-trend">
                    <i class="fas fa-check-circle"></i> {{ $stats['resolved_incidents'] ?? 0 }} resolved total
                </div>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-exclamation-triangle"></i></div>
        </div>

        {{-- 6. Health Programs --}}
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['health_programs'] ?? 0) }}</h3>
                <p>Health Programs</p>
                <div class="stat-trend">
                    <i class="fas fa-notes-medical"></i> {{ $stats['ongoing_programs'] ?? 0 }} ongoing
                </div>
            </div>
            {{-- Assuming you add an .icon-pink style in your layout CSS --}}
            <div class="stat-icon icon-pink"><i class="fas fa-heart"></i></div>
        </div>
    </div>

    {{-- UPDATED ACTIVITIES/EVENTS GRID --}}
    <div class="activities-grid">
        {{-- Removed outer card div, applying styles to header/content separately --}}
        <div>
            <div class="activity-header"> {{-- Blue header --}}
                <i class="fas fa-list"></i>
                <h3>Recent Activities</h3>
            </div>
            <div class="activity-content-wrapper">
                {{-- Placeholder content - Replace with dynamic data --}}
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">New resident registered</div>
                        <div class="activity-meta">by Secretary Maria • 2 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Barangay clearance issued</div>
                        <div class="activity-meta">by Secretary Maria • 3 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Incident report filed</div>
                        <div class="activity-meta">by Tanod Juan • 5 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Project milestone completed</div>
                        <div class="activity-meta">by Kagawad Pedro • 1 day ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Health assistance approved</div>
                        <div class="activity-meta">by Health Worker Ana • 1 day ago</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Removed outer card div, applying styles to header/content separately --}}
        <div>
            <div class="activity-header orange"> {{-- Orange header --}}
                <i class="fas fa-calendar-alt"></i>
                <h3>Upcoming Events</h3>
            </div>
            <div class="activity-content-wrapper">
                {{-- Placeholder content - Replace with dynamic data --}}
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