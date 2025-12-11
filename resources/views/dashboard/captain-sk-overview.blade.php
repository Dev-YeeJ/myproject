{{-- resources/views/dashboard/captain-sk-overview.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'SK Oversight Module')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link">
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
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.financial') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.incident.index') }}" class="nav-link">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.project.monitoring') }}" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.announcements.index') }}" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.sk.overview') }}" class="nav-link active">
            <i class="fas fa-user-graduate"></i>
            <span>SK Module</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- UPDATED: Main Blue Gradient Theme --- */
    .module-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%); /* Blue Gradient matching reference */
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
    }

    .module-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .module-subtitle {
        opacity: 0.95;
        font-size: 1rem;
        margin-bottom: 15px;
    }

    .barangay-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }

    .barangay-badge .badge-icon {
        background: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #2B5CE6; /* UPDATED: Blue text */
    }

    .total-registered {
        position: absolute;
        top: 40px;
        right: 40px;
        text-align: right;
    }

    .total-registered-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 4px;
    }

    .total-registered-count {
        font-size: 2.5rem;
        font-weight: 700;
    }

    .total-registered-sublabel {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    /* Stats Grid */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-box {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 4px 0;
    }

    .stat-content p {
        color: #666;
        margin: 0 0 8px 0;
        font-size: 0.9rem;
    }

    .stat-badge {
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Color Utilities (Kept consistent with reference) */
    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }
    .stat-badge.red { color: #DC2626; }
    .stat-badge.purple { color: #A855F7; }

    .stat-box-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }

    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-red-bg { background: #DC2626; }
    .icon-purple-bg { background: #A855F7; }

    /* --- UPDATED: Directory / Table Header Blue Gradient --- */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%); /* Blue Gradient */
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .directory-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .table-container {
        background: white;
        border-radius: 0 0 12px 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        margin-bottom: 30px;
    }

    .table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .table thead { background: #F9FAFB; }

    .table th {
        padding: 16px 20px;
        font-weight: 700;
        color: #1F2937;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #E5E7EB;
    }

    .table td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #F3F4F6;
        color: #4B5563;
    }
    
    .table tr:last-child td { border-bottom: none; }

    /* Progress Bar Customization */
    .progress-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4B5563;
    }
    .custom-progress {
        height: 8px;
        background: #E5E7EB;
        border-radius: 4px;
        overflow: hidden;
    }
    .custom-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    
    /* Layout Columns */
    .dashboard-columns {
        display: grid;
        grid-template-columns: 2fr 1fr; /* Main content vs Side panel */
        gap: 25px;
    }

    /* Side Panel Cards */
    .side-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 25px;
    }
    .side-header {
        padding: 20px;
        border-bottom: 1px solid #F3F4F6;
        font-weight: 700;
        color: #1F2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .side-body {
        padding: 0;
    }
    
    .official-item {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #F3F4F6;
    }
    .official-item:last-child { border-bottom: none; }
    
    /* --- UPDATED: Official Avatar Blue Theme --- */
    .official-avatar {
        width: 40px; 
        height: 40px; 
        background: #DBEAFE; /* Light blue bg */
        color: #1E40AF; /* Dark blue text */
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
    }
    .official-info h6 { margin: 0; font-size: 0.95rem; font-weight: 600; color: #1F2937; }
    /* --- UPDATED: Official Role Text Color --- */
    .official-info small { color: #2B5CE6; font-weight: 600; font-size: 0.8rem; }

    @media (max-width: 1200px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .dashboard-columns { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .module-header { text-align: left; }
        .total-registered { position: static; margin-top: 20px; text-align: left; }
    }
</style>

{{-- HEADER SECTION --}}
<div class="module-header">
    <div class="module-title">Sangguniang Kabataan Oversight</div>
    <div class="module-subtitle">Monitor youth demographics, budget utilization, and projects</div>
    <div class="barangay-badge">
        <span class="badge-icon">SK</span>
        <span>Barangay Calbueg, Malasiqui</span>
    </div>
    <div class="total-registered">
        <div class="total-registered-label">Total KK Members</div>
        <div class="total-registered-count">{{ $youthStats['total_youth'] }}</div>
        <div class="total-registered-sublabel">Age 15-30 Years Old</div>
    </div>
</div>

{{-- STATS GRID --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $youthStats['registered_voters'] }}</h3>
            <p>Registered Voters</p>
            <div class="stat-badge blue">
                <i class="fas fa-vote-yea"></i>
                <span>Participating Youth</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-vote-yea"></i>
        </div>
    </div>
    
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $youthStats['students'] }}</h3>
            <p>Students</p>
            <div class="stat-badge purple">
                <i class="fas fa-graduation-cap"></i>
                <span>Currently Enrolled</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-graduation-cap"></i>
        </div>
    </div>
    
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $youthStats['out_of_school'] }}</h3>
            <p>Out-of-School</p>
            <div class="stat-badge orange">
                <i class="fas fa-user-slash"></i>
                <span>Needs Attention</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-briefcase"></i>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $youthStats['employed'] ?? 0 }}</h3> 
            <p>Working Youth</p>
            <div class="stat-badge green">
                <i class="fas fa-check-circle"></i>
                <span>Employed</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-user-tie"></i>
        </div>
    </div>
</div>

<div class="dashboard-columns">
    
    {{-- MAIN COLUMN: PROJECTS & BUDGET --}}
    <div class="main-column">
        
        {{-- SK Fund Status Section --}}
        <div class="directory-header">
            <div class="directory-title">
                <i class="fas fa-coins"></i>
                <span>SK Annual Budget Status</span>
            </div>
            <div style="font-size: 1.2rem; font-weight: 700;">
                Remaining: ₱{{ number_format($budgetStats['available_cash'], 2) }}
            </div>
        </div>
        
        <div class="table-container" style="padding: 25px;">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="progress-wrapper">
                        <div class="progress-label">
                            <span>Total Allocation (10%)</span>
                            <span>₱{{ number_format($budgetStats['allocation'], 2) }}</span>
                        </div>
                        <div class="custom-progress">
                            <div class="custom-bar icon-blue-bg" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="progress-wrapper">
                        <div class="progress-label">
                            <span>Committed to Projects</span>
                            <span>₱{{ number_format($budgetStats['committed'], 2) }}</span>
                        </div>
                        @php $comPercent = ($budgetStats['allocation'] > 0) ? ($budgetStats['committed'] / $budgetStats['allocation']) * 100 : 0; @endphp
                        <div class="custom-progress">
                            <div class="custom-bar icon-orange-bg" style="width: {{ $comPercent }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="progress-wrapper">
                        <div class="progress-label">
                            <span>Actual Amount Spent</span>
                            <span>₱{{ number_format($budgetStats['spent'], 2) }}</span>
                        </div>
                        <div class="custom-progress">
                            <div class="custom-bar icon-red-bg" style="width: {{ $budgetStats['utilization_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Projects Table --}}
        <div class="directory-header">
            <div class="directory-title">
                <i class="fas fa-project-diagram"></i>
                <span>Active Projects Monitoring</span>
            </div>
            <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 4px; font-size: 0.9rem;">
                {{ $skProjects->count() }} Total
            </span>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Budget</th>
                        <th>Spent</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skProjects as $project)
                    <tr>
                        <td style="font-weight: 600; color: #1F2937;">{{ $project->title }}</td>
                        <td>₱{{ number_format($project->budget, 2) }}</td>
                        <td class="{{ $project->amount_spent > $project->budget ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format($project->amount_spent, 2) }}
                        </td>
                        <td>
                            @php
                                $badgeClass = 'icon-blue-bg';
                                if($project->status == 'Completed') $badgeClass = 'icon-green-bg';
                                if($project->status == 'Pending') $badgeClass = 'icon-orange-bg';
                            @endphp
                            <span style="color: white; padding: 4px 12px; border-radius: 6px; font-size: 0.8rem;" class="{{ $badgeClass }}">
                                {{ $project->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div style="color: #9CA3AF;">
                                <i class="fas fa-folder-open" style="font-size: 2rem; margin-bottom: 10px;"></i>
                                <p>No projects found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- SIDE COLUMN: OFFICIALS --}}
    <div class="side-column">
        <div class="side-card">
            <div class="side-header">
                {{-- UPDATED ICON COLOR --}}
                <i class="fas fa-users-cog" style="color: #2B5CE6;"></i>
                <span>SK Officials</span>
            </div>
            <div class="side-body">
                @forelse($officials as $official)
                <div class="official-item">
                    <div class="official-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="official-info">
                        <h6>{{ $official->resident->first_name }} {{ $official->resident->last_name }}</h6>
                        <small>{{ $official->position }}</small>
                    </div>
                </div>
                @empty
                <div style="padding: 20px; text-align: center; color: #9CA3AF;">
                    No officials recorded.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection