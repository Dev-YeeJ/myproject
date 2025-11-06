@extends('layouts.dashboard-layout')

@section('title', 'Resident Details')

{{-- THIS IS THE SECRETARY'S NAVIGATION --}}
@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('dashboard.secretary') }}" class="nav-link">
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
        <a href="#" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-search"></i>
            <span>Search Residents</span>
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
    /* All styles are copied directly from the captain's view */
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
        grid-template-columns: 2fr 1fr;
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
        grid-template-columns: repeat(2, 1fr);
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

    .badge-married {
        background: #FEF3C7;
        color: #92400E;
    }

    .badge-single {
        background: #F3F4F6;
        color: #4B5563;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .stat-label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #6B7280;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .stat-label i {
        width: 32px;
        height: 32px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2B5CE6;
    }

    .stat-value {
        font-weight: 700;
        color: #1F2937;
        font-size: 1.1rem;
    }

    .checkbox-indicators {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .checkbox-indicator {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #F9FAFB;
        border-radius: 8px;
    }

    .checkbox-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .checkbox-icon.checked {
        background: #10B981;
        color: white;
    }

    .checkbox-icon.unchecked {
        background: #E5E7EB;
        color: #9CA3AF;
    }

    .checkbox-label {
        font-weight: 600;
        color: #374151;
        font-size: 0.9rem;
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

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
        }

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
    {{-- UPDATED: Route points to secretary --}}
    <a href="{{ route('secretary.resident-profiling') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Residents</span>
    </a>

    <div class="details-header">
        <div class="header-left">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="header-info">
                <h1>{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}{{ $resident->suffix ? ' ' . $resident->suffix : '' }}</h1>
                <div class="header-badges">
                    <span class="header-badge">{{ $resident->age }} years old</span>
                    <span class="header-badge">{{ $resident->gender }}</span>
                    @if($resident->is_senior_citizen)
                    <span class="header-badge">Senior Citizen</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="header-actions">
            {{-- UPDATED: Route points to secretary --}}
            <a href="{{ route('secretary.resident.edit', $resident->id) }}" class="btn-header">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </a>
            <button class="btn-header" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
        </div>
    </div>

    <div class="details-grid">
        <div>
            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title">
                    <i class="fas fa-user"></i>
                    <span>Personal Information</span>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Suffix</div>
                        <div class="info-value">{{ $resident->suffix ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ date('F j, Y', strtotime($resident->date_of_birth)) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value highlight">{{ $resident->age }} years old</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value">
                            <span class="badge-status {{ $resident->gender === 'Male' ? 'badge-male' : 'badge-female' }}">
                                {{ $resident->gender }}
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Civil Status</div>
                        <div class="info-value">
                            <span class="badge-status {{ $resident->civil_status === 'Married' ? 'badge-married' : 'badge-single' }}">
                                {{ $resident->civil_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title">
                    <i class="fas fa-home"></i>
                    <span>Household Information</span>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Household Number</div>
                        <div class="info-value">{{ $resident->household ? $resident->household->household_number : 'Not Assigned' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Household Status</div>
                        <div class="info-value">
                            <span class="badge-status {{ $resident->household_status === 'Household Head' ? 'badge-head' : 'badge-single' }}">
                                {{ $resident->household_status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-row single">
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $resident->address }}</div>
                    </div>
                </div>

                @if($resident->household)
                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Purok</div>
                        <div class="info-value">{{ $resident->household->purok }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Household Members</div>
                        <div class="info-value highlight">{{ $resident->household->total_members }}</div>
                    </div>
                </div>
                @endif
            </div>

            <div class="details-card">
                <div class="card-title">
                    <i class="fas fa-briefcase"></i>
                    <span>Contact & Employment</span>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">{{ $resident->contact_number ?? 'Not Provided' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $resident->email ?? 'Not Provided' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Occupation</div>
                        <div class="info-value">{{ $resident->occupation ?? 'Not Specified' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Monthly Income</div>
                        <div class="info-value highlight">{{ $resident->monthly_income ? '₱' . number_format($resident->monthly_income, 2) : 'Not Disclosed' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    <span>Quick Stats</span>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">
                            <i class="fas fa-calendar"></i>
                            <span>Age</span>
                        </div>
                        <div class="stat-value">{{ $resident->age }}</div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-label">
                            <i class="fas fa-users"></i>
                            <span>Status</span>
                        </div>
                        <div class="stat-value" style="font-size: 0.85rem;">{{ $resident->household_status }}</div>
                    </div>

                    @if($resident->monthly_income)
                    <div class="stat-item">
                        <div class="stat-label">
                            <i class="fas fa-money-bill"></i>
                            <span>Income</span>
                        </div>
                        <div class="stat-value" style="font-size: 0.9rem;">₱{{ number_format($resident->monthly_income, 0) }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="details-card">
                <div class="card-title">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </div>

                <div class="checkbox-indicators">
                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_registered_voter ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_registered_voter ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">Registered Voter</div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_senior_citizen ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_senior_citizen ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">Senior Citizen</div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_pwd ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_pwd ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">Person with Disability</div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_indigenous ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_indigenous ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">Indigenous Person</div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_4ps ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_4ps ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">4Ps Beneficiary (Pantawid Pamilya)</div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->age < 18 ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->age < 18 ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">Minor (Under 18)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection