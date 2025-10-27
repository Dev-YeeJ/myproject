{{-- resources/views/dashboards/captain-resident-add.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Add New Resident')

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
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 1000px;
        margin: 0 auto;
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #E5E7EB;
    }

    .form-header-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .form-header-text h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1F2937;
        margin: 0 0 5px 0;
    }

    .form-header-text p {
        color: #6B7280;
        margin: 0;
    }

    .form-section {
        margin-bottom: 35px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #2B5CE6;
    }

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
        border-color: #2B5CE6;
        box-shadow: 0 0 0 3px rgba(43, 92, 230, 0.1);
    }

    .form-control.error {
        border-color: #EF4444;
    }

    .error-message {
        color: #EF4444;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 40px;
        padding-top: 25px;
        border-top: 2px solid #E5E7EB;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #2B5CE6;
        color: white;
    }

    .btn-primary:hover {
        background: #1E3A8A;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #F3F4F6;
        color: #4B5563;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: #E5E7EB;
    }

    @media (max-width: 768px) {
        .form-row,
        .form-row.triple {
            grid-template-columns: 1fr;
        }

        .form-container {
            padding: 25px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="form-container">
    <div class="form-header">
        <div class="form-header-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="form-header-text">
            <h2>Add New Resident</h2>
            <p>Fill in the resident information below</p>
        </div>
    </div>

    <form action="{{ route('captain.resident.store') }}" method="POST">
        @csrf

        <!-- Personal Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-user"></i>
                <span>Personal Information</span>
            </div>

            <div class="form-row triple">
                <div class="form-group">
                    <label for="first_name" class="required">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') error @enderror" 
                           value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control" 
                           value="{{ old('middle_name') }}">
                </div>

                <div class="form-group">
                    <label for="last_name" class="required">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') error @enderror" 
                           value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="suffix">Suffix</label>
                    <input type="text" id="suffix" name="suffix" class="form-control" 
                           value="{{ old('suffix') }}" placeholder="Jr., Sr., III, etc.">
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="required">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') error @enderror" 
                           value="{{ old('date_of_birth') }}" required>
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
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="civil_status" class="required">Civil Status</label>
                    <select id="civil_status" name="civil_status" class="form-control @error('civil_status') error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status') == 'Separated' ? 'selected' : '' }}>Separated</option>
                        <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                    @error('civil_status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Household Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-home"></i>
                <span>Household Information</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="household_id">Household</label>
                    <select id="household_id" name="household_id" class="form-control">
                        <option value="">Select Household (Optional)</option>
                        @foreach($households as $household)
                            {{-- FIX: Check if $selectedHousehold matches --}}
                            <option value="{{ $household->id }}" {{ (old('household_id') == $household->id || $selectedHousehold == $household->id) ? 'selected' : '' }}>
                                {{-- FIX: Display household_name --}}
                                {{ $household->household_name }} ({{ $household->household_number }}) - {{ $household->address }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="household_status" class="required">Household Status</label>
                    <select id="household_status" name="household_status" class="form-control @error('household_status') error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="Household Head" {{ old('household_status') == 'Household Head' ? 'selected' : '' }}>Household Head</option>
                        <option value="Spouse" {{ old('household_status') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                        <option value="Child" {{ old('household_status') == 'Child' ? 'selected' : '' }}>Child</option>
                        <option value="Member" {{ old('household_status') == 'Member' ? 'selected' : '' }}>Member</option>
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
                           value="{{ old('address') }}" placeholder="Block, Lot, Street" required>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-phone"></i>
                <span>Contact Information</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" class="form-control" 
                           value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="{{ old('email') }}" placeholder="example@email.com">
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-briefcase"></i>
                <span>Employment Information</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="occupation">Occupation</label>
                    <input type="text" id="occupation" name="occupation" class="form-control" 
                           value="{{ old('occupation') }}" placeholder="e.g., Teacher, Nurse, Farmer">
                </div>

                <div class="form-group">
                    <label for="monthly_income">Monthly Income (₱)</label>
                    <input type="number" id="monthly_income" name="monthly_income" class="form-control" 
                           value="{{ old('monthly_income') }}" placeholder="0.00" step="0.01" min="0">
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-info-circle"></i>
                <span>Additional Information</span>
            </div>

            <div class="form-row">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_registered_voter" name="is_registered_voter" value="1" 
                           {{ old('is_registered_voter') ? 'checked' : '' }}>
                    <label for="is_registered_voter">Registered Voter</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_indigenous" name="is_indigenous" value="1" 
                           {{ old('is_indigenous') ? 'checked' : '' }}>
                    <label for="is_indigenous">Indigenous Person</label>
                </div>
            </div>

            <div class="form-row">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_pwd" name="is_pwd" value="1" 
                           {{ old('is_pwd') ? 'checked' : '' }}>
                    <label for="is_pwd">Person with Disability (PWD)</label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="is_4ps" name="is_4ps" value="1" 
                           {{ old('is_4ps') ? 'checked' : '' }}>
                    <label for="is_4ps">4Ps Beneficiary (Pantawid Pamilya)</label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('captain.resident-profiling') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <span>Save Resident</span>
            </button>
        </div>
    </form>
</div>

@endsection
