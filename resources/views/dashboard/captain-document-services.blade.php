{{-- resources/views/dashboards/captain-document-services.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Document Management')

@section('nav-items')
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
        <a href="{{ route('captain.document-services') }}" class="nav-link active">
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
        <a href="{{ route('captain.health-services') }}" class="nav-link  ">
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
    /* --- Base styles from resident-profiling --- */
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
    .btn-add-household { background: #10B981; color: white; }
    .btn-add-household:hover { background: #059669; transform: translateY(-2px); color: white; }
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
    .alert-success {
        padding: 16px 20px; border-radius: 10px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 12px;
        background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7;
    }

    /* --- NEW STYLES FOR DOCUMENT CARDS --- */
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 20px;
        padding-left: 10px;
        border-left: 4px solid #2B5CE6;
    }
    .card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 columns */
        gap: 24px;
    }
    .template-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 columns */
        gap: 24px;
    }

    /* Card for Document Types */
    .doc-type-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        padding: 24px;
        position: relative;
    }
    .doc-type-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .doc-type-card-icon {
        width: 48px; height: 48px;
        background: #EFF6FF; /* light-blue */
        color: #2B5CE6; /* blue */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .doc-type-card-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1F2937;
        text-align: right;
    }
    .doc-type-card-price small {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #6B7280;
    }
    .doc-type-card-body h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }
    .doc-type-card-body p {
        font-size: 0.9rem;
        color: #6B7280;
        line-height: 1.5;
        margin-bottom: 16px;
        min-height: 60px;
    }
    .doc-type-card-body h4 {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .doc-type-card-body ul {
        list-style: none;
        padding-left: 0;
        margin: 0 0 16px 0;
    }
    .doc-type-card-body li {
        font-size: 0.85rem;
        color: #6B7280;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .doc-type-card-body li i {
        font-size: 0.6rem;
        color: #2B5CE6;
    }
    .doc-type-card-footer {
        border-top: 1px solid #F3F4F6;
        padding-top: 16px;
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .doc-type-card-footer .actions a,
    .doc-type-card-footer .actions button {
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }
    .doc-type-card-footer .actions .edit-btn {
        color: #10B981;
    }
    .doc-type-card-footer .actions .edit-btn:hover {
        background-color: #ECFDF5;
    }
    .doc-type-card-footer .actions .delete-btn {
        color: #EF4444;
        border: none;
        background: transparent;
        cursor: pointer;
    }
    .doc-type-card-footer .actions .delete-btn:hover {
        background-color: #FEE2E2;
    }
    .doc-type-card-footer .status-badge {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .status-badge.active { background: #D1FAE5; color: #065F46; }
    .status-badge.disabled { background: #FEE2E2; color: #991B1B; }


    /* Card for Templates */
    .template-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        padding: 24px;
        display: flex;
        gap: 20px;
    }
    .template-card-icon {
        width: 48px; height: 48px;
        background: #ECFDF5; /* light-green */
        color: #10B981; /* green */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .template-card-body h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px 0;
    }
    .template-card-body .subtitle {
        font-size: 0.9rem;
        color: #6B7280;
        margin-bottom: 4px;
    }
    .template-card-body .category-badge {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 20px;
        background: #EFF6FF;
        color: #2B5CE6;
        margin-bottom: 12px;
    }
    .template-card-body p {
        font-size: 0.9rem;
        color: #6B7280;
        line-height: 1.5;
        margin-bottom: 16px;
    }
    .template-card-meta {
        font-size: 0.8rem;
        color: #6B7280;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .template-card-meta span { display: flex; align-items: center; gap: 6px; }
    .template-card-meta i { color: #9CA3AF; }
    .template-card-actions {
        margin-left: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-start;
        gap: 8px;
        flex-shrink: 0;
    }
    .template-card-actions a {
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        color: #374151;
        padding: 6px 12px;
        border-radius: 6px;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .template-card-actions a:hover {
        background-color: #F3F4F6;
    }
    .template-card-actions a.edit-btn {
        color: #10B981;
    }
    .template-card-actions a.edit-btn:hover {
        background-color: #ECFDF5;
    }
    
    /* === STYLES FOR DOCUMENT REQUESTS TABLE === */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .table-header {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #F3F4F6;
        flex-wrap: wrap;
        gap: 16px;
    }
    .table-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .table-title .title-count {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6B7280;
        background: #F3F4F6;
        padding: 4px 10px;
        border-radius: 8px;
    }
    .table-filters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .table-filters .search-input {
        display: flex;
        align-items: center;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 0 12px;
    }
    .table-filters .search-input i { color: #9CA3AF; }
    .table-filters .search-input input {
        border: none; background: transparent;
        padding: 10px 8px; font-size: 0.9rem;
        outline: none;
    }
    .table-filters .filter-dropdown {
        padding: 10px 12px;
        border: 1px solid #E5E7EB;
        background: #F9FAFB;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #374151;
        font-weight: 500;
        outline: none;
    }
    .responsive-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    .responsive-table {
        width: 100%;
        min-width: 1000px; /* Force scroll on smaller viewports */
        border-collapse: collapse;
    }
    .responsive-table th {
        background: #F9FAFB;
        padding: 16px 24px;
        text-align: left;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #E5E7EB;
    }
    .responsive-table td {
        padding: 16px 24px;
        font-size: 0.9rem;
        color: #374151;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: top;
    }
    .responsive-table tbody tr:last-child td {
        border-bottom: none;
    }
    .tracking-number {
        font-weight: 600;
        color: #2B5CE6;
        text-decoration: none;
    }
    .tracking-number:hover { text-decoration: underline; }
    .requestor-info { display: flex; flex-direction: column; }
    .requestor-info .name { font-weight: 600; color: #1F2937; }
    .requestor-info .phone { font-size: 0.85rem; color: #6B7280; }
    .document-type-info { display: flex; flex-direction: column; }
    .document-type-info small { font-size: 0.85rem; color: #6B7280; }
    
    .badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
    }
    .badge-unpaid { background: #FEE2E2; color: #DC2626; }
    .badge-paid { background: #D1FAE5; color: #065F46; }
    .badge-waived { background: #FEF3C7; color: #B45309; }

    .badge-pending { background: #FEF3C7; color: #B45309; }
    .badge-pickup { background: #DBEAFE; color: #2563EB; }
    .badge-processing { background: #E0E7FF; color: #4338CA; }
    .badge-under-review { background: #F3E8FF; color: #7E22CE; }
    .badge-completed { background: #D1FAE5; color: #065F46; }
    .badge-rejected { background: #FEE2E2; color: #991B1B; } /* --- NEW --- */

    .badge-urgent { background: #FECACA; color: #B91C1C; }
    .badge-normal { background: #E5E7EB; color: #4B5563; }
    .badge-low { background: #E0E7FF; color: #4338CA; }

    .table-actions a, .table-actions button {
        color: #6B7280;
        text-decoration: none;
        font-size: 0.9rem; /* --- UPDATED --- */
        margin: 0 4px;
        border: none; background: transparent; cursor: pointer;
        font-weight: 600; padding: 6px 10px; border-radius: 6px;
    }
    /* --- NEW --- */
    .table-actions .manage-btn {
        background: #EFF6FF;
        color: #2B5CE6;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .table-actions .manage-btn:hover {
        background: #DBEAFE;
    }
    /* --- NEW --- */
    .attachment-icon {
        color: #9CA3AF;
        font-size: 0.9rem;
        margin-left: 8px;
    }
    .attachment-icon:hover {
        color: #1F2937;
    }
    
    @media (max-width: 1200px) {
        .card-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
        .card-grid { grid-template-columns: 1fr; }
        .template-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .total-registered { position: static; text-align: left; margin-top: 20px; }
    }
</style>

{{-- Get the current view from the request, default to 'requests' --}}
@php $view = request('view', 'requests'); @endphp

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- Header section, adapted from reference --}}
<div class="profiling-header">
    <div class="profiling-title">Document Management</div>
    <div class="profiling-subtitle">Manage document requests, types, and official templates</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
    </div>
    <div class="total-registered">
        <div class="total-registered-label">Total Document Types</div>
        <div class="total-registered-count">{{ $stats['total_types'] ?? 0 }}</div>
        <div class="total-registered-sublabel">Configured</div>
    </div>
</div>

{{-- Stats row, adapted from reference --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_types'] ?? 0 }}</h3>
            <p>Total Document Types</p>
            <div class="stat-badge blue">
                <i class="fas fa-file-alt"></i>
                <span>{{ $stats['active_types'] ?? 0 }} active</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-file-alt"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_templates'] ?? 0 }}</h3>
            <p>Total Templates</p>
            <div class="stat-badge green">
                <i class="fas fa-print"></i>
                <span>Linked Templates</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-print"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['paid_documents'] ?? 0 }}</h3>
            <p>Paid Documents</p>
            <div class="stat-badge orange">
                <i class="fas fa-peso-sign"></i>
                <span>Require payment</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-peso-sign"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['pending_requests'] ?? 0 }}</h3>
            <p>Pending Requests</p>
            <div class="stat-badge purple">
                <i class="fas fa-clock"></i>
                <span>Awaiting action</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-clock"></i>
        </div>
    </div>
</div>

{{-- Action buttons, adapted from reference --}}
<div class="action-buttons">
    <a href="{{-- {{ route('captain.document-type.create') }} --}}" class="btn-action btn-add">
        <i class="fas fa-plus"></i>
        <span>Add Document Type</span>
    </a>
    <a href="{{-- {{ route('captain.template.create') }} --}}" class="btn-action btn-add-household">
        <i class="fas fa-plus"></i>
        <span>Add Template</span>
    </a>
</div>

{{-- 3. UPDATED TOGGLES (Document Requests is now first) --}}
<div class="view-toggles">
    <a href="{{ route('captain.document-services', ['view' => 'requests']) }}" class="btn-toggle {{ $view === 'requests' ? 'active' : '' }}">
        <i class="fas fa-file-import"></i>
        <span>Document Requests</span>
    </a>
    <a href="{{ route('captain.document-services', ['view' => 'types']) }}" class="btn-toggle {{ $view === 'types' ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <span>Document Types</span>
    </a>
    <a href="{{ route('captain.document-services', ['view' => 'templates']) }}" class="btn-toggle {{ $view === 'templates' ? 'active' : '' }}">
        <i class="fas fa-print"></i>
        <span>Templates</span>
    </a>
</div>


{{-- 4. NEW IF/ELSEIF/ELSE STRUCTURE --}}

@if($view === 'requests')

    {{-- Helper functions for badges --}}
    @php
        function getPaymentBadge($status) {
            $map = [
                'Unpaid' => 'badge-unpaid',
                'Paid' => 'badge-paid',
                'Waived' => 'badge-waived',
            ];
            $class = $map[$status] ?? 'badge-normal';
            return "<span class='badge {$class}'>{$status}</span>";
        }

        function getStatusBadge($status) {
            $map = [
                'Pending' => 'badge-pending',
                'Ready for Pickup' => 'badge-pickup',
                'Processing' => 'badge-processing',
                'Under Review' => 'badge-under-review',
                'Completed' => 'badge-completed',
                'Rejected' => 'badge-rejected',
            ];
            $class = $map[$status] ?? 'badge-normal';
            return "<span class='badge {$class}'>{$status}</span>";
        }

        function getPriorityBadge($priority) {
            $map = [
                'Urgent' => 'badge-urgent',
                'Normal' => 'badge-normal',
                'Low' => 'badge-low',
            ];
            $class = $map[$priority] ?? 'badge-normal';
            return "<span class='badge {$class}'>{$priority}</span>";
        }
    @endphp

    <div class="table-container">
        <div class="table-header">
            <div class="table-title">
                Document Requests 
                <span class="title-count">{{ $documentRequests->total() }}</span>
            </div>
            {{-- This is the filter section from your image --}}
            <div class="table-filters">
                <form action="{{ route('captain.document-services') }}" method="GET">
                    <input type="hidden" name="view" value="requests">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search requests..." value="{{ request('search') }}">
                    </div>
                    <select name="status" class="filter-dropdown" onchange="this.form.submit()">
                        <option value="All" {{ request('status') === 'All' ? 'selected' : '' }}>All Status</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Processing" {{ request('status') === 'Processing' ? 'selected' : '' }}>Processing</option>
                        <option value="Ready for Pickup" {{ request('status') === 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
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
                        <th>Requestor</th>
                        <th>Document Type</th>
                        {{-- <th>Purpose</th> --}}
                        <th>Date</th>
                        {{-- <th>Priority</th> --}}
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $request)
                    <tr>
                        <td>
                            {{-- !!! UPDATED: LINK TO MANAGE PAGE !!! --}}
                            <a href="{{ route('captain.document.show', $request->id) }}" class="tracking-number">{{ $request->tracking_number }}</a>
                        </td>
                        <td>
                            <div class="requestor-info">
                                <span class="name">{{ $request->resident->first_name ?? 'N/A' }} {{ $request->resident->last_name ?? '' }}</span>
                                <span class="phone">{{ $request->resident->contact_number ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="document-type-info">
                                <span>
                                    {{ $request->documentType->name ?? 'N/A' }}
                                    {{-- !!! NEW: ATTACHMENT ICON !!! --}}
                                    @if($request->requirements->count() > 0)
                                        <i class="fas fa-paperclip attachment-icon" title="{{ $request->requirements->count() }} requirements uploaded"></i>
                                    @endif
                                </span>
                                <small>₱{{ number_format($request->price ?? 0, 2) }}</small>
                            </div>
                        </td>
                        {{-- <td>{{ $request->purpose }}</td> --}}
                        <td>{{ $request->created_at->format('M d, Y') }}</td>
                        {{-- <td>{!! getPriorityBadge($request->priority) !!}</td> --}}
                        <td>{!! getPaymentBadge($request->payment_status) !!}</td>
                        <td>{!! getStatusBadge($request->status) !!}</td>
                        <td class="table-actions">
                            {{-- !!! UPDATED: ACTION BUTTONS !!! --}}
                            <a href="{{ route('captain.document.show', $request->id) }}" class="manage-btn">
                                <i class="fas fa-edit"></i> Manage
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7"> {{-- Updated colspan --}}
                            <div class="no-results-found" style="box-shadow: none; padding: 40px;">
                                <i class="fas fa-file-import"></i>
                                <p>No document requests found.</p>
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


@elseif($view === 'templates')

    <h3 class="section-title">Document Templates ({{ $templates->total() }})</h3>
    <div class="template-grid">
        @forelse($templates as $template)
        <div class="template-card">
            <div class="template-card-icon">
                <i class="fas fa-file-word"></i>
            </div>
            <div class="template-card-body">
                <h3>{{ $template->name }}</h3>
                <span class="subtitle">{{ $template->documentType->name ?? 'Unlinked' }}</span>
                <span class="category-badge">{{ $template->documentType->category ?? 'General' }}</span>
                <p>{{ $template->description ?? 'No description available.' }}</p>
                <div class="template-card-meta">
                    <span>
                        <i class="fas fa-file-alt"></i>
                        {{ $template->file_name ?? 'template_file.docx' }}
                    </span>
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                        Last updated: {{ $template->updated_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
            <div class="template-card-actions">
                <a href="#"><i class="fas fa-eye"></i> Preview</a>
                <a href="#"><i class="fas fa-download"></i> Download</a>
                <a href="{{-- {{ route('captain.template.edit', $template->id) }} --}}" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
            </div>
        </div>
        @empty
        <div class="no-results-found" style="grid-column: 1 / -1;">
            <i class="fas fa-print"></i>
            <p>No templates found. Click "Add Template" to get started.</p>
        </div>
        @endforelse
    </div>
    
    <div class="pagination-container">
        {{ $templates->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@else {{-- This is for $view === 'types' --}}

    <h3 class="section-title">Available Document Types ({{ $documentTypes->total() }})</h3>
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
                    {{-- This is placeholder data. You'll need to add a way to store requirements. --}}
                    <li><i class="fas fa-circle"></i> Valid Government ID</li>
                    <li><i class="fas fa-circle"></i> 1x1 Recent Photo</li>
                </ul>
            </div>
            <div class="doc-type-card-footer">
                <span class="status-badge {{ $type->is_active ? 'active' : 'disabled' }}">
                    {{ $type->is_active ? 'Active' : 'Disabled' }}
                </span>
                <div class="actions">
                    <a href="{{-- {{ route('captain.document-type.edit', $type->id) }} --}}" class="edit-btn">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="delete-btn" onclick="showDeleteModal({{ $type->id }}, '{{ $type->name }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="no-results-found" style="grid-column: 1 / -1;">
            <i class="fas fa-file-alt"></i>
            <p>No document types found. Click "Add Document Type" to get started.</p>
        </div>
        @endforelse
    </div>
    
    <div class="pagination-container">
         {{ $documentTypes->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@endif


<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Delete Document Type</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove <strong id="typeName"></strong>?</p>
            <p>This action cannot be undone.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Delete</button>
            </form>
        </div>
    </div>
</div>

<div id="deleteTemplateModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="modal-title">Delete Template</div>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove <strong id="templateName"></strong>?</p>
            <p>This will not delete the document type, only the template file.</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteTemplateModal()">Cancel</button>
            <form id="deleteTemplateForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirm-delete">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- Document Type Delete Modal ---
    function showDeleteModal(id, name) {
        document.getElementById('typeName').textContent = name;
        document.getElementById('deleteForm').action = `/captain/document-type/${id}`; 
        const currentView = new URLSearchParams(window.location.search).get('view') || 'types';
        document.getElementById('deleteForm').action += `?view=${currentView}`;
        document.getElementById('deleteModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }

    // --- Template Delete Modal ---
    function showDeleteTemplateModal(id, name) {
        document.getElementById('templateName').textContent = name;
        document.getElementById('deleteTemplateForm').action = `/captain/template/${id}`;
        const currentView = new URLSearchParams(window.location.search).get('view') || 'types';
        document.getElementById('deleteTemplateForm').action += `?view=${currentView}`;
        document.getElementById('deleteTemplateModal').classList.add('show');
    }
    function closeDeleteTemplateModal() {
        document.getElementById('deleteTemplateModal').classList.remove('show');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const residentModal = document.getElementById('deleteModal');
        const householdModal = document.getElementById('deleteTemplateModal');
        if (event.target === residentModal) {
            closeDeleteModal();
        }
        if (event.target === householdModal) {
            closeDeleteTemplateModal();
        }
    }
</script>
@endsection