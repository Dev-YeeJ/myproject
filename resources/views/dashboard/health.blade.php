{{-- resources/views/dashboards/bhw.blade.php --}}
{{-- This is the MAIN dashboard for the BHW user role --}}

@extends('layouts.dashboard-layout')

@section('title', 'BHW Dashboard')

@section('nav-items')
    {{-- MODIFIED: Only two navigation items as requested --}}
    <li class="nav-item">
        <a href="{{ route('dashboard.health') }}" class="nav-link active"> {{-- Dashboard is active --}}
            <i class="fas fa-home"></i>
            <span>Dashboard</span>  
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later, e.g., route('bhw.health-services') --}}
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
@endsection

@section('content')
{{-- STYLES: Copied from captain.blade.php for reference --}}
<style>
    /* Header Sizing - Copied from captain.blade.php */
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

    /* Stats Grid Sizing - MODIFIED to 4 columns for BHW content */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* BHW has 4 stats */
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

    /* --- Activities & Events (Copied from captain.blade.php) --- */
    .activities-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); 
        gap: 20px;
    }
    /* This style applies to the <div> wrapper now, not .activity-card */
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
    .activity-header.blue {
        /* This selector from BHW HTML now matches the default blue */
        background: #2B5CE6; 
    }

    .activity-content-wrapper {
        padding: 0 24px; /* Only horizontal padding */
    }

    /* Activity Item (from BHW content) */
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
        margin-top: 7px; /* Align with text */
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

    /* Event Item (from BHW content) */
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
    /* Styling for the date block from BHW HTML */
    .event-date {
        width: 50px; /* Custom size for this date block */
        flex-shrink: 0;
        text-align: center;
        background: #EFF6FF;
        border-radius: 8px;
        padding: 6px 0;
        font-weight: 600;
    }
    .event-day {
        font-size: 1.25rem;
        color: #2B5CE6;
        line-height: 1;
    }
    .event-month {
        font-size: 0.75rem;
        color: #6B7280;
        text-transform: uppercase;
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


    /* Responsive Adjustments (Matching captain.blade.php) */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
     @media (max-width: 991px) { 
        .activities-grid {
            grid-template-columns: 1fr; 
        }
        /* Add margin to the wrapper div when stacked */
        .activities-grid > div { 
             margin-bottom: 20px;
        }
         .activities-grid > div:last-of-type {
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
             font-size: 2rem; /* Adjusted size */
        }
        .header-date-label {
            font-size: 0.85rem;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- CONTENT: HTML from BHW dashboard, with structure modified to match styles --}}
<main class="main-content">
    <div class="header-section">
        <div class="header-title">Health & Wellness</div>
        <div class="header-subtitle">Health Programs & Community Services</div>
        
        {{-- REPLACED: .date-badge with .header-date-block for styling --}}
        <div class="header-date-block">
            <div class="header-date-label">Current Date</div>
            <div class="header-date-value">{{ now()->format('m/d/Y') }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['health_programs']) }}</h3>
                <p>Health Programs</p>
                <div class="stat-trend text-primary"> {{-- Added text-primary --}}
                    <i class="fas fa-heartbeat"></i>
                    <span>{{ $stats['ongoing_programs'] }} ongoing</span>
                </div>
            </div>
            <div class="stat-icon icon-pink">
                <i class="fas fa-heart"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents']) }}</h3>
                <p>Total Residents</p>
                 <div class="stat-trend"> {{-- Added trend line for consistency --}}
                    <i class="fas fa-users"></i>
                    <span>In record</span>
                </div>
            </div>
            <div class="stat-icon icon-blue">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $stats['beneficiaries_served'] ?? 245 }}</h3> {{-- Added variable --}}
                <p>Beneficiaries Served</p>
                <div class="stat-trend text-success"> {{-- Added text-success --}}
                    <i class="fas fa-arrow-up"></i>
                    <span>This month</span>
                </div>
            </div>
            <div class="stat-icon icon-green">
                <i class="fas fa-hospital-user"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $stats['scheduled_activities'] ?? 12 }}</h3> {{-- Added variable --}}
                <p>Scheduled Activities</p>
                <div class="stat-trend"> {{-- Added trend line for consistency --}}
                    <i class="fas fa-calendar-alt"></i>
                    <span>Upcoming</span>
                </div>
            </div>
            <div class="stat-icon icon-orange">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <div classs="activities-grid">
        
        {{-- MODIFIED: Added <div> wrapper and .activity-content-wrapper --}}
        <div>
            <div class="activity-header blue">
                <i class="fas fa-clinic-medical"></i>
                <h3>Ongoing Programs</h3>
            </div>
            <div class="activity-content-wrapper">
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">COVID-19 Vaccination - 320/500</div>
                        <div class="activity-meta">64% completion rate</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Nutrition Program - 145/150</div>
                        <div class="activity-meta">96% completion rate</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"></div>
                    <div class="activity-content">
                        <div class="activity-title">Blood Pressure Monitoring</div>
                        <div class="activity-meta">Every Tuesday & Thursday</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODIFIED: Added <div> wrapper and .activity-content-wrapper --}}
        <div>
            <div class="activity-header orange">
                <i class="fas fa-calendar-check"></i>
                <h3>Scheduled Missions</h3>
            </div>
            <div class="activity-content-wrapper">
                <div class="event-item">
                    <div class="event-date">
                        <div class="event-day">20</div>
                        <div class="event-month">DEC</div>
                    </div>
                    <div class="event-details">
                        <div class="event-title">Medical Mission</div>
                        <div class="event-time">8:00 AM at Barangay Hall</div>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-date">
                        <div class="event-day">28</div>
                        <div class="event-month">DEC</div>
                    </div>
                    <div class="event-details">
                        <div class="event-title">Dental Clinic</div>
                        <div class="event-time">10:00 AM</div>
                    </div>
                </div>
                <div class="event-item">
                    <div class="event-date">
                        <div class="event-day">15</div>
                        <div class="event-month">JAN</div>
                    </div>
                    <div class="event-details">
                        <div class="event-title">Health Awareness Drive</div>
                        <div class="event-time">2:00 PM</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection