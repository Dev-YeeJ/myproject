@extends('layouts.dashboard-layout')

@section('title', 'Payment Successful')

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
    .success-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        padding: 50px;
        text-align: center;
        max-width: 600px;
        margin: 40px auto;
    }
    .success-icon {
        font-size: 4rem;
        color: #10B981;
        margin-bottom: 20px;
    }
    .success-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 10px;
    }
    .success-card p {
        font-size: 1rem;
        color: #6B7280;
        margin-bottom: 30px;
    }
    .btn-primary {
        background: #2B5CE6;
        border-color: #2B5CE6;
        color: white;
        font-weight: 600;
        padding: 10px 24px;
        text-decoration: none;
        border-radius: 8px;
    }
    .btn-primary:hover {
        background: #1E3A8A;
        color: white;
    }
</style>

<div class="success-card">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <h3>Payment Successful!</h3>
    <p>
        Thank you! Your payment for <strong>{{ $documentRequest->documentType->name }}</strong> has been received.
        The barangay will now process your request. You can check its status on your "My Request History" page.
    </p>
    <a href="{{ route('resident.document-services', ['view' => 'history']) }}" class="btn btn-primary">
        Back to My Requests
    </a>
</div>