{{-- resources/views/dashboard/captain-document-view.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Manage Document Request')

@section('nav-items')
    {{-- This nav is copied from your reference, with 'Documents Services' active --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.resident-profiling') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.document-services') }}" class="nav-link active">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    {{-- ... other nav items ... --}}
    <li class="nav-item">
        <a href="{{ route('captain.health-services') }}" class="nav-link ">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
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
    .detail-item label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
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
            <div class="col-md-6 detail-item">
                <label>Requestor</label>
                <div class="value">{{ $documentRequest->resident->first_name }} {{ $documentRequest->resident->last_name }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <label>Contact Number</label>
                <div class="value">{{ $documentRequest->resident->contact_number ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <label>Document Type</label>
                <div class="value">{{ $documentRequest->documentType->name }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <label>Price</label>
                <div class="value">₱{{ number_format($documentRequest->price, 2) }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <label>Payment Status</label>
                <div class="value">{{ $documentRequest->payment_status }}</div>
            </div>
            <div class="col-md-6 detail-item">
                <label>Date Requested</label>
                <div class="value">{{ $documentRequest->created_at->format('M d, Y - h:ia') }}</div>
            </div>
            <div class="col-12 detail-item">
                <label>Purpose</label>
                <div class="value-purpose">{{ $documentRequest->purpose }}</div>
            </div>
        </div>

        <hr class="my-4">

        <div>
            <h4><i class="fas fa-paperclip"></i> Resident's Uploaded Requirements</h4>
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
            <p>Update the status and upload the final document.</p>
        </div>
        
        <form action="{{ route('captain.document.update', $documentRequest->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="status">Update Status</label>
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

            <div class="form-group">
                <label for="remarks">Remarks (Optional)</label>
                <textarea name="remarks" id="remarks" rows="4" class="form-control" placeholder="e.g., Rejected due to missing valid ID. Please re-submit.">{{ $documentRequest->remarks }}</textarea>
                <small class="form-text text-muted">This will be visible to the resident.</small>
            </div>

            <div class="form-group mt-3">
                <label for="generated_file">Upload Generated Document (Optional)</label>
                <input type="file" name="generated_file" id="generated_file" class="form-control">
                <small class="form-text text-muted">.pdf, .doc, .docx (Max 5MB)</small>

                @if($documentRequest->generated_file_path)
                <div class="mt-2">
                    <strong>Current File:</strong> 
                    <a href="{{ Storage::url($documentRequest->generated_file_path) }}" target="_blank">View Uploaded Document</a>
                </div>
                @endif
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ route('captain.document-services', ['view' => 'requests']) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Request
                </button>
            </div>

        </form>
    </div>

</div>

@endsection