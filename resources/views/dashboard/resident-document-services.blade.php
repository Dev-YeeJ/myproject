{{-- resources/views/dashboards/resident-document-services.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Document Services')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link ">
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
    <li class="nav-item">
    <a href="{{ route('resident.health-services') }}" class="nav-link">
        <i class="fas fa-heartbeat"></i>
        <span>Health Services</span>
    </a>
</li>

{{-- NEW LINK HERE --}}
<li class="nav-item">
    <a href="{{ route('resident.incidents.index') }}" class="nav-link {{ request()->routeIs('resident.incidents.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident Reports</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('resident.announcements.index') }}" class="nav-link">
        <i class="fas fa-bullhorn"></i>
        <span>Announcements</span>
    </a>
</li>

@endsection

@section('content')
<style>
    /* --- Base styles from captain-document-services --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 40px; border-radius: 16px;
        margin-bottom: 30px; position: relative;
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }
    .total-registered {
        position: absolute; top: 40px; right: 40px; text-align: right;
    }
    .total-registered-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .total-registered-count { font-size: 2.5rem; font-weight: 700; }
    .total-registered-sublabel { font-size: 0.85rem; opacity: 0.9; }
    .stats-row {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 20px; margin-bottom: 30px;
    }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
    }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }
    .stat-badge.purple { color: #A855F7; }
    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-purple-bg { background: #A855F7; }
    .action-buttons { display: flex; gap: 12px; margin-bottom: 30px; }
    .btn-action {
        padding: 12px 24px; border-radius: 10px; border: none;
        font-weight: 600; display: flex; align-items: center;
        gap: 10px; cursor: pointer; transition: all 0.3s;
        font-size: 0.95rem; text-decoration: none;
    }
    .btn-add { background: #2B5CE6; color: white; }
    .btn-add:hover { background: #1E3A8A; transform: translateY(-2px); color: white; }

    .view-toggles {
        margin-bottom: 30px; display: flex; gap: 0;
        border-radius: 10px; overflow: hidden;
        border: 2px solid #2B5CE6; width: fit-content;
    }
    .btn-toggle {
        padding: 12px 24px; border: none; font-weight: 600;
        display: flex; align-items: center; gap: 10px;
        cursor: pointer; transition: all 0.3s; font-size: 0.95rem;
        text-decoration: none; background: white; color: #2B5CE6;
    }
    .btn-toggle.active { background: #2B5CE6; color: white; }
    .btn-toggle:not(.active):hover { background: #EFF6FF; }
    
    .pagination-container {
        padding: 20px; background: white; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07); margin-top: 30px;
    }
    .no-results-found {
        text-align: center; padding: 60px; background: white;
        border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .no-results-found i { font-size: 3rem; color: #ccc; margin-bottom: 15px; }
    .no-results-found p { color: #999; font-size: 1.1rem; }

    /* --- Card styles for Resident --- */
    .section-title {
        font-size: 1.25rem; font-weight: 700; color: #1F2937;
        margin-bottom: 20px; padding-left: 10px; border-left: 4px solid #2B5CE6;
    }
    .card-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
    }
    .doc-type-card {
        background: white; border: 1px solid #E5E7EB;
        border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex; flex-direction: column; padding: 24px;
        position: relative;
    }
    .doc-type-card-header {
        display: flex; align-items: flex-start;
        justify-content: space-between; margin-bottom: 16px;
    }
    .doc-type-card-icon {
        width: 48px; height: 48px; background: #EFF6FF; color: #2B5CE6;
        border-radius: 8px; display: flex; align-items: center;
        justify-content: center; font-size: 1.5rem; flex-shrink: 0;
    }
    .doc-type-card-price {
        font-size: 1.25rem; font-weight: 700;
        color: #1F2937; text-align: right;
    }
    .doc-type-card-price small {
        display: block; font-size: 0.8rem;
        font-weight: 500; color: #6B7280;
    }
    .doc-type-card-body h3 {
        font-size: 1.1rem; font-weight: 700;
        color: #111827; margin: 0 0 8px 0;
    }
    .doc-type-card-body p {
        font-size: 0.9rem; color: #6B7280;
        line-height: 1.5; margin-bottom: 16px;
        min-height: 60px; /* Ensures consistent card height */
    }
    .doc-type-card-body h4 {
        font-size: 0.8rem; font-weight: 600; color: #374151;
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .doc-type-card-body ul {
        list-style: none; padding-left: 0; margin: 0 0 16px 0;
    }
    .doc-type-card-body li {
        font-size: 0.85rem; color: #6B7280;
        margin-bottom: 4px; display: flex;
        align-items: center; gap: 8px;
    }
    .doc-type-card-body li i { font-size: 0.6rem; color: #2B5CE6; }
    
    /* This footer is for the RESIDENT card */
    .doc-type-card-footer {
        border-top: 1px solid #F3F4F6;
        padding-top: 16px; margin-top: auto;
    }
    /* Re-using .btn-action.btn-add for the request button */
    .doc-type-card-footer .btn-add {
        width: 100%; padding: 10px 24px;
        justify-content: center; font-size: 0.9rem;
    }

    /* --- Table styles for Resident --- */
    .table-container {
        background: white; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .table-header {
        padding: 20px 24px; display: flex;
        justify-content: space-between; align-items: center;
        border-bottom: 1px solid #F3F4F6;
        flex-wrap: wrap; gap: 16px;
    }
    .table-title {
        font-size: 1.1rem; font-weight: 700;
        color: #111827; display: flex;
        align-items: center; gap: 8px;
    }
    .table-title .title-count {
        font-size: 0.9rem; font-weight: 600;
        color: #6B7280; background: #F3F4F6;
        padding: 4px 10px; border-radius: 8px;
    }
    .table-filters { display: flex; gap: 12px; flex-wrap: wrap; }
    .table-filters .filter-dropdown {
        padding: 10px 12px; border: 1px solid #E5E7EB;
        background: #F9FAFB; border-radius: 8px;
        font-size: 0.9rem; color: #374151;
        font-weight: 500; outline: none;
    }
    .responsive-table-wrapper { width: 100%; overflow-x: auto; }
    .responsive-table {
        width: 100%; min-width: 800px; /* Resident table can be simpler */
        border-collapse: collapse;
    }
    .responsive-table th {
        background: #F9FAFB; padding: 16px 24px;
        text-align: left; font-size: 0.8rem;
        font-weight: 600; color: #6B7280;
        text-transform: uppercase; letter-spacing: 0.5px;
        border-bottom: 1px solid #E5E7EB;
    }
    .responsive-table td {
        padding: 16px 24px; font-size: 0.9rem;
        color: #374151;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: top; /* Changed to top */
    }
    .responsive-table tbody tr:last-child td { border-bottom: none; }
    
    .tracking-number {
        font-weight: 600; color: #2B5CE6; text-decoration: none;
    }
    .tracking-number:hover { text-decoration: underline; }

    .badge {
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.8rem; font-weight: 600;
        display: inline-block; text-align: center;
    }
    .badge-unpaid { background: #FEE2E2; color: #DC2626; }
    .badge-paid { background: #D1FAE5; color: #065F46; }
    .badge-waived { background: #FEF3C7; color: #B45309; }
    .badge-pending { background: #FEF3C7; color: #B45309; }
    .badge-pickup { background: #DBEAFE; color: #2563EB; }
    .badge-processing { background: #E0E7FF; color: #4338CA; }
    .badge-completed { background: #D1FAE5; color: #065F46; }
    .badge-cancelled { background: #F3F4F6; color: #6B7280; }
    .badge-rejected { background: #FEE2E2; color: #991B1B; } /* --- NEW --- */

    .table-actions a, .table-actions button {
        color: #6B7280; text-decoration: none;
        font-size: 0.9rem; margin: 0 4px;
        border: none; background: transparent; cursor: pointer;
        font-weight: 600; padding: 6px 10px; border-radius: 6px;
    }
    .table-actions .view-btn { color: #2B5CE6; }
    .table-actions .view-btn:hover { background: #EFF6FF; }
    .table-actions .cancel-btn { color: #DC2626; }
    .table-actions .cancel-btn:hover { background: #FEE2E2; }
    /* --- NEW --- */
    .table-actions .download-btn {
        color: #059669;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .table-actions .download-btn:hover { background: #ECFDF5; }
    
    /* --- NEW --- */
    .remarks-box {
        font-size: 0.85rem;
        font-style: italic;
        background: #FDFDEA; /* Light yellow */
        border: 1px solid #F0E9C3;
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
    }
    .remarks-box strong {
        color: #B45309;
    }

    /* Modal styles */
    .modal {
        display: none; position: fixed; z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    .modal.show { display: flex; align-items: center; justify-content: center; }
    .modal-content {
        background: white; padding: 30px; border-radius: 12px;
        max-width: 400px; width: 90%;
    }
    .modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .modal-icon {
        width: 48px; height: 48px; background: #FEE2E2;
        color: #EF4444; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 1.5rem;
    }
    .modal-title { font-size: 1.3rem; font-weight: 700; color: #1F2937; }
    .modal-body { margin-bottom: 25px; color: #6B7280; line-height: 1.6; }
    .modal-actions { display: flex; gap: 12px; justify-content: flex-end; }
    .btn-cancel {
        padding: 10px 20px; background: #F3F4F6; color: #4B5563;
        border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
    }
    .btn-confirm-delete {
        padding: 10px 20px; background: #EF4444; color: white;
        border: none; border-radius: 8px; font-weight: 600; cursor: pointer;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .card-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .total-registered { position: static; text-align: left; margin-top: 20px; }
        .card-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- Get the current view from the request, default to 'available' --}}
@php $view = request('view', 'available'); @endphp

@if(session('success'))
<div class="alert alert-success" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-times-circle"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Header section, adapted for Resident --}}
<div class="profiling-header">
    <div class="profiling-title">Document Services</div>
    <div class="profiling-subtitle">Request barangay documents and track your requests.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
    </div>
    <div class="total-registered">
        <div class="total-registered-label">My Pending Requests</div>
        <div class="total-registered-count">{{ $stats['my_pending_requests'] ?? 0 }}</div>
        <div class="total-registered-sublabel">Awaiting action</div>
    </div>
</div>

{{-- Stats row, adapted for Resident --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['my_pending_requests'] ?? 0 }}</h3>
            <p>My Pending Requests</p>
            <div class="stat-badge purple">
                <i class="fas fa-clock"></i>
                <span>Awaiting action</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-clock"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['my_completed_requests'] ?? 0 }}</h3>
            <p>My Completed Requests</p>
            <div class="stat-badge green">
                <i class="fas fa-check-circle"></i>
                <span>In total</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['available_documents'] ?? 0 }}</h3>
            <p>Available Documents</p>
            <div class="stat-badge blue">
                <i class="fas fa-file-alt"></i>
                <span>Ready to request</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-file-alt"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_requests'] ?? 0 }}</h3>
            <p>Total Requests Made</p>
            <div class="stat-badge orange">
                <i class="fas fa-history"></i>
                <span>View history</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-history"></i>
        </div>
    </div>
</div>

{{-- Action buttons, adapted for Resident --}}
<div class="action-buttons">
    <a href="{{ route('resident.document.create') }}" class="btn-action btn-add">
        <i class="fas fa-plus"></i>
        <span>Request a New Document</span>
    </a>
</div>

{{-- View Toggles, adapted for Resident --}}
<div class="view-toggles">
    <a href="{{ route('resident.document-services', ['view' => 'available']) }}" class="btn-toggle {{ $view === 'available' ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <span>Available Documents</span>
    </a>
    <a href="{{ route('resident.document-services', ['view' => 'history']) }}" class="btn-toggle {{ $view === 'history' ? 'active' : '' }}">
        <i class="fas fa-history"></i>
        <span>My Request History</span>
    </a>
</div>


{{-- Badge helper functions for the table --}}
@php
    function getPaymentBadge($status) {
        $map = [
            'Unpaid' => 'badge-unpaid', 
            'Paid' => 'badge-paid', 
            'Waived' => 'badge-waived',
            // Added Verification Pending mapped to processing (Indigo)
            'Verification Pending' => 'badge-processing', 
        ];
        $class = $map[$status] ?? 'badge-unpaid';
        return "<span class='badge {$class}'>{$status}</span>";
    }

    function getStatusBadge($status) {
        $map = [
            'Pending' => 'badge-pending', 'Ready for Pickup' => 'badge-pickup',
            'Processing' => 'badge-processing', 'Completed' => 'badge-completed',
            'Cancelled' => 'badge-cancelled', 'Rejected' => 'badge-rejected',
            'Under Review' => 'badge-processing', // Use same as processing
        ];
        $class = $map[$status] ?? 'badge-pending';
        return "<span class='badge {$class}'>{$status}</span>";
    }
@endphp


@if($view === 'available')

    <h3 class="section-title">Available Documents to Request</h3>
    
    {{-- Controller must pass $documentTypes (only active ones) --}}
    <div class="card-grid">
        @forelse($documentTypes as $type)
        <div class="doc-type-card">
            <div class="doc-type-card-header">
                <div class="doc-type-card-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="doc-type-card-price">
                    {{ $type->price > 0 ? '₱' . number_format($type->price, 0) : 'Free' }}
                    <small>{{ $type->requires_payment ? 'Payment Required' : 'No Payment' }}</small>
                </div>
            </div>
            <div class="doc-type-card-body">
                <h3>{{ $type->name }}</h3>
                <p>{{ $type->description ?? 'No description provided for this document.' }}</p>
                <h4>Requirements</h4>
                <ul>
                    {{-- This should be dynamic from your DB --}}
                    <li><i class="fas fa-circle"></i> Valid Government ID</li>
                    <li><i class="fas fa-circle"></i> Proof of Residency</li>
                </ul>
            </div>
            <div class="doc-type-card-footer">
                {{-- Link to the request form, passing the type ID --}}
                <a href="{{ route('resident.document.create', ['type_id' => $type->id]) }}" class="btn-action btn-add">
                    <i class="fas fa-file-import"></i>
                    <span>Request Now</span>
                </a>
            </div>
        </div>
        @empty
        <div class="no-results-found" style="grid-column: 1 / -1;">
            <i class="fas fa-file-alt"></i>
            <p>No documents are available for request at this time.</p>
        </div>
        @endforelse
    </div>
    
    <div class="pagination-container">
         {{ $documentTypes->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@elseif($view === 'history')

    <div class="table-container">
        <div class="table-header">
            <div class="table-title">
                My Request History
                <span class="title-count">{{ $documentRequests->total() }}</span>
            </div>
            <div class="table-filters">
                <form action="{{ route('resident.document-services') }}" method="GET">
                    <input type="hidden" name="view" value="history">
                    <select name="status" class="filter-dropdown" onchange="this.form.submit()">
                        <option value="All" {{ request('status') === 'All' ? 'selected' : '' }}>All Status</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Ready for Pickup" {{ request('status') === 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="responsive-table-wrapper">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>Tracking #</th>
                        <th>Document Type</th>
                        <th>Date Requested</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $request)
                    <tr>
                        <td>
                            <a href="#" class="tracking-number">{{ $request->tracking_number }}</a>
                            {{-- !!! NEW: REMARKS BOX !!! --}}
                            @if($request->remarks)
                            <div class="remarks-box">
                                <strong>Captain's Remarks:</strong>
                                <div>{{ $request->remarks }}</div>
                            </div>
                            @endif
                        </td>
                        <td>{{ $request->documentType->name ?? 'N/A' }}</td>
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            {{-- Payment Status Badge --}}
                            {!! getPaymentBadge($request->payment_status) !!}
                            
                            {{-- Payment Method Indicator --}}
                            @if($request->payment_method)
                                <div style="font-size: 0.75rem; color: #6B7280; margin-top: 4px;">
                                    @if($request->payment_method === 'Online')
                                        <i class="fas fa-mobile-alt"></i> Online
                                    @elseif($request->payment_method === 'Cash')
                                        <i class="fas fa-coins"></i> Cash
                                    @else
                                        {{ $request->payment_method }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td>{!! getStatusBadge($request->status) !!}</td>
                        <td class="table-actions">
                            {{-- <a href="#" class="view-btn"><i class="fas fa-eye"></i> View</a> --}}

                            {{-- !!! NEW: DOWNLOAD BUTTON !!! --}}
                            @if($request->status === 'Completed' && $request->generated_file_path)
                                <a href="{{ route('resident.document.download', $request->id) }}" class="download-btn">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @endif
                            
                            @if(in_array($request->status, ['Pending', 'Processing']))
                                <button class="cancel-btn" onclick="showCancelModal({{ $request->id }}, '{{ $request->tracking_number }}')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="no-results-found" style="box-shadow: none; padding: 40px;">
                                <i class="fas fa-file-import"></i>
                                <p>You have not made any document requests yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="pagination-container">
        {{ $documentRequests->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@endif


{{-- Modal for Cancelling a Request --}}
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Cancel Request</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to cancel request <strong id="trackingNumber"></strong>?</p>
            <p>This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeCancelModal()">Close</button>
            <form id="cancelForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Yes, Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showCancelModal(id, trackingNumber) {
        document.getElementById('trackingNumber').textContent = trackingNumber;
        // Update this route to your resident cancel route
        document.getElementById('cancelForm').action = `/resident/document-request/${id}/cancel`; 
        const currentView = new URLSearchParams(window.location.search).get('view') || 'history';
        document.getElementById('cancelForm').action += `?view=${currentView}`;
        document.getElementById('cancelModal').classList.add('show');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('cancelModal');
        if (event.target === modal) {
            closeCancelModal();
        }
    }
</script>
@endsection