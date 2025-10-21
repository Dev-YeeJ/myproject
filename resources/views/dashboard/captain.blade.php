<!-- ============================================
FILE: resources/views/dashboards/captain.blade.php
DESCRIPTION: Barangay Captain Dashboard View
============================================ -->

@extends('layouts.dashboard-layout')

@section('title', 'Captain Dashboard')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('dashboard.captain') }}" class="nav-link active">
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
        <a href="#" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-check-circle"></i>
            <span>SK Module</span>
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
    <div class="header-section">
        <div class="header-title">Dashboard</div>
        <div class="header-subtitle">Welcome to the Integrated Barangay Management Information System</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents']) }}</h3>
                <p>Total Residents</p>
                <div class="stat-trend"><i class="fas fa-arrow-up"></i> Growing</div>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>₱{{ number_format($stats['monthly_budget']) }}</h3>
                <p>Monthly Budget</p>
                <div class="stat-trend"><i class="fas fa-arrow-up"></i> ₱{{ number_format($stats['budget_remaining']) }} remaining</div>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-peso-sign"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['pending_documents']) }}</h3>
                <p>Pending Documents</p>
                <div class="stat-trend"><i class="fas fa-check"></i> {{ $stats['documents_completed_today'] }} today</div>
            </div>
            <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['active_projects']) }}</h3>
                <p>Active Projects</p>
                <div class="stat-trend"><i class="fas fa-arrow-up"></i> {{ $stats['projects_near_completion'] }} near completion</div>
            </div>
            <div class="stat-icon icon-orange"><i class="fas fa-folder-open"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['recent_incidents']) }}</h3>
                <p>Recent Incidents</p>
                <div class="stat-trend"><i class="fas fa-check-circle"></i> {{ $stats['resolved_incidents'] }} resolved</div>
            </div>
            <div class="stat-icon icon-purple"><i class="fas fa-exclamation-triangle"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['health_programs']) }}</h3>
                <p>Health Programs</p>
                <div class="stat-trend"><i class="fas fa-heartbeat"></i> {{ $stats['ongoing_programs'] }} ongoing</div>
            </div>
            <div class="stat-icon icon-pink"><i class="fas fa-heart"></i></div>
        </div>
    </div>

    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-list"></i>
                <h3>Recent Activities</h3>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">New Resident Registered</div>
                    <div class="activity-meta">by Secretary Sunshine • 2 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Barangay Clearance Issued</div>
                    <div class="activity-meta">by Kagawad Martinez • 4 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Budget Report Submitted</div>
                    <div class="activity-meta">by Treasurer Reyes • 6 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Health Program Registration</div>
                    <div class="activity-meta">by Health Worker Santos • 8 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Security Incident Resolved</div>
                    <div class="activity-meta">by Tanod Cruz • Yesterday</div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-calendar"></i>
                <h3>Upcoming Events</h3>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">15</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Monthly Barangay Assembly</div>
                    <div class="event-time">2:00 PM</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">20</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Medical Mission</div>
                    <div class="event-time">8:00 AM</div>
                </div>
            </div>
            <div class="event-item">
                <div class="event-date">
                    <div class="event-day">25</div>
                    <div class="event-month">DEC</div>
                </div>
                <div class="event-details">
                    <div class="event-title">Christmas Celebration</div>
                    <div class="event-time">4:00 PM</div>
                </div>
            </div>
        </div>
    </div>
@endsection