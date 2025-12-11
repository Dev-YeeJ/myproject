{{-- resources/views/dashboard/captain-document-view.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Manage Document Request')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.resident-profiling') }}" class="nav-link ">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new document services route --}}
        <a href="{{ route('captain.document-services') }}" class="nav-link active">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.financial') }}" class="nav-link {{ request()->routeIs('captain.financial*') ? 'active' : '' }}">
        <i class="fas fa-dollar-sign"></i>
        <span>Financial Management</span>
    </a>
</li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new health services route --}}
        <a href="{{ route('captain.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.incident.index') }}" class="nav-link {{ request()->routeIs('captain.incident.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident & Blotter</span>
    </a>
</li>
    <li class="nav-item">
        <a href="{{ route('captain.project.monitoring') }}" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.announcements.index') }}" class="nav-link {{ request()->routeIs('captain.announcements.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>Announcements</span>
    </a>
</li>
   <li class="nav-item">
        {{-- Use the new captain.sk.overview route --}}
        <a href="{{ route('captain.sk.overview') }}" class="nav-link {{ request()->routeIs('captain.sk.overview') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>SK Module</span>
        </a>
    </li>
   
@endsection

@section('content')
<style>
    .details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    .details-card, .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        padding: 30px;
    }
    .details-header {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .details-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    .details-header p {
        font-size: 0.95rem;
        color: #6B7280;
        margin-top: 8px;
        margin-bottom: 0;
    }
    .details-header .tracking-number {
        font-size: 0.9rem;
        font-weight: 700;
        color: #2B5CE6;
        background: #EFF6FF;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 10px;
    }
    .detail-item {
        margin-bottom: 16px;
    }
    
    /* --- Updated Styles matched from Resident View --- */
    .section-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6B7280;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.5px;
    }
    .amount-display {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1F2937;
        line-height: 1;
    }
    .payment-header, .info-header {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #2B5CE6; /* Blue matching the icon */
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F3F4F6;
    }
    /* --------------------------------------- */

    .detail-item .value {
        font-size: 1rem;
        font-weight: 500;
        color: #1F2937;
    }
    .detail-item .value-purpose {
        font-size: 0.95rem;
        font-style: italic;
        color: #374151;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        padding: 12px;
        border-radius: 8px;
    }
    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .requirements-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    .requirements-list .file-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #374151;
    }
    .requirements-list .file-info i {
        color: #2B5CE6;
        font-size: 1.2rem;
    }
    .requirements-list a {
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        color: #10B981;
    }
    .form-card label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    .form-card .form-control, .form-card .form-select {
        margin-bottom: 16px;
    }
    .btn-primary {
        background: #2B5CE6;
        border-color: #2B5CE6;
        color: white;
        font-weight: 600;
        padding: 10px 24px;
    }
    .btn-secondary {
        background: #E5E7EB;
        border-color: #E5E7EB;
        color: #374151;
        font-weight: 600;
        padding: 10px 24px;
    }

    @media (max-width: 992px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@if(session('success'))
<div class="alert alert-success" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

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

<div class="details-grid">
    
    {{-- Left Column: Request Details --}}
    <div class="details-card">
        <div class="details-header">
            <h3>Document Request Details</h3>
            <span class="tracking-number">{{ $documentRequest->tracking_number }}</span>
        </div>

        <div class="row">
            {{-- Basic Info --}}
            <div class="col-md-6 detail-item">
                <span class="section-label">REQUESTOR</span>
                <div class="value">{{ $documentRequest->resident->first_name }} {{ $documentRequest->resident->last_name }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <span class="section-label">CONTACT NUMBER</span>
                <div class="value">{{ $documentRequest->resident->contact_number ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <span class="section-label">DOCUMENT TYPE</span>
                <div class="value">{{ $documentRequest->documentType->name }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <span class="section-label">DATE REQUESTED</span>
                <div class="value">{{ $documentRequest->created_at->format('M d, Y - h:ia') }}</div>
            </div>
            <div class="col-12 detail-item">
                <span class="section-label">PURPOSE</span>
                <div class="value-purpose">{{ $documentRequest->purpose }}</div>
            </div>
            
            {{-- ========================================== --}}
            {{-- NEW: DYNAMIC ADDITIONAL INFORMATION SECTION --}}
            {{-- ========================================== --}}
            @if(!empty($documentRequest->custom_data))
                <div class="col-12">
                    <div class="mt-4 pt-2">
                        <div class="info-header">
                            <i class="fas fa-info-circle"></i> Additional Information
                        </div>
                        <div class="row g-3">
                            @foreach($documentRequest->custom_data as $key => $value)
                                <div class="col-md-6 detail-item">
                                    {{-- Format key: business_name -> BUSINESS NAME --}}
                                    <span class="section-label">{{ strtoupper(str_replace('_', ' ', $key)) }}</span>
                                    <div class="value">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{-- ========================================== --}}

            <div class="col-12">
                <div class="mt-4 pt-2">
                    <div class="payment-header">
                        <i class="fas fa-coins"></i> Payment Information
                    </div>

                    <div class="row g-3">
                        {{-- Amount Due --}}
                        <div class="col-md-4 detail-item">
                            <span class="section-label">AMOUNT DUE</span>
                            <div class="amount-display">₱{{ number_format($documentRequest->price, 2) }}</div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-4 detail-item">
                            <span class="section-label">PAYMENT METHOD</span>
                            <div class="value mt-1">
                                @if($documentRequest->payment_method === 'Online')
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <span class="fw-bold text-primary">GCash / Maya</span>
                                    </div>
                                @elseif($documentRequest->payment_method === 'Cash')
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <span class="fw-bold text-success">Cash on Pickup</span>
                                    </div>
                                @elseif($documentRequest->price == 0)
                                    <span class="badge bg-secondary">Free</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>

                        {{-- Payment Status --}}
                        <div class="col-md-4 detail-item">
                            <span class="section-label">PAYMENT STATUS</span>
                            <div class="value mt-1">
                                @if($documentRequest->payment_status == 'Paid')
                                    <span class="badge bg-success px-3 py-2">Paid</span>
                                @elseif($documentRequest->payment_status == 'Verification Pending')
                                    <span class="badge bg-primary px-3 py-2">Verification Pending</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">{{ $documentRequest->payment_status }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Online Payment Details (Proof) --}}
                        @if($documentRequest->payment_method === 'Online')
                            <div class="col-12 mt-3">
                                <div class="p-3 bg-light border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <span class="section-label">REFERENCE NUMBER</span>
                                            <div class="value font-monospace bg-white border rounded px-3 py-2 d-inline-block">
                                                {{ $documentRequest->payment_reference_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <span class="section-label">PROOF OF PAYMENT (SCREENSHOT)</span>
                                            @if($documentRequest->payment_proof)
                                                <div class="mt-2">
                                                    <a href="{{ Storage::url($documentRequest->payment_proof) }}" target="_blank">
                                                        <img src="{{ Storage::url($documentRequest->payment_proof) }}" 
                                                             alt="Proof of Payment" 
                                                             class="img-fluid border rounded"
                                                             style="max-height: 400px; width: auto; object-fit: contain;">
                                                    </a>
                                                    <div class="mt-1">
                                                        <small class="text-muted"><i class="fas fa-search-plus"></i> Click image to view full size</small>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mt-2">
                                                    <i class="fas fa-exclamation-triangle"></i> No proof of payment uploaded.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Requirements Section --}}
        <div>
            <h4 class="mb-3 text-dark fw-bold" style="font-size: 1.1rem;">
                <i class="fas fa-paperclip text-muted me-2"></i> Resident's Uploaded Requirements
            </h4>
            @if($documentRequest->requirements->count() > 0)
                <ul class="requirements-list mt-3">
                    @foreach($documentRequest->requirements as $file)
                    <li>
                        <div class="file-info">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $file->file_name }}</span>
                        </div>
                        <a href="{{ route('captain.requirement.download', $file->id) }}" target="_blank">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No requirements were uploaded for this request.</p>
            @endif
        </div>

    </div>

    {{-- Right Column: Action Form --}}
    <div class="form-card">
        <div class="details-header">
            <h3>Process Request</h3>
            <p>Update status, verify payment, and upload final document.</p>
        </div>
        
        <form action="{{ route('captain.document.update', $documentRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- 1. Document Status --}}
            <div class="form-group mb-3">
                <label for="status">Request Status</label>
                <select name="status" id="status" class="form-select">
                    @php 
                        $statuses = ['Pending', 'Processing', 'Under Review', 'Ready for Pickup', 'Completed', 'Rejected', 'Cancelled']; 
                    @endphp
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $documentRequest->status === $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Payment Status (NEW) --}}
            <div class="form-group mb-3 p-3 bg-light border rounded">
                <label for="payment_status" class="text-dark">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-select">
                    <option value="Unpaid" {{ $documentRequest->payment_status === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="Verification Pending" {{ $documentRequest->payment_status === 'Verification Pending' ? 'selected' : '' }}>Verification Pending (Check Proof)</option>
                    <option value="Paid" {{ $documentRequest->payment_status === 'Paid' ? 'selected' : '' }}>Paid (Confirmed)</option>
                    <option value="Waived" {{ $documentRequest->payment_status === 'Waived' ? 'selected' : '' }}>Waived (Free)</option>
                </select>
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle"></i> Mark as "Paid" only after verifying the cash receipt or the GCash screenshot.
                </small>
            </div>

            <div class="form-group mb-3">
                <label for="remarks">Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="4" class="form-control" placeholder="e.g., Document is ready for pickup.">{{ $documentRequest->remarks }}</textarea>
                <small class="form-text text-muted">This will be visible to the resident.</small>
            </div>

            <div class="form-group mt-3">
                <label for="generated_file">Upload Generated Document (Optional)</label>
                <input type="file" name="generated_file" id="generated_file" class="form-control">
                <small class="form-text text-muted">.pdf, .doc, .docx (Max 5MB)</small>

                @if($documentRequest->generated_file_path)
                <div class="mt-2 p-2 border rounded bg-light">
                    <strong>Current File:</strong> 
                    <a href="{{ Storage::url($documentRequest->generated_file_path) }}" target="_blank">View Uploaded Document</a>
                </div>
                @endif
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ route('captain.document-services', ['view' => 'requests']) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Request
                </button>
            </div>

        </form>
    </div>

</div>

@endsection