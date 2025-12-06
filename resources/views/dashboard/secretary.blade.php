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
            <i class="fas fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.financial-management') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    {{-- REPLACED: Search Residents with Announcements --}}
    <li class="nav-item">
        <a href="{{ route('secretary.announcements.index') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
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
        <div class="header-title">Secretary Dashboard</div>
        <div class="header-subtitle">Resident Profiling & Document Services</div>
        <div class="date-badge">{{ now()->format('m/d/Y') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['registered_residents'] ?? 0) }}</h3>
                <p>Total Residents</p>
            </div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-card">
             <div class="stat-info">
                 <h3>{{ number_format($stats['pending_documents'] ?? 0) }}</h3>
                 <p>Pending Documents</p>
                 <div class="stat-trend"><i class="fas fa-info-circle"></i> {{ $stats['documents_today'] ?? 0 }} today</div>
             </div>
             <div class="stat-icon icon-orange"><i class="fas fa-file-alt"></i></div>
        </div>
        <div class="stat-card">
             <div class="stat-info">
                 <h3>{{ number_format($stats['active_households'] ?? 0) }}</h3>
                 <p>Active Households</p>
             </div>
             <div class="stat-icon icon-green"><i class="fas fa-home"></i></div>
        </div>
        <div class="stat-card">
             <div class="stat-info">
                 <h3>{{ number_format($stats['documents_processed'] ?? 0) }}</h3>
                 <p>Documents Processed</p>
             </div>
             <div class="stat-icon icon-purple"><i class="fas fa-check-double"></i></div>
        </div>
    </div>

    <div class="activities-grid">
        <div class="activity-card">
            <div class="activity-header blue">
                <i class="fas fa-tasks"></i>
                <h3>Quick Actions</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="{{ route('secretary.resident.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add Resident
                </a>
                <a href="#" class="btn btn-primary">
                    <i class="fas fa-file-invoice"></i> New Document
                </a>
                <a href="{{ route('secretary.household.create') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i> New Household
                </a>
                <a href="{{ route('secretary.resident-profiling') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </a>
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header orange">
                <i class="fas fa-clock"></i>
                <h3>Pending Documents</h3>
            </div>
            {{-- Placeholder Content --}}
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Barangay Clearance - Jose Dela Cruz</div>
                    <div class="activity-meta">Submitted 2 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Certificate of Indigency - Maria Santos</div>
                    <div class="activity-meta">Submitted 4 hours ago</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"></div>
                <div class="activity-content">
                    <div class="activity-title">Certificate of Residency - Juan Garcia</div>
                    <div class="activity-meta">Submitted 6 hours ago</div>
                </div>
            </div>
        </div>
    </div>
@endsection