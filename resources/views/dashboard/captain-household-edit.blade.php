{{-- resources/views/dashboards/captain-household-edit.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Edit Household - ' . $household->household_name)

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
    /* (Using the same green-themed styles from captain-household-create) */
    .details-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .details-header {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%); /* Green theme */
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
        padding: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1F2937;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 30px 30px 15px 30px;
        border-bottom: 2px solid #E5E7EB;
        margin-bottom: 0; 
    }

    .card-title i {
        color: #10B981; /* Green */
        font-size: 1.5rem;
    }

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

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row.single {
        grid-template-columns: 1fr;
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
    
    .form-group small {
        margin-top: 5px; 
        color: #6B7280;
        font-size: 0.85rem;
    }

    .form-control {
        padding: 12px 16px;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s;
        background: #F9FAFB;
    }
    
    .form-control:disabled {
        background: #F3F4F6;
        color: #6B7280;
        cursor: not-allowed;
    }
    
    select.form-control {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
        padding-right: 2.5rem; 
    }

    .form-control:focus {
        outline: none;
        border-color: #10B981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        background: white;
    }

    .form-control.error {
        border-color: #EF4444;
    }

    .error-message {
        color: #EF4444;
        font-size: 0.85rem;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .details-grid, .form-row {
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
    }
</style>

<div class="details-container">
    <a href="{{ route('captain.resident-profiling', ['view' => 'households']) }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Household Directory</span>
    </a>

    <form action="{{ route('captain.household.update', $household->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="details-header">
            <div class="header-left">
                <div class="profile-avatar">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="header-info">
                    <h1>Edit Household</h1>
                    <p>Updating {{ $household->household_name }}</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('captain.resident-profiling', ['view' => 'households']) }}" class="btn-header btn-secondary">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn-header btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Update Household</span>
                </button>
            </div>
        </div>

        <div class="details-grid">
            {{-- Left Column --}}
            <div>
                <div class="details-card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i>
                        <span>Household Details</span>
                    </div>

                    <div class="card-content">
                        <div class="form-row"> 
                            <div class="form-group">
                                <label for="household_name" class="required">Household Name</label>
                                <input type="text" id="household_name" name="household_name" class="form-control @error('household_name') error @enderror" 
                                       value="{{ old('household_name', $household->household_name) }}" placeholder="e.g., Dela Cruz Family" required>
                                @error('household_name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="household_number">Household Number</label>
                                <input type="text" id="household_number" class="form-control" 
                                       value="{{ $household->household_number }}" disabled>
                                <small>Household number cannot be changed.</small>
                            </div>
                        </div>

                        <div class="form-row single">
                            <div class="form-group">
                                <label for="address" class="required">Address</label>
                                <input type="text" id="address" name="address" class="form-control @error('address') error @enderror" 
                                       value="{{ old('address', $household->address) }}" placeholder="Block, Lot, Street" required>
                                @error('address')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                <div class="details-card">
                    <div class="card-title">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Location & Status</span>
                    </div>
                    
                    <div class="card-content">
                        <div class="form-row single">
                            <div class="form-group">
                                <label for="purok">Purok</label>
                                <select id="purok" name="purok" class="form-control @error('purok') error @enderror">
                                    <option value="">Select Purok (Optional)</option>
                                    <option value="Purok 1" {{ old('purok', $household->purok) == 'Purok 1' ? 'selected' : '' }}>Purok 1</option>
                                    <option value="Purok 2" {{ old('purok', $household->purok) == 'Purok 2' ? 'selected' : '' }}>Purok 2</option>
                                    <option value="Purok 3" {{ old('purok', $household->purok) == 'Purok 3' ? 'selected' : '' }}>Purok 3</option>
                                    <option value="Purok 4" {{ old('purok', $household->purok) == 'Purok 4' ? 'selected' : '' }}>Purok 4</option>
                                    <option value="Purok 5" {{ old('purok', $household->purok) == 'Purok 5' ? 'selected' : '' }}>Purok 5</option>
                                    <option value="Purok 6" {{ old('purok', $household->purok) == 'Purok 6' ? 'selected' : '' }}>Purok 6</option>
                                    <option value="Purok 7" {{ old('purok', $household->purok) == 'Purok 7' ? 'selected' : '' }}>Purok 7</option>
                                </select>
                                @error('purok')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- {{-- MODIFIED: Removed the "Data Status" field --}} --}}
                        <div class="form-row single">
                             <div class="form-group">
                                <label for="status">Data Status</label>
                                <input type="text" id="status" class="form-control" 
                                       value="{{ ucfirst($household->status) }}" disabled>
                                <small>This status is automatically set to 'Complete' when a 'Household Head' is assigned.</small>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection