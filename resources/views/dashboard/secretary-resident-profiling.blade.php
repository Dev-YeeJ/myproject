@extends('layouts.dashboard-layout')

@section('title', 'Resident Profiling')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('secretary.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.resident-profiling') }}" class="nav-link active">
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
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
    }

    .profiling-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .profiling-subtitle {
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
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .stat-content p {
        color: #666;
        margin: 0 0 8px 0;
        font-size: 0.95rem;
    }

    .stat-badge {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }
    .stat-badge.purple { color: #A855F7; }

    .stat-box-icon {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }

    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-purple-bg { background: #A855F7; }
    
    /* --- REMOVED: Control Panel Card --- */

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 30px;
    }

    .btn-action {
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.95rem;
        text-decoration: none;
    }

    .btn-add {
        background: #2B5CE6;
        color: white;
    }

    .btn-add:hover {
        background: #1E3A8A;
        transform: translateY(-2px);
        color: white;
    }
    
    .btn-add-household {
        background: #10B981;
        color: white;
    }
    
    .btn-add-household:hover {
        background: #059669;
        transform: translateY(-2px);
        color: white;
    }

    .btn-secondary {
        background: white;
        color: #333;
        border: 2px solid #E5E7EB;
    }

    .btn-secondary:hover {
        border-color: #2B5CE6;
        color: #2B5CE6;
        transform: translateY(-2px);
    }

    /* --- View Toggle Buttons --- */
    .view-toggles {
        margin-bottom: 30px; 
        display: flex; 
        gap: 0; 
        border-radius: 10px; 
        overflow: hidden; 
        border: 2px solid #2B5CE6; 
        width: fit-content;
    }
    .btn-toggle {
        padding: 12px 24px;
        border: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.95rem;
        text-decoration: none;
        background: white;
        color: #2B5CE6;
    }
    .btn-toggle.active {
        background: #2B5CE6;
        color: white;
    }
    .btn-toggle:not(.active):hover {
        background: #EFF6FF;
    }
    /* --- End View Toggle Buttons --- */

    /* --- DELETED: Quick Filter Buttons --- */


    /* Search and Filter Section */
    .filters-section {
        display: flex;
        align-items: center;
        flex-wrap: wrap; /* Allow filters to wrap */
        gap: 10px;
    }

    /* Adjusted .search-input to match .filter-select style */
    .search-input {
        padding: 10px 16px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        min-width: 200px; /* Adjust as needed */
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .search-input:focus {
        border-color: #2B5CE6;
        box-shadow: 0 0 0 3px rgba(43, 92, 230, 0.2);
        outline: none;
    }


    /* RESTORED: Directory Header */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
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
    
    .filter-select {
        padding: 10px 16px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        min-width: 140px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .filter-select:focus {
        border-color: #2B5CE6;
        box-shadow: 0 0 0 3px rgba(43, 92, 230, 0.2);
        outline: none;
    }

    /* Table styles remain largely the same */
    .residents-table { /* Now used for both directories */
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .table thead {
        background: #F9FAFB;
    }

    .table th {
        padding: 16px 20px;
        font-weight: 700;
        color: #1F2937;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        border-bottom: 2px solid #E5E7EB;
    }

    .table td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #F3F4F6;
        text-align: left;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background: #F9FAFB;
    }

    /* Table Container (for both directories) */
    .table-container {
        background: white;
        border-radius: 0 0 12px 12px; /* MODIFIED: Connects to blue bar */
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }

    .resident-name {
        font-weight: 600;
        color: #1F2937;
        margin: 0 0 4px 0;
    }

    .resident-suffix {
        color: #6B7280;
        font-size: 0.9rem;
    }

    .badge {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-male {
        background: #DBEAFE;
        color: #1E40AF;
    }

    .badge-female {
        background: #FCE7F3;
        color: #BE185D;
    }

    .badge-head {
        background: #1E3A8A;
        color: white;
    }

    .badge-spouse {
        background: #FEF3C7;
        color: #92400E;
    }

    .badge-child {
        background: #E0E7FF;
        color: #3730A3;
    }
    
    .badge-member {
        background: #F3F4F6;
        color: #4B5563;
    }

    .badge-green {
        background: #D1FAE5;
        color: #065F46;
    }
    .badge-orange {
        background: #FEF3C7;
        color: #92400E;
    }


    .contact-info {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6B7280;
    }

    .action-icons {
        display: flex;
        gap: 12px;
    }

    .action-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        background: transparent;
        color: #6B7280;
    }

    .action-icon.view { color: #2B5CE6; }
    .action-icon.view:hover { background: #EFF6FF; }
    .action-icon.edit { color: #10B981; }
    .action-icon.edit:hover { background: #ECFDF5; }
    .action-icon.delete { color: #EF4444; }
    .action-icon.delete:hover { background: #FEE2E2; }

    .household-info-cell .name {
        font-weight: 600;
        color: #1F2937;
    }
    .household-info-cell .head {
        font-size: 0.85rem;
        color: #6B7280;
        margin-top: 4px;
    }
    .household-info-cell .address {
        font-size: 0.85rem;
        color: #6B7280;
    }


    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #D1FAE5;
        color: #065F46;
        border: 1px solid #6EE7B7;
    }
    
    .pagination-container {
        padding: 20px;
        background: white;
        border-radius: 0 0 12px 12px;
    }
    
    .no-results-found {
        text-align: center;
        padding: 60px;
    }
    .no-results-found i {
        font-size: 3rem; 
        color: #ccc; 
        margin-bottom: 15px;
    }
    .no-results-found p {
        color: #999;
        font-size: 1.1rem;
    }


    @media (max-width: 1200px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
        .total-registered {
            position: static;
            margin-top: 20px;
            text-align: left;
        }
        .directory-header, .filters-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .search-input,
        .filter-select {
            width: 100%;
        }
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
    }
    .modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    .modal-icon {
        width: 48px;
        height: 48px;
        background: #FEE2E2;
        color: #EF4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .modal-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1F2937;
    }
    .modal-body {
        margin-bottom: 25px;
        color: #6B7280;
        line-height: 1.6;
    }
    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    .btn-cancel {
        padding: 10px 20px;
        background: #F3F4F6;
        color: #4B5563;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel:hover { background: #E5E7EB; }
    .btn-confirm-delete {
        padding: 10px 20px;
        background: #EF4444;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-confirm-delete:hover { background: #DC2626; }
</style>

{{-- Get the current view from the request, default to 'residents' --}}
@php $view = request('view', 'residents'); @endphp
@php $filter = request('filter'); @endphp {{-- Get the active quick filter --}}

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="profiling-header">
    <div class="profiling-title">Resident Profiling & Household Mapping</div>
    <div class="profiling-subtitle">Manage comprehensive resident information and household data</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
    </div>
    <div class="total-registered">
        <div class="total-registered-label">Total Registered</div>
        <div class="total-registered-count">{{ $totalResidents }}</div>
        <div class="total-registered-sublabel">Active Residents</div>
    </div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $totalResidents }}</h3>
            <p>Total Residents</p>
            <div class="stat-badge blue">
                <i class="fas fa-arrow-up"></i>
                <span>+12 this month</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $totalHouseholds }}</h3>
            <p>Total Households</p>
            <div class="stat-badge orange">
                <i class="fas fa-home"></i>
                <span>{{ $completeHouseholds }} complete</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-home"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $seniorCitizens }}</h3>
            <p>Senior Citizens</p>
            <div class="stat-badge green">
                <i class="fas fa-heart"></i>
                <span>60+ years old</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-heart"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $minors }}</h3>
            <p>Minors</p>
            <div class="stat-badge purple">
                <i class="fas fa-child"></i>
                <span>Under 18 years</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
        <i class="fas fa-graduation-cap"></i>
    </div>
    </div>
</div>

{{-- NEW: Second Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $totalVoters }}</h3>
            <p>Total Voters</p>
            <div class="stat-badge blue">
                <i class="fas fa-check-to-slot"></i>
                <span>Registered</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-check-to-slot"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $totalPwd }}</h3>
            <p>Total PWD</p>
            <div class="stat-badge purple">
                <i class="fas fa-wheelchair"></i>
                <span>Tagged</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-wheelchair"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $total4ps }}</h3>
            <p>Total 4Ps Beneficiaries</p>
            <div class="stat-badge green">
                <i class="fas fa-hand-holding-heart"></i>
                <span>Beneficiaries</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-hand-holding-heart"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $incompleteHouseholds }}</h3>
            <p>Incomplete Households</p>
            <div class="stat-badge orange">
                <i class="fas fa-exclamation-triangle"></i>
                <span>No Head Assigned</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    </div>
</div>

{{-- MODIFICATION: Reverted to old layout for buttons --}}
<div class="action-buttons">
    <a href="{{ route('secretary.resident.create') }}" class="btn-action btn-add">
        <i class="fas fa-user-plus"></i>
        <span>Add Resident</span>
    </a>
    <a href="{{ route('secretary.household.create') }}" class="btn-action btn-add-household">
        <i class="fas fa-home"></i>
        <span>Add Household</span>
    </a>
    <button class="btn-action btn-secondary">
        <i class="fas fa-download"></i>
        <span>Export Data</span>
    </button>
</div>

{{-- MODIFICATION: Reverted to old layout for toggles --}}
<div class="view-toggles">
    <a href="{{ route('secretary.resident-profiling', ['view' => 'residents']) }}" class="btn-toggle {{ $view === 'residents' ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Resident Directory</span>
    </a>
    <a href="{{ route('secretary.resident-profiling', ['view' => 'households']) }}" class="btn-toggle {{ $view === 'households' ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Household Directory</span>
    </a>
</div>

{{-- MODIFICATION: DELETED .control-panel-card wrapper --}}


@if($view === 'households')

    {{-- MODIFICATION: Re-added .directory-header --}}
    <div class="directory-header">
        <div class="directory-title">
            <i class="fas fa-home"></i>
            <span>Household Directory ({{ $totalHouseholds }})</span>
        </div>
        <form method="GET" action="{{ route('secretary.resident-profiling') }}" class="filters-section">
            <input type="hidden" name="view" value="households">
            <input type="text" name="search" class="search-input" placeholder="🔍 Search by household or head name..." value="{{ request('search') }}">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
            </select>
        </form>
    </div>

    {{-- MODIFICATION: Fixed border-radius --}}
    <div class="table-container" style="border-radius: 0 0 12px 12px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Household Name & Head</th>
                    <th>Address</th>
                    <th>Purok</th>
                    <th>Total Members</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($households as $household)
                <tr>
                    <td>
                        <div class="household-info-cell">
                            <div class="name">{{ $household->household_name ?? 'Household ' . $household->id }}</div>
                            @if($household->head)
                                <div class="head">Head: {{ $household->head->first_name }} {{ $household->head->last_name }}</div>
                            @else
                                <div class="head">No Head Assigned</div>
                            @endif
                        </div>
                    </td>
                    <td>{{ $household->address }}</td>
                    <td>{{ $household->purok ?? 'N/A' }}</td>
                    <td>{{ $household->total_members }}</td>
                    <td>
                        <span class="badge {{ $household->status === 'complete' ? 'badge-green' : 'badge-orange' }}">
                            {{ ucfirst($household->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-icons">
                            <a href="{{ route('secretary.household.show', $household->id) }}" class="action-icon view" title="View Household Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('secretary.household.edit', $household->id) }}" class="action-icon edit" title="Edit Household">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('secretary.resident.create', ['household_id' => $household->id]) }}" class="action-icon" style="color: #0d6efd;" title="Add Member">
                                <i class="fas fa-user-plus"></i>
                            </a>
                            <button class="action-icon delete" title="Delete Household" onclick="showDeleteHouseholdModal({{ $household->id }}, '{{ $household->household_name ?? 'Household ' . $household->id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="no-results-found">
                            <i class="fas fa-home"></i>
                            <p>No households found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination-container">
            {{ $households->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
    

@else

    {{-- MODIFICATION: Re-added .directory-header and added the new Category Dropdown --}}
    <div class="directory-header">
        <div class="directory-title">
            <i class="fas fa-users"></i>
            <span>Resident Directory ({{ $totalResidents }})</span>
        </div>
        <form method="GET" action="{{ route('secretary.resident-profiling') }}" class="filters-section">
            <input type="hidden" name="view" value="residents">
            <input type="text" name="search" class="search-input" placeholder="🔍 Search residents..." value="{{ request('search') }}">
            
            {{-- This is the new "Category" dropdown --}}
            <select name="filter" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="seniors" {{ $filter == 'seniors' ? 'selected' : '' }}>Seniors</option>
                <option value="pwd" {{ $filter == 'pwd' ? 'selected' : '' }}>PWD</option>
                <option value="4ps" {{ $filter == '4ps' ? 'selected' : '' }}>4Ps</option>
                <option value="voters" {{ $filter == 'voters' ? 'selected' : '' }}>Voters</option>
            </select>
            
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="Household Head" {{ request('status') == 'Household Head' ? 'selected' : '' }}>Household Head</option>
                <option value="Spouse" {{ request('status') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                <option value="Child" {{ request('status') == 'Child' ? 'selected' : '' }}>Child</option>
                <option value="Member" {{ request('status') == 'Member' ? 'selected' : '' }}>Member</option>
            </select>
            <select name="gender" class="filter-select" onchange="this.form.submit()">
                <option value="">All Genders</option>
                <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </form>
    </div>

    {{-- MODIFICATION: Fixed border-radius --}}
    <div class="table-container" style="border-radius: 0 0 12px 12px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>User Account</th> 
                    <th>Occupation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($residents as $resident)
                <tr>
                    <td>
                        <div class="resident-name">{{ $resident->first_name }} {{ $resident->last_name }}</div>
                        @if($resident->suffix)
                        <div class="resident-suffix">{{ $resident->suffix }}</div>
                        @endif
                    </td>
                    <td>{{ $resident->age }}</td>
                    <td>
                        <span class="badge {{ $resident->gender === 'Male' ? 'badge-male' : 'badge-female' }}">
                            {{ $resident->gender }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = 'badge-member'; // Default
                            if ($resident->household_status === 'Household Head') $statusClass = 'badge-head';
                            if ($resident->household_status === 'Spouse') $statusClass = 'badge-spouse';
                            if ($resident->household_status === 'Child') $statusClass = 'badge-child';
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ $resident->household_status }}
                        </span>
                    </td>
                    <td>{{ $resident->address }}</td>
                    <td>
                        <div class="contact-info">
                            <i class="fas fa-phone"></i>
                            <span>{{ $resident->contact_number ?? 'N/A' }}</span>
                        </div>
                    </td>
                    
                    <td>
                        @if($resident->user)
                            <div class="contact-info">
                                <i class="fas fa-user-check" style="color: #10B981;"></i>
                                <span>{{ $resident->user->username }}</span>
                            </div>
                            <small style="color: #6B7280; margin-left: 20px;">({{ $resident->user->is_active ? 'Active' : 'Disabled' }})</small>
                        @else
                            <span class="badge badge-member">
                                No Account
                            </span>
                        @endif
                    </td>
                    
                    <td>{{ $resident->occupation ?? 'N/A' }}</td>
                    <td>
                        <div class="action-icons">
                            <a href="{{ route('captain.resident.show', $resident->id) }}" class="action-icon view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('captain.resident.edit', $resident->id) }}" class="action-icon edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="action-icon delete" title="Delete" onclick="showDeleteModal({{ $resident->id }}, '{{ $resident->first_name }} {{ $resident->last_name }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="no-results-found">
                            <i class="fas fa-users"></i>
                            <p>No residents found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination-container">
            {{ $residents->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
    

@endif


<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Remove Resident</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove <strong id="residentName"></strong> from the system?</p>
            <p>This will also deactivate their login account. The data will be kept for records.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Remove Resident</button>
            </form>
        </div>
    </div>
</div>

<div id="deleteHouseholdModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Delete Household</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove <strong id="householdName"></strong>?</p>
            <p>This will remove the household and **deactivate all associated residents'** login accounts. This action is permanent.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteHouseholdModal()">Cancel</button>
            <form id="deleteHouseholdForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Delete Household</button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- Resident Delete Modal ---
    function showDeleteModal(residentId, residentName) {
        document.getElementById('residentName').textContent = residentName;
        // Correct route: /captain/resident/{id}
        document.getElementById('deleteForm').action = `/secretary/resident/${residentId}`;
        document.getElementById('deleteModal').classList.add('show');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }

    // --- Household Delete Modal ---
    function showDeleteHouseholdModal(householdId, householdName) {
        document.getElementById('householdName').textContent = householdName;
        // Correct route: /captain/household/{id}
        document.getElementById('deleteHouseholdForm').action = `/secretary/household/${householdId}`;
        document.getElementById('deleteHouseholdModal').classList.add('show');
    }

    function closeDeleteHouseholdModal() {
        document.getElementById('deleteHouseholdModal').classList.remove('show');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const residentModal = document.getElementById('deleteModal');
        const householdModal = document.getElementById('deleteHouseholdModal');
        if (event.target === residentModal) {
            closeDeleteModal();
        }
        if (event.target === householdModal) {
            closeDeleteHouseholdModal();
        }
    }
</script>

@endsection