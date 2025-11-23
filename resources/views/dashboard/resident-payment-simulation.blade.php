@extends('layouts.dashboard-layout')

@section('title', 'Confirm Payment')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('resident.document-services') }}" class="nav-link active">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    {{-- Add other resident nav items if you have them --}}
@endsection

@section('content')
<style>
    .payment-container {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .payment-header {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: 24px;
        text-align: center;
    }
    .payment-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    .payment-body {
        padding: 30px;
    }
    .order-summary {
        margin-bottom: 25px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
        color: #374151;
        margin-bottom: 12px;
    }
    .summary-item .label {
        color: #6B7280;
    }
    .summary-item .value {
        font-weight: 600;
    }
    .summary-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        border-top: 2px solid #E5E7EB;
        padding-top: 15px;
    }
    
    /* Fake form for impressive simulation */
    .fake-form-group {
        margin-bottom: 15px;
    }
    .fake-form-group label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        display: block;
        margin-bottom: 6px;
    }
    .fake-input {
        background: #E5E7EB; /* Grayed out */
        border: 1px solid #D1D5DB;
        padding: 12px;
        border-radius: 8px;
        width: 100%;
        font-style: italic;
        color: #6B7280;
    }
    
    .payment-footer {
        padding: 30px;
        background: #F9FAFB;
        border-top: 1px solid #E5E7EB;
        text-align: center;
    }
    .btn-confirm-payment {
        background: #10B981;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background-color 0.2s;
    }
    .btn-confirm-payment:hover {
        background: #059669;
    }
    .btn-confirm-payment.loading {
        background: #059669;
        cursor: not-allowed;
    }
    .btn-confirm-payment .spinner {
        display: none;
        width: 1.2rem;
        height: 1.2rem;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    .btn-confirm-payment.loading .spinner {
        display: inline-block;
    }
    .btn-confirm-payment.loading .btn-text {
        display: none;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .cancel-link {
        display: block;
        margin-top: 15px;
        color: #6B7280;
        font-weight: 500;
        font-size: 0.9rem;
        text-decoration: none;
    }
    .cancel-link:hover {
        color: #111827;
    }

</style>

<div class="payment-container">
    <div class="payment-header">
        <h3>Confirm Your Payment</h3>
    </div>
    <div class="payment-body">
        <div class="order-summary">
            <div class="summary-item">
                <span class="label">Document:</span>
                <span class="value">{{ $documentRequest->documentType->name }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Purpose:</span>
                <span class="value">{{ Str::limit($documentRequest->purpose, 25) }}</span>
            </div>
            <div class="summary-item summary-total">
                <span class="label">Total Amount:</span>
                <span class="value">₱{{ number_format($documentRequest->price, 2) }}</span>
            </div>
        </div>

        <div class="alert alert-info" style="font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i>
            This is a school project simulation. No real payment will be processed.
        </div>

        {{-- This is just for show to make it look real --}}
        <div class="fake-form-group mt-4">
            <label for="fake-card">Payment Method</label>
            <div class="fake-input">
                <i class="fas fa-credit-card"></i>
                <span>**** **** **** 1234 (Test Card)</span>
            </div>
        </div>
    </div>
    <div class="payment-footer">
        {{-- This form submits to your new controller method --}}
        <form action="{{ route('resident.document.process', $documentRequest->id) }}" method="POST" id="payment-form">
            @csrf
            <button type="submit" class="btn-confirm-payment" id="payment-button">
                <div class="spinner"></div>
                <span class="btn-text">
                    <i class="fas fa-lock"></i>
                    Pay ₱{{ number_format($documentRequest->price, 2) }} Now
                </span>
            </button>
        </form>
        <a href="{{ route('resident.document-services', ['view' => 'history']) }}" class="cancel-link">
            Cancel and return to history
        </a>
    </div>
</div>

<script>
    // Add a fake loading spinner to make the simulation more impressive
    document.getElementById('payment-form').addEventListener('submit', function() {
        const button = document.getElementById('payment-button');
        button.classList.add('loading');
        button.disabled = true;
        // The button text is now hidden by CSS
    });
</script>