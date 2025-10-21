<!-- ============================================
FILE: resources/views/dashboards/captain-resident-profiling.blade.php
DESCRIPTION: Captain Resident Profiling & Household Mapping View
============================================ -->

@extends('layouts.dashboard-layout')

@section('title', 'Resident Profiling')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('dashboard.captain') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.resident-profiling') }}" class="nav-link active">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Document Services</span>
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
    }

    .btn-add {
        background: #2B5CE6;
        color: white;
    }

    .btn-add:hover {
        background: #1E3A8A;
        transform: translateY(-2px);
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

    .tabs-section {
        background: white;
        border-radius: 12px;
        padding: 0;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .tabs-header {
        display: flex;
        border-bottom: 2px solid #E5E7EB;
        padding: 0 20px;
    }

    .tab-item {
        padding: 16px 24px;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        font-weight: 600;
        color: #666;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .tab-item.active {
        color: #2B5CE6;
        border-bottom-color: #2B5CE6;
    }

    .tab-item:hover {
        color: #2B5CE6;
    }

    .residents-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .residents-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .filters-section {
        display: flex;
        gap: 12px;
    }

    .search-input {
        padding: 10px 16px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        width: 280px;
        font-size: 0.95rem;
    }

    .filter-select {
        padding: 10px 16px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 0.95rem;
        background: white;
        min-width: 140px;
    }

    .residents-table {
        background: white;
        border-radius: 0 0 12px 12px;
        overflow: hidden;
    }

    .table {
        width: 100%;
        margin: 0;
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
        border-bottom: 2px solid #E5E7EB;
    }

    .table td {
        padding: 18px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #F3F4F6;
    }

    .table tbody tr:hover {
        background: #F9FAFB;
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
        background: #F3F4F6;
        color: #4B5563;
    }

    .badge-child {
        background: #F3F4F6;
        color: #4B5563;
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
    }

    .action-icon.view {
        color: #2B5CE6;
    }

    .action-icon.view:hover {
        background: #EFF6FF;
    }

    .action-icon.edit {
        color: #10B981;
    }

    .action-icon.edit:hover {
        background: #ECFDF5;
    }

    .action-icon.delete {
        color: #EF4444;
    }

    .action-icon.delete:hover {
        background: #FEE2E2;
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

        .residents-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .filters-section {
            flex-direction: column;
            width: 100%;
        }

        .search-input,
        .filter-select {
            width: 100%;
        }
    }
</style>

<div class="profiling-header">
    <div class="profiling-title">Resident Profiling & Household Mapping</div>
    <div class="profiling-subtitle">Manage comprehensive resident information and household data</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Bantug, Malasiqui, Pangasinan</span>
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

<div class="action-buttons">
    <button class="btn-action btn-add">
        <i class="fas fa-user-plus"></i>
        <span>Add Resident</span>
    </button>
    <button class="btn-action btn-secondary">
        <i class="fas fa-home"></i>
        <span>Add Household</span>
    </button>
    <button class="btn-action btn-secondary">
        <i class="fas fa-download"></i>
        <span>Export Data</span>
    </button>
</div>

<div class="tabs-section">
    <div class="tabs-header">
        <div class="tab-item active">
            <i class="fas fa-users"></i>
            <span>Residents</span>
        </div>
        <div class="tab-item">
            <i class="fas fa-home"></i>
            <span>Households</span>
        </div>
        <div class="tab-item">
            <i class="fas fa-map-marked-alt"></i>
            <span>Map View</span>
        </div>
    </div>
</div>

<div class="residents-section">
    <div class="residents-title">
        <i class="fas fa-users"></i>
        <span>Resident Directory ({{ $totalResidents }})</span>
    </div>
    <div class="filters-section">
        <input type="text" class="search-input" placeholder="🔍 Search residents...">
        <select class="filter-select">
            <option>All Status</option>
            <option>Household Head</option>
            <option>Spouse</option>
            <option>Child</option>
        </select>
        <select class="filter-select">
            <option>All</option>
            <option>Male</option>
            <option>Female</option>
        </select>
    </div>
</div>

<div class="residents-table">
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($residents as $resident)
            <tr>
                <td>
                    <div class="resident-name">{{ $resident->first_name }} {{ $resident->last_name }}</div>
                    <div class="resident-suffix">{{ $resident->suffix }}</div>
                </td>
                <td>{{ $resident->age }}</td>
                <td>
                    <span class="badge {{ $resident->gender === 'Male' ? 'badge-male' : 'badge-female' }}">
                        {{ $resident->gender }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $resident->household_status === 'Household Head' ? 'badge-head' : ($resident->household_status === 'Spouse' ? 'badge-spouse' : 'badge-child') }}">
                        {{ $resident->household_status }}
                    </span>
                </td>
                <td>{{ $resident->address }}</td>
                <td>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span>{{ $resident->contact_number }}</span>
                    </div>
                </td>
                <td>{{ $resident->occupation }}</td>
                <td>
                    <div class="action-icons">
                        <button class="action-icon view" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-icon edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-icon delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                    <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
                    <p style="color: #999;">No residents found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection