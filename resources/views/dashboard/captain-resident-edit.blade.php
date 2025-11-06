{{-- resources/views/dashboards/captain-resident-edit.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Edit Resident - ' . $resident->first_name . ' ' . $resident->last_name)

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
    /* Copied Styles from View Page */
    .details-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .details-header {
        /* UPDATED: Green theme for 'Edit' */
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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

    .header-info p {
        margin: 0;
        opacity: 0.9;
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
        font-size: 0.95rem;
    }
    
    .btn-header.btn-primary {
        background: white;
        color: #059669; /* Green */
        border-color: white;
    }
    .btn-header.btn-primary:hover {
        background: rgba(255,255,255,0.9);
        transform: translateY(-2px);
    }
    
    .btn-header.btn-secondary {
        background: transparent;
        color: white;
        border-color: rgba(255,255,255,0.8);
    }
    .btn-header.btn-secondary:hover {
        background: rgba(255,255,255,0.1);
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
        padding: 0; /* <<< MODIFICATION: Removed padding */
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden; /* <<< MODIFICATION: Added overflow hidden */
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
        padding: 30px 30px 15px 30px; /* <<< MODIFICATION: Added padding back here */
        margin-bottom: 0; /* <<< MODIFICATION: Removed margin */
    }

    .card-title i {
        color: #10B981; /* Green */
        font-size: 1.5rem;
    }

    /* <<< MODIFICATION: Added card-content wrapper style */
    .card-content {
        padding: 30px;
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

    /* Form Styles from Original Edit Page */
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row.single {
        grid-template-columns: 1fr;
    }

    .form-row.triple {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .form-row:last-child {
        margin-bottom: 0;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-group label.required::after {
        content: '*';
        color: #EF4444;
        margin-left: 4px;
    }

    .form-control {
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: #10B981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-control.error {
        border-color: #EF4444;
    }

    .error-message {
        color: #EF4444;
        font-size: 0.85rem;
        margin-top: 5px;
    }
    
    /* Special Checkbox styling for this layout */
    .checkbox-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #F9FAFB;
        padding: 12px;
        border-radius: 8px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #10B981;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #374151;
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
            flex-direction: column-reverse;
        }

        .btn-header {
            flex: 1;
            justify-content: center;
        }

        .form-row,
        .form-row.triple,
        .checkbox-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="details-container">
    <a href="{{ route('captain.resident.show', $resident->id) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Resident Details</span>
    </a>

    <form action="{{ route('captain.resident.update', $resident->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="details-header">
            <div class="header-left">
                <div class="profile-avatar">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="header-info">
                    <h1>Edit Resident</h1>
                    <p>Updating information for {{ $resident->first_name }} {{ $resident->last_name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('captain.resident.show', $resident->id) }}" class="btn-header btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn-header btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Update Resident</span>
                </button>
            </div>
        </div>

        <div class="details-grid">
            {{-- Left Column --}}
            <div>
                <div class="details-card" style="margin-bottom: 25px;">
                    <div class="card-title">
                        <i class="fas fa-user"></i>
                        <span>Personal Information</span>
                    </div>

                    {{-- <<< MODIFICATION: Added card-content wrapper --}}
                    <div class="card-content">
                        <div class="form-row triple">
                            <div class="form-group">
                                <label for="first_name" class="required">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') error @enderror" 
                                       value="{{ old('first_name', $resident->first_name) }}" required>
                                @error('first_name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" class="form-control" 
                                       value="{{ old('middle_name', $resident->middle_name) }}">
                            </div>

                            <div class="form-group">
                                <label for="last_name" class="required">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') error @enderror" 
                                       value="{{ old('last_name', $resident->last_name) }}" required>
                                @error('last_name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="suffix">Suffix</label>
                                <input type="text" id="suffix" name="suffix" class="form-control" 
                                       value="{{ old('suffix', $resident->suffix) }}" placeholder="Jr., Sr., III, etc.">
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth" class="required">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') error @enderror" 
                                       value="{{ old('date_of_birth', $resident->date_of_birth->format('Y-m-d')) }}" required>
                                @error('date_of_birth')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender" class="required">Gender</label>
                                <select id="gender" name="gender" class="form-control @error('gender') error @enderror" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', $resident->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $resident->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="civil_status" class="required">Civil Status</label>
                                <select id="civil_status" name="civil_status" class="form-control @error('civil_status') error @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="Single" {{ old('civil_status', $resident->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $resident->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $resident->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('civil_status', $resident->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="Divorced" {{ old('civil_status', $resident->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                                @error('civil_status')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="details-card">
                    <div class="card-title">
                        <i class="fas fa-home"></i>
                        <span>Household & Contact</span>
                    </div>

                    {{-- <<< MODIFICATION: Added card-content wrapper --}}
                    <div class="card-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="household_id">Household</label>
                                <select id="household_id" name="household_id" class="form-control">
                                    <option value="">Select Household (Optional)</option>
                                    @foreach($households as $household)
                                        <option value="{{ $household->id }}" {{ old('household_id', $resident->household_id) == $household->id ? 'selected' : '' }}>
                                            {{ $household->household_number }} - {{ $household->household_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="household_status" class="required">Household Status</label>
                                <select id="household_status" name="household_status" class="form-control @error('household_status') error @enderror" required>
                                    <option value="">Select Status</option>
                                    <option value="Household Head" {{ old('household_status', $resident->household_status) == 'Household Head' ? 'selected' : '' }}>Household Head</option>
                                    <option value="Spouse" {{ old('household_status', $resident->household_status) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="Child" {{ old('household_status', $resident->household_status) == 'Child' ? 'selected' : '' }}>Child</option>
                                    <option value="Member" {{ old('household_status', $resident->household_status) == 'Member' ? 'selected' : '' }}>Member</option>
                                </select>
                                @error('household_status')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row single">
                            <div class="form-group">
                                <label for="address" class="required">Address</label>
                                <input type="text" id="address" name="address" class="form-control @error('address') error @enderror" 
                                       value="{{ old('address', $resident->address) }}" placeholder="Block, Lot, Street" required>
                                @error('address')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" id="contact_number" name="contact_number" class="form-control" 
                                       value="{{ old('contact_number', $resident->contact_number) }}" placeholder="09XXXXXXXXX">
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="{{ old('email', $resident->email) }}" placeholder="example@email.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <div class="details-card">
                     <div class="card-title">
                        <i class="fas fa-briefcase"></i>
                        <span>Employment & Categories</span>
                    </div>
                    
                    {{-- <<< MODIFICATION: Added card-content wrapper --}}
                    <div class="card-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="occupation">Occupation</label>
                                <input type="text" id="occupation" name="occupation" class="form-control" 
                                       value="{{ old('occupation', $resident->occupation) }}" placeholder="e.g., Teacher, Nurse, Farmer">
                            </div>

                            <div class="form-group">
                                <label for="monthly_income">Monthly Income </label>
                                <input type="number" id="monthly_income" name="monthly_income" class="form-control" 
                                       value="{{ old('monthly_income', $resident->monthly_income) }}" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        
                        <hr style="border: 0; border-top: 2px solid #E5E7EB; margin: 25px 0;">
                        
                        <div class="checkbox-grid"> {{-- <<< MODIFICATION: Fixed typo from 'classs' --}}
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_registered_voter" name="is_registered_voter" value="1" 
                                       {{ old('is_registered_voter', $resident->is_registered_voter) ? 'checked' : '' }}>
                                <label for="is_registered_voter">Registered Voter</label>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" id="is_indigenous" name="is_indigenous" value="1" 
                                       {{ old('is_indigenous', $resident->is_indigenous) ? 'checked' : '' }}>
                                <label for="is_indigenous">Indigenous Person</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_pwd" name="is_pwd" value="1" 
                                       {{ old('is_pwd', $resident->is_pwd) ? 'checked' : '' }}>
                                <label for="is_pwd">PWD</label>
                            </div>

                            <div class="checkbox-group">
                                <input type="checkbox" id="is_4ps" name="is_4ps" value="1" 
                                       {{ old('is_4ps', $resident->is_4ps) ? 'checked' : '' }}>
                                <label for="is_4ps">4Ps Beneficiary</label>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </form>
</div>

@endsection