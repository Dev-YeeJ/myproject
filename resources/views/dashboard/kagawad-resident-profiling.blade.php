@extends('layouts.dashboard-layout')

@section('title', 'Resident Profiling (View Only)')

@section('nav-items')
    {{-- Kagawad Specific Navigation --}}
    <li class="nav-item">
        <a href="{{ route('kagawad.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.residents') }}" class="nav-link active">
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
            <span>Blotter & Lupon</span>
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
    /* Re-using Captain's CSS for consistency */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2);
        padding: 8px 16px; border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }
    .total-registered { position: absolute; top: 40px; right: 40px; text-align: right; }
    .total-registered-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .total-registered-count { font-size: 2.5rem; font-weight: 700; }
    .total-registered-sublabel { font-size: 0.85rem; opacity: 0.9; }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
    }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }
    .stat-badge.purple { color: #A855F7; }
    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-purple-bg { background: #A855F7; }

    /* View Toggle Buttons */
    .view-toggles {
        margin-bottom: 30px; display: flex; gap: 0; 
        border-radius: 10px; overflow: hidden; border: 2px solid #2B5CE6; width: fit-content;
    }
    .btn-toggle {
        padding: 12px 24px; border: none; font-weight: 600;
        display: flex; align-items: center; gap: 10px; cursor: pointer;
        transition: all 0.3s; font-size: 0.95rem; text-decoration: none;
        background: white; color: #2B5CE6;
    }
    .btn-toggle.active { background: #2B5CE6; color: white; }
    .btn-toggle:not(.active):hover { background: #EFF6FF; }

    /* Search and Filter */
    .filters-section { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
    .search-input {
        padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.95rem; background: white; min-width: 200px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .search-input:focus { border-color: #2B5CE6; outline: none; }
    
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 20px 30px; border-radius: 12px 12px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .directory-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 700; }
    .filter-select {
        padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.95rem; background: white; min-width: 140px;
    }
    .filter-select:focus { border-color: #2B5CE6; outline: none; }

    /* Table Styles */
    .table { width: 100%; margin: 0; border-collapse: collapse; }
    .table thead { background: #F9FAFB; }
    .table th {
        padding: 16px 20px; font-weight: 700; color: #1F2937;
        font-size: 0.9rem; text-transform: uppercase; text-align: left;
        border-bottom: 2px solid #E5E7EB;
    }
    .table td {
        padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F3F4F6; text-align: left;
    }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover { background: #F9FAFB; }
    .table-container {
        background: white; border-radius: 0 0 12px 12px; overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .resident-name { font-weight: 600; color: #1F2937; margin: 0 0 4px 0; }
    .resident-suffix { color: #6B7280; font-size: 0.9rem; }
    
    .badge { padding: 6px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-block; }
    .badge-male { background: #DBEAFE; color: #1E40AF; }
    .badge-female { background: #FCE7F3; color: #BE185D; }
    .badge-head { background: #1E3A8A; color: white; }
    .badge-spouse { background: #FEF3C7; color: #92400E; }
    .badge-child { background: #E0E7FF; color: #3730A3; }
    .badge-member { background: #F3F4F6; color: #4B5563; }
    .badge-green { background: #D1FAE5; color: #065F46; }
    .badge-orange { background: #FEF3C7; color: #92400E; }

    .contact-info { display: flex; align-items: center; gap: 8px; color: #6B7280; }
    .household-info-cell .name { font-weight: 600; color: #1F2937; }
    .household-info-cell .head { font-size: 0.85rem; color: #6B7280; margin-top: 4px; }
    .pagination-container { padding: 20px; background: white; border-radius: 0 0 12px 12px; }
    .no-results-found { text-align: center; padding: 60px; }
    .no-results-found i { font-size: 3rem; color: #ccc; margin-bottom: 15px; }
    .no-results-found p { color: #999; font-size: 1.1rem; }

    @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .total-registered { position: static; margin-top: 20px; text-align: left; }
        .directory-header, .filters-section { flex-direction: column; align-items: flex-start; gap: 15px; }
        .search-input, .filter-select { width: 100%; }
    }
</style>

@php $view = request('view', 'residents'); @endphp
@php $filter = request('filter'); @endphp

<div class="profiling-header">
    <div class="profiling-title">Resident Profiling</div>
    <div class="profiling-subtitle">View Only Access - Resident & Household Data</div>
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
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $totalHouseholds }}</h3>
            <p>Total Households</p>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-home"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $seniorCitizens }}</h3>
            <p>Senior Citizens</p>
        </div>
        <div class="stat-box-icon icon-green-bg"><i class="fas fa-heart"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $minors }}</h3>
            <p>Minors</p>
        </div>
        <div class="stat-box-icon icon-purple-bg"><i class="fas fa-child"></i></div>
    </div>
</div>

<div class="view-toggles">
    <a href="{{ route('kagawad.residents', ['view' => 'residents']) }}" class="btn-toggle {{ $view === 'residents' ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Resident Directory</span>
    </a>
    <a href="{{ route('kagawad.residents', ['view' => 'households']) }}" class="btn-toggle {{ $view === 'households' ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Household Directory</span>
    </a>
</div>

@if($view === 'households')

    <div class="directory-header">
        <div class="directory-title">
            <i class="fas fa-home"></i>
            <span>Household Directory ({{ $totalHouseholds }})</span>
        </div>
        {{-- Form submits to Kagawad Route --}}
        <form method="GET" action="{{ route('kagawad.residents') }}" class="filters-section">
            <input type="hidden" name="view" value="households">
            <input type="text" name="search" class="search-input" placeholder="🔍 Search households..." value="{{ request('search') }}">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
            </select>
        </form>
    </div>

    <div class="table-container" style="border-radius: 0 0 12px 12px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Household Name & Head</th>
                    <th>Address</th>
                    <th>Purok</th>
                    <th>Total Members</th>
                    <th>Status</th>
                    {{-- Actions Column Removed for View Only --}}
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
                </tr>
                @empty
                <tr>
                    <td colspan="5">
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

    <div class="directory-header">
        <div class="directory-title">
            <i class="fas fa-users"></i>
            <span>Resident Directory ({{ $totalResidents }})</span>
        </div>
        {{-- Form submits to Kagawad Route --}}
        <form method="GET" action="{{ route('kagawad.residents') }}" class="filters-section">
            <input type="hidden" name="view" value="residents">
            <input type="text" name="search" class="search-input" placeholder="🔍 Search residents..." value="{{ request('search') }}">
            
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
        </form>
    </div>

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
                    <th>Occupation</th>
                    {{-- Actions Column Removed for View Only --}}
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
                            $statusClass = 'badge-member'; 
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
                    <td>{{ $resident->occupation ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
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

@endsection