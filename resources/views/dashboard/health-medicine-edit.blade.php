@extends('layouts.dashboard-layout')

@section('title', 'Edit Medicine')

@section('nav-items')
    {{-- Navigation for BHW Role --}}
    <li class="nav-item">
        <a href="{{ route('health.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>    
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('health.health-services') }}" class="nav-link active"> 
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
     <li class="nav-item">
        <a href="{{ route('health.announcements') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Edit Medicine</h3>
            <a href="{{ route('health.health-services') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>

        <div class="form-container">
            {{-- Display Validation Errors --}}
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

            <form action="{{ route('health.medicine.update', $medicine) }}" method="POST">
                @csrf
                @method('PUT') {{-- Required for updates --}}

                <div class="row g-3">
                    {{-- Item Name --}}
                    <div class="col-md-6">
                        <label for="item_name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="item_name" name="item_name" 
                               value="{{ old('item_name', $medicine->item_name) }}" required>
                    </div>

                    {{-- Brand Name --}}
                    <div class="col-md-6">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name"
                               value="{{ old('brand_name', $medicine->brand_name) }}">
                    </div>

                    {{-- Dosage --}}
                    <div class="col-md-6">
                        <label for="dosage" class="form-label">Dosage (e.g., 500mg) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dosage" name="dosage"
                               value="{{ old('dosage', $medicine->dosage) }}" required>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category" name="category"
                               value="{{ old('category', $medicine->category) }}" required>
                    </div>

                    {{-- Quantity --}}
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="0"
                               value="{{ old('quantity', $medicine->quantity) }}" required>
                    </div>

                    {{-- Low Stock Threshold --}}
                    <div class="col-md-4">
                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" min="0"
                               value="{{ old('low_stock_threshold', $medicine->low_stock_threshold) }}" required>
                    </div>
                    
                    {{-- Expiration Date --}}
                    <div class="col-md-4">
                        <label for="expiration_date" class="form-label">Expiration Date <span class="text-danger">*</span></label>
                        {{-- Format date for the input type="date" --}}
                        <input type="date" class="form-control" id="expiration_date" name="expiration_date" 
                               value="{{ old('expiration_date', $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date)->format('Y-m-d') : '') }}" required>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-12 text-end mt-4">
                        <a href="{{ route('health.health-services') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection