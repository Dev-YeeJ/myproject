@extends('layouts.dashboard-layout')

@section('title', 'Add New Medicine')

@section('nav-items')
    {{-- Navigation items for BHW Dashboard --}}
    <li class="nav-item">
        <a href="{{ route('dashboard.health') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <li class="nav-item">
        {{-- Active link for Health & Social Services --}}
        <a href="{{ route('health.health-services') }}" class="nav-link active">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    
@endsection

@section('content')
<style>
    /* Re-using table container style for the form */
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px;
        max-width: 800px; /* Limit width for better form readability */
        margin: 0 auto;   /* Center the form */
    }
    .form-header {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 25px;
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 15px;
    }
    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 30px;
    }
</style>

<div class="form-container">
    <div class="form-header">
        Add New Medicine to Inventory
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- This form correctly posts to the 'health.medicine.store' route --}}
    <form action="{{ route('health.medicine.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="item_name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="item_name" name="item_name" value="{{ old('item_name') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="brand_name" class="form-label">Brand Name</label>
                <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" placeholder="e.g., Biogesic">
            </div>

            {{-- NEW: Category Dropdown --}}
            <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="category" name="category" required>
                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>Select a category</option>
                    <option value="Pain Relief/Fever" {{ old('category') == 'Pain Relief/Fever' ? 'selected' : '' }}>Pain Relief/Fever</option>
                    <option value="Antibiotic" {{ old('category') == 'Antibiotic' ? 'selected' : '' }}>Antibiotic</option>
                    <option value="Allergy" {{ old('category') == 'Allergy' ? 'selected' : '' }}>Allergy</option>
                    <option value="Asthma" {{ old('category') == 'Asthma' ? 'selected' : '' }}>Asthma</option>
                    <option value="Cold & Cough" {{ old('category') == 'Cold & Cough' ? 'selected' : '' }}>Cold & Cough</option>
                    <option value="Vitamins & Supplements" {{ old('category') == 'Vitamins & Supplements' ? 'selected' : '' }}>Vitamins & Supplements</option>
                    <option value="Digestive Health" {{ old('category') == 'Digestive Health' ? 'selected' : '' }}>Digestive Health</option>
                    <option value="First Aid" {{ old('category') == 'First Aid' ? 'selected' : '' }}>First Aid</option>
                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- UPDATED: Changed from col-md-4 to col-md-6 --}}
            <div class="col-md-6 mb-3">
                <label for="dosage" class="form-label">Dosage <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="e.g., 500mg" required>
            </div>

            {{-- UPDATED: Now on a new row --}}
            <div class="col-md-4 mb-3">
                <label for="quantity" class="form-label">Quantity (pcs) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="expiration_date" class="form-label">Expiration Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}" required>
            </div>
            
            {{-- UPDATED: Changed from col-md-12 to col-md-4 and shortened text --}}
            <div class="col-md-4 mb-3">
                <label for="low_stock_threshold" class="form-label">Low Stock Alert <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" required>
                <small class="form-text text-muted">Warn when qty is below this.</small>
            </div>
        </div>

        <div class="form-footer">
            {{-- FIXED: This route now correctly points to the BHW's page --}}
            <a href="{{ route('health.health-services') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Medicine
            </button>
        </div>

    </form>
</div>
@endsection