@extends('layouts.dashboard-layout')

@section('title', 'View Medicine Details')

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
@endsection

@section('content')
<style>
    .details-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px;
    }
    .details-list {
        font-size: 1.05rem;
    }
    .details-list dt {
        font-weight: 600;
        color: #555;
    }
    .details-list dd {
        margin-left: 0;
        margin-bottom: 1rem;
        font-weight: 500;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Medicine Details</h3>
            <a href="{{ route('health.health-services') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>

        <div class="details-container">
            <h4 class="mb-4 border-bottom pb-2">{{ $medicine->item_name }}</h4>

            <dl class="row details-list">
                <dt class="col-sm-4">Brand Name</dt>
                <dd class="col-sm-8">{{ $medicine->brand_name ?? 'N/A' }}</dd>

                <dt class="col-sm-4">Dosage</dt>
                <dd class="col-sm-8">{{ $medicine->dosage }}</dd>

                <dt class="col-sm-4">Category</dt>
                <dd class="col-sm-8">{{ $medicine->category }}</dd>

                <dt class="col-sm-4">Current Quantity</dt>
                <dd class="col-sm-8">{{ $medicine->quantity }}</dd>

                <dt class="col-sm-4">Low Stock Threshold</dt>
                <dd class="col-sm-8">{{ $medicine->low_stock_threshold }}</dd>

                <dt class="col-sm-4">Expiration Date</dt>
                <dd class="col-sm-8">
                    {{ $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date)->format('F d, Y') : 'N/A' }}
                </D>

                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">
                    @if ($medicine->status === 'In Stock')
                        <span class="badge bg-success fs-6">{{ $medicine->status }}</span>
                    @elseif ($medicine->status === 'Low Stock')
                        <span class="badge bg-warning fs-6">{{ $medicine->status }}</span>
                    @elseif ($medicine->status === 'Expired')
                        <span class="badge bg-danger fs-6">{{ $medicine->status }}</span>
                    @else
                        <span class="badge bg-danger fs-6">{{ $medicine->status }}</span>
                    @endif
                </dd>
            </dl>

            <div class="text-end border-top pt-3 mt-3">
                <a href="{{ route('health.medicine.edit', $medicine) }}" class="btn btn-primary">
                    <i class="fas fa-pen"></i> Edit This Item
                </a>
            </div>
        </div>
    </div>
</div>
@endsection