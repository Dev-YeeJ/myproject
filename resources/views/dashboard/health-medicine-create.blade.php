{{-- resources/views/dashboard/captain-medicine-create.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Add New Medicine')

@section('nav-items')
    {{-- Navigation items for Captain Dashboard --}}
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

            <div class="col-md-4 mb-3">
                <label for="dosage" class="form-label">Dosage <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="e.g., 500mg" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="quantity" class="form-label">Quantity (pcs) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
            </div>

            <div class="col-md-4 mb-3">
                <label for="expiration_date" class="form-label">Expiration Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}" required>
            </div>
            
            <div class="col-md-12 mb-3">
                <label for="low_stock_threshold" class="form-label">Low Stock Alert Level <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" required>
                <small class="form-text text-muted">Show "Low Stock" warning when quantity falls below this number.</small>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('captain.health-services') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Medicine
            </button>
        </div>

    </form>
</div>
@endsection