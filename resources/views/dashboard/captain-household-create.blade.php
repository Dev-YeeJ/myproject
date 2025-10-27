{{-- resources/views/dashboards/captain-household-create.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Add New Household')

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
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
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
        color: #059669;
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
        background: #10B981;
        color: white;
    }

    .btn-primary:hover {
        background: #059669;
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
        .form-row {
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
            <i class="fas fa-home"></i>
        </div>
        <div class="form-header-text">
            <h2>Add New Household</h2>
            <p>Fill in the household information below</p>
        </div>
    </div>

    <form action="{{ route('captain.household.store') }}" method="POST">
        @csrf

        <!-- Household Information -->
        <div class="form-section">
            <div class="section-title">
                <i class="fas fa-info-circle"></i>
                <span>Household Details</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="household_name" class="required">Household Name</label>
                    <input type="text" id="household_name" name="household_name" class="form-control @error('household_name') error @enderror" 
                           value="{{ old('household_name') }}" placeholder="e.g., Dela Cruz Family" required>
                    @error('household_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="household_number" class="required">Household Number</label>
                    <input type="text" id="household_number" name="household_number" class="form-control @error('household_number') error @enderror" 
                           value="{{ old('household_number') }}" placeholder="e.g., HH-001" required>
                    @error('household_number')
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

            <div class="form-row">
                <div class="form-group">
                    <label for="purok">Purok</label>
                    <input type="text" id="purok" name="purok" class="form-control @error('purok') error @enderror" 
                           value="{{ old('purok') }}" placeholder="e.g., Purok 1">
                    @error('purok')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="required">Status</label>
                    <select id="status" name="status" class="form-control @error('status') error @enderror" required>
                        <option value="">Select Status</option>
                        <option value="complete" {{ old('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                        <option value="incomplete" {{ old('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('captain.resident-profiling', ['view' => 'households']) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                <span>Cancel</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <span>Save Household</span>
            </button>
        </div>
    </form>
</div>

@endsection
