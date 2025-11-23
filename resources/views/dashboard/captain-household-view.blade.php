{{-- resources/views/dashboards/captain-household-view.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Household Details - ' . $household->household_name)

@section('nav-items')
    {{-- (Nav items remain unchanged) --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link">
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
        <a href="{{ route('captain.document-services') }}" class="nav-link">
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
        <a href="{{ route('captain.health-services') }}" class="nav-link">
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
    /* (Using the blue-themed styles from captain-resident-view) */
    .details-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .details-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 30px 40px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .header-info h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-badges {
        display: flex;
        gap: 10px;
    }

    .header-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-header {
        padding: 10px 20px;
        border-radius: 8px;
        border: 2px solid white;
        background: transparent;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-header:hover {
        background: white;
        color: #2B5CE6;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr; /* Single column for this view */
        gap: 25px;
        margin-bottom: 25px;
    }

    .details-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        border-bottom: 2px solid #E5E7EB;
    }

    .card-title i {
        color: #2B5CE6;
        font-size: 1.5rem;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Three columns for household info */
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-row.single {
        grid-template-columns: 1fr;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.85rem;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: #1F2937;
        font-weight: 500;
    }

    .info-value.highlight {
        color: #2B5CE6;
        font-weight: 600;
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        width: fit-content;
    }

    .badge-green {
        background: #D1FAE5;
        color: #065F46;
    }
    .badge-orange {
        background: #FEF3C7;
        color: #92400E;
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


    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2B5CE6;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .back-link:hover {
        gap: 12px;
        color: #1E3A8A;
    }

    /* Table styles from profiling page */
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        margin-top: 25px;
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
    .resident-name {
        font-weight: 600;
        color: #1F2937;
        margin: 0 0 4px 0;
    }
    .resident-suffix {
        color: #6B7280;
        font-size: 0.9rem;
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

    @media (max-width: 768px) {
        .details-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        .header-actions {
            width: 100%;
        }
        .btn-header {
            flex: 1;
            justify-content: center;
        }
        .info-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="details-container">
    <a href="{{ route('captain.resident-profiling', ['view' => 'households']) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Household Directory</span>
    </a>

    <div class="details-header">
        <div class="header-left">
            <div class="profile-avatar">
                <i class="fas fa-home"></i>
            </div>
            <div class="header-info">
                <h1>{{ $household->household_name }}</h1>
                <div class="header-badges">
                    <span class="header-badge">{{ $household->household_number }}</span>
                    <span class="header-badge">{{ $household->total_members }} Members</span>
                    <span class="header-badge">Purok {{ $household->purok ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('captain.household.edit', $household->id) }}" class="btn-header">
                <i class="fas fa-edit"></i>
                <span>Edit Household</span>
            </a>
            <a href="{{ route('captain.resident.create', ['household_id' => $household->id]) }}" class="btn-header" style="background: white; color: #2B5CE6;">
                <i class="fas fa-user-plus"></i>
                <span>Add Member</span>
            </a>
        </div>
    </div>

    <div class="details-grid">
        <div>
            <div class="details-card">
                <div class="card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Household Information</span>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Household Head</div>
                        <div class="info-value highlight">
                            {{ $household->head ? $household->head->full_name : 'No Head Assigned' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Data Status</div>
                        <div class="info-value">
                            <span class="badge-status {{ $household->status === 'complete' ? 'badge-green' : 'badge-orange' }}">
                                {{ ucfirst($household->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Members</div>
                        <div class="info-value">{{ $household->total_members }}</div>
                    </div>
                </div>

                <div class="info-row single">
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $household->address }}</div>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <div class="card-title" style="padding: 30px 30px 15px 30px; margin-bottom: 0; border-bottom: 2px solid #E5E7EB;">
                    <i class="fas fa-users"></i>
                    <span>Active Household Members</span>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Occupation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($household->activeResidents as $resident)
                        <tr>
                            <td>
                                <div class="resident-name">{{ $resident->first_name }} {{ $resident->last_name }}</div>
                                @if($resident->suffix)
                                <div class="resident-suffix">{{ $resident->suffix }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClass = 'badge-member'; // Default
                                    if ($resident->household_status === 'Household Head') $statusClass = 'badge-head';
                                    if ($resident->household_status === 'Spouse') $statusClass = 'badge-spouse';
                                    if ($resident->household_status === 'Child') $statusClass = 'badge-child';
                                @endphp
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $resident->household_status }}
                                </span>
                            </td>
                            <td>{{ $resident->age }}</td>
                            <td>{{ $resident->gender }}</td>
                            <td>{{ $resident->occupation ?? 'N/A' }}</td>
                            <td>
                                <div class="action-icons">
                                    <a href="{{ route('captain.resident.show', $resident->id) }}" class="action-icon view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('captain.resident.edit', $resident->id) }}" class="action-icon edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- You might want a delete modal here --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="no-results-found">
                                    <i class="fas fa-users"></i>
                                    <p>No active residents found for this household.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection