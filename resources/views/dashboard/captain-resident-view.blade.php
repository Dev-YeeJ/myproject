{{-- resources/views/dashboard/captain-resident-view.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Resident Details - ' . $resident->first_name . ' ' . $resident->last_name)

@section('nav-items')
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
    /* (Most styles remain unchanged) */
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
    
    /* Style for the reset button */
    .btn-header-danger {
        border-color: #FFA500;
        color: #FFA500;
    }
    .btn-header-danger:hover {
        background: #FFA500;
        color: #1E3A8A;
    }

    .btn-header:hover {
        background: white;
        color: #2B5CE6;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
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
    
    .info-row:last-child {
        margin-bottom: 0; /* Remove margin from last row */
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
    
    /* --- MODIFIED PASSWORD STYLES --- */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #F9FAFB;
        border-radius: 6px;
        padding: 4px 8px;
    }
    .password-value {
        color: #D946EF; /* Fuchsia */
        font-weight: 700;
        font-family: monospace;
        font-size: 1.1rem;
    }
    .password-toggle {
        cursor: pointer;
        color: #6B7280;
        font-size: 1rem;
        margin-left: auto; /* Pushes it to the right */
        padding: 5px;
    }
    .password-toggle:hover {
        color: var(--primary-blue);
    }
    /* --- END OF MODIFIED STYLES --- */


    .badge-status {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        width: fit-content;
    }

    .badge-male { background: #DBEAFE; color: #1E40AF; }
    .badge-female { background: #FCE7F3; color: #BE185D; }
    .badge-head { background: #1E3A8A; color: white; }
    .badge-married { background: #FEF3C7; color: #92400E; }
    .badge-single { background: #F3F4F6; color: #4B5563; }
    .badge-green { background: #D1FAE5; color: #065F46; } /* Active */

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
        flex-shrink: 0;
    }

    .checkbox-icon.checked { background: #10B981; color: white; }
    .checkbox-icon.unchecked { background: #E5E7EB; color: #9CA3AF; }
    .checkbox-label { font-weight: 600; color: #374151; font-size: 0.9rem; }
    .checkbox-sub-label {
        font-weight: 500; color: #6B7280;
        font-size: 0.85rem; display: block;
        margin-top: 2px;
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

    .back-link:hover { gap: 12px; color: #1E3A8A; }

    @media (max-width: 992px) {
        .details-grid { grid-template-columns: 1fr; }
        .details-header { flex-direction: column; align-items: flex-start; gap: 20px; }
        .header-actions { width: 100%; }
        .btn-header { flex: 1; justify-content: center; }
        .info-row { grid-template-columns: 1fr; }
    }
</style>

<div class="details-container">

    <a href="{{ route('captain.resident-profiling', ['view' => request('view', 'residents')]) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Residents</span>
    </a>

    {{-- Display Success/Error messages for password reset --}}
    @if(session('success'))
    <div class="alert alert-success" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-times-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif


    <div class="details-header">
        <div class="header-left">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="header-info">
                <h1>{{ $resident->full_name }}</h1>
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
            <a href="{{ route('captain.resident.edit', $resident->id) }}" class="btn-header">
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
        
        {{-- LEFT COLUMN --}}
        <div>
            <div class="details-card" style="margin-bottom: 25px;">
                <div class="card-title">
                    <i class="fas fa-user"></i>
                    <span>Personal Information</span>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $resident->full_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $resident->date_of_birth ? $resident->date_of_birth->format('F j, Y') : 'N/A' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value">{{ $resident->age }} years old</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value">
                            <span class="badge-status {{ $resident->gender === 'Male' ? 'badge-male' : 'badge-female' }}">
                                {{ $resident->gender }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="info-row single">
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
                        <div class="info-label">Household</div>
                        <div class="info-value">{{ $resident->household ? $resident->household->household_name : 'Not Assigned' }}</div>
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
            
            {{-- MODIFIED: USER ACCOUNT CARD --}}
            <div class="details-card">
                <div class="card-title">
                    <i class="fas fa-user-shield" style="color: #6366F1;"></i>
                    <span>User Account</span>
                </div>
                @if($resident->user)
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">Username</div>
                            <div class="info-value">{{ $resident->user->username }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">
                                <span class="badge-status {{ $resident->user->is_active ? 'badge-green' : 'badge-single' }}">
                                    {{ $resident->user->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- NEW: DEFAULT PASSWORD with TOGGLE --}}
                    <div class="info-row single">
                        <div class="info-item">
                            <div class="info-label">Default Password</div>
                            <div class="password-wrapper" title="This is the default password. The user may have changed it.">
                                <span id="defaultPassword" 
                                     class="password-value"
                                     data-password="{{ $defaultPassword ?? 'N/A' }}">
                                    &bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;
                                </span>
                                <i id="togglePassword" class="fas fa-eye password-toggle"></i>
                            </div>
                        </div>
                    </div>

                    {{-- NEW: RESET PASSWORD BUTTON --}}
                    <form action="{{ route('captain.resident.reset-password', $resident->id) }}" method="POST" style="margin-top: 20px;">
                        @csrf
                        <button type="submit" class="btn-header btn-header-danger" style="width: 100%; justify-content: center;"
                                onclick="return confirm('Are you sure you want to reset this resident\'s password to the default?');">
                            <i class="fas fa-key"></i>
                            <span>Reset Password</span>
                        </button>
                    </form>
                    
                @else
                    <div class="info-row single">
                        <div class="info-item">
                            <div class="info-label">Login</div>
                            <div class="info-value">No user account linked.</div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div>
            <div class="details-card" style="margin-bottom: 25px;">
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
                        <div class="checkbox-label">
                            Registered Voter
                            @if($resident->is_registered_voter && $resident->precinct_number)
                                <span class="checkbox-sub-label">
                                    Precinct: {{ $resident->precinct_number }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="checkbox-indicator">
                        <div class="checkbox-icon {{ $resident->is_pwd ? 'checked' : 'unchecked' }}">
                            <i class="fas {{ $resident->is_pwd ? 'fa-check' : 'fa-times' }}"></i>
                        </div>
                        <div class="checkbox-label">
                            Person with Disability
                            @if($resident->is_pwd)
                                @if($resident->pwd_id_number)
                                <span class="checkbox-sub-label">
                                    ID: {{ $resident->pwd_id_number }}
                                </span>
                                @endif
                                @if($resident->disability_type)
                                <span class="checkbox-sub-label">
                                    Type: {{ $resident->disability_type }}
                                </span>
                                @endif
                            @endif
                        </div>
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
                        <div class="checkbox-label">4Ps Beneficiary</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- NEW: JAVASCRIPT FOR PASSWORD TOGGLE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('defaultPassword');
        
        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                // Get the real password from the data attribute
                const realPassword = passwordField.getAttribute('data-password');
                
                // Check if password is currently hidden
                if (passwordField.textContent.includes('•')) {
                    // Show the password
                    passwordField.textContent = realPassword;
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    // Hide the password
                    passwordField.textContent = '••••••••••';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        }
    });
</script>

@endsection