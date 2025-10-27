{{-- resources/views/dashboard/captain-document-services.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Document Services')

{{-- Sidebar Navigation --}}
@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('dashboard.captain') }}" class="nav-link ">
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
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
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

{{-- Page Content --}}
@section('content')
<style>
    /* Base styles from profiling page */
    .alert { padding: 16px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
    .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
    .action-buttons { display: flex; gap: 12px; margin-bottom: 30px; }
    .btn-action { padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.3s; font-size: 0.95rem; text-decoration: none; }
    .btn-add { background: #2B5CE6; color: white; }
    .btn-add:hover { background: #1E3A8A; transform: translateY(-2px); color: white; }
    .btn-secondary { background: white; color: #333; border: 2px solid #E5E7EB; }
    .btn-secondary:hover { border-color: #2B5CE6; color: #2B5CE6; transform: translateY(-2px); }
    .view-toggles { margin-bottom: 30px; display: flex; gap: 0; border-radius: 10px; overflow: hidden; border: 2px solid #2B5CE6; width: fit-content; }
    .btn-toggle { padding: 12px 24px; border: none; font-weight: 600; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.3s; font-size: 0.95rem; text-decoration: none; background: white; color: #2B5CE6; }
    .btn-toggle.active { background: #2B5CE6; color: white; }
    .btn-toggle:not(.active):hover { background: #EFF6FF; }
    .directory-header { background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%); color: white; padding: 20px 30px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .directory-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 700; }
    .filters-section { display: flex; align-items: center; gap: 10px; }
    .search-input, .filter-select { padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 0.95rem; background: white; min-width: 140px; transition: border-color 0.3s, box-shadow 0.3s; }
    .search-input:focus, .filter-select:focus { border-color: #2B5CE6; box-shadow: 0 0 0 3px rgba(43, 92, 230, 0.2); outline: none; }
    .table-container { background: white; border-radius: 0 0 12px 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
    .table { width: 100%; margin: 0; border-collapse: collapse; }
    .table thead { background: #F9FAFB; }
    .table th { padding: 16px 20px; font-weight: 700; color: #1F2937; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; border-bottom: 2px solid #E5E7EB; }
    .table td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F3F4F6; text-align: left; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover { background: #F9FAFB; }
    .no-results-found { text-align: center; padding: 60px; }
    .no-results-found i { font-size: 3rem; color: #ccc; margin-bottom: 15px; }
    .no-results-found p { color: #999; font-size: 1.1rem; }
    .pagination-container { padding: 20px; background: white; border-radius: 0 0 12px 12px; box-shadow: 0 -2px 8px rgba(0,0,0,0.07); }
    .action-icons { display: flex; gap: 12px; }
    .action-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s; border: none; background: transparent; }
    .action-icon.view { color: #2B5CE6; }
    .action-icon.view:hover { background: #EFF6FF; }
    .action-icon.edit { color: #10B981; }
    .action-icon.edit:hover { background: #ECFDF5; }
    .action-icon.print { color: #A855F7; }
    .action-icon.print:hover { background: #F3E8FF; }

    /* Page-Specific Header */
    .document-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
    }
    .document-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .document-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    .barangay-badge { display: inline-flex; align-items: center; gap: 10px; background: rgba(255, 165, 0, 0.2); padding: 8px 16px; border-radius: 8px; font-weight: 600; }
    .barangay-badge .badge-icon { background: #FFA500; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; }
    .total-requests { position: absolute; top: 40px; right: 40px; text-align: right; }
    .total-requests-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .total-requests-count { font-size: 2.5rem; font-weight: 700; }
    .total-requests-sublabel { font-size: 0.85rem; opacity: 0.9; }
    
    /* Page-Specific Stats */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-content p { color: #666; margin: 0; font-size: 0.95rem; }
    .stat-box-icon { width: 70px; height: 70px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; }
    .icon-red-bg { background: #EF4444; }
    .icon-blue-bg { background: #3B82F6; }
    .icon-green-bg { background: #10B981; }
    .icon-yellow-bg { background: #F59E0B; }

    /* Table Specific Styles */
    .requestor-cell .name { font-weight: 600; color: #1F2937; }
    .requestor-cell .contact { font-size: 0.85rem; color: #6B7280; margin-top: 4px; }
    .badge { padding: 6px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-block; }

    /* Status Badges */
    .badge-pending { background: #FEF3C7; color: #92400E; }
    .badge-processing { background: #DBEAFE; color: #1E40AF; }
    .badge-ready { background: #D1FAE5; color: #065F46; }
    .badge-completed { background: #F3F4F6; color: #4B5563; }
    .badge-cancelled { background: #FEE2E2; color: #991B1B; }

    /* Payment Badges */
    .badge-paid { background: #D1FAE5; color: #065F46; }
    .badge-liquidated { background: #E0E7FF; color: #3730A3; }
    .badge-waived { background: #F3F4F6; color: #4B5563; }

    /* Priority Badges */
    .badge-normal { background: #F3F4F6; color: #4B5563; }
    .badge-urgent { background: #FEE2E2; color: #991B1B; }


    @media (max-width: 1200px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stats-row { grid-template-columns: 1fr; }
        .total-requests { position: static; margin-top: 20px; text-align: left; }
        .directory-header, .filters-section { flex-direction: column; align-items: flex-start; gap: 15px; }
        .search-input, .filter-select { width: 100%; }
    }
</style>

{{-- Get the current tab from the request, default to 'requests' --}}
@php $tab = request('tab', 'requests'); @endphp

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="document-header">
    <div class="document-title">Document Services</div>
    <div class="document-subtitle">Streamlined document processing and issuance system</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        {{-- Using "Calbueg" as seen in your other code --}}
        <span>Barangay Calbueg Document Center</span>
    </div>
    <div class="total-requests">
        <div class="total-requests-label">Total Requests</div>
        {{-- Assumes $totalRequests variable is passed from controller --}}
        <div class="total-requests-count">{{ $totalRequests ?? 0 }}</div> 
        <div class="total-requests-sublabel">This month</div>
    </div>
</div>

<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            {{-- Assumes $pendingRequests variable --}}
            <h3>{{ $pendingRequests ?? 0 }}</h3> 
            <p>Pending Requests</p>
        </div>
        <div class="stat-box-icon icon-red-bg">
            <i class="fas fa-hourglass-start"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            {{-- Assumes $processingRequests variable --}}
            <h3>{{ $processingRequests ?? 0 }}</h3> 
            <p>Processing</p>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-cogs"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            {{-- Assumes $readyForPickupRequests variable --}}
            <h3>{{ $readyForPickupRequests ?? 0 }}</h3> 
            <p>Ready for Pickup</p>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            {{-- Assumes $totalRevenue variable --}}
            <h3>₱{{ number_format($totalRevenue ?? 0, 2) }}</h3> 
            <p>Total Revenue</p>
        </div>
        <div class="stat-box-icon icon-yellow-bg">
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>
</div>

<div class="action-buttons">
    {{-- Assumes 'captain.document.create' route exists --}}
    <a href="{{-- route('captain.document.create') --}}" class="btn-action btn-add">
        <i class="fas fa-plus"></i>
        <span>New Request</span>
    </a>
    <button class="btn-action btn-secondary">
        <i class="fas fa-download"></i>
        <span>Export Data</span>
    </button>
</div>

<div class="view-toggles">
    {{-- Assumes 'captain.document-services' route exists --}}
    <a href="{{ route('captain.document-services', ['tab' => 'requests']) }}" class="btn-toggle {{ $tab === 'requests' ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i>
        <span>Document Requests</span>
    </a>
    <a href="{{ route('captain.document-services', ['tab' => 'types']) }}" class="btn-toggle {{ $tab === 'types' ? 'active' : '' }}">
        <i class="fas fa-list-ul"></i>
        <span>Document Types</span>
    </a>
    <a href="{{ route('captain.document-services', ['tab' => 'templates']) }}" class="btn-toggle {{ $tab === 'templates' ? 'active' : '' }}">
        <i class="fas fa-clone"></i>
        <span>Templates</span>
    </a>
</div>

@if($tab === 'requests')

    <div class="directory-header">
        <div class="directory-title">
            <i class="fas fa-file-alt"></i>
            <span>Document Requests ({{ $documents->total() ?? 0 }})</span>
        </div>
        <form method="GET" action="{{ route('captain.document-services') }}" class="filters-section">
            <input type="hidden" name="tab" value="requests">
            <input type="text" name="search" class="search-input" placeholder="🔍 Search requests, tracking..." value="{{ request('search') }}">
            
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                {{-- These should be populated from the database, but hardcoding for example --}}
                <option value="Barangay Clearance" {{ request('type') == 'Barangay Clearance' ? 'selected' : '' }}>Barangay Clearance</option>
                <option value="Certificate of Residency" {{ request('type') == 'Certificate of Residency' ? 'selected' : '' }}>Certificate of Residency</option>
                <option value="Certificate of Indigency" {{ request('type') == 'Certificate of Indigency' ? 'selected' : '' }}>Certificate of Indigency</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tracking #</th>
                    <th>Requestor</th>
                    <th>Document Type</th>
                    <th>Purpose</th>
                    <th>Date</th>
                    <th>Priority</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Assumes $documents variable is passed and is paginated --}}
                @forelse($documents as $doc)
                <tr>
                    <td><strong>{{ $doc->tracking_number ?? 'N/A' }}</strong></td>
                    <td>
                        <div class="requestor-cell">
                            <div class="name">{{ $doc->requestor_name ?? 'N/A' }}</div>
                            <div class="contact">{{ $doc->contact_number ?? 'N/A' }}</div>
                        </div>
                    </td>
                    <td>{{ $doc->document_type ?? 'N/A' }}</td>
                    <td>{{ $doc->purpose ?? 'N/A' }}</td>
                    <td>{{ $doc->created_at ? $doc->created_at->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        @php
                            $priorityClass = 'badge-normal';
                            if (strtolower($doc->priority ?? 'normal') === 'urgent') $priorityClass = 'badge-urgent';
                        @endphp
                        <span class="badge {{ $priorityClass }}">{{ ucfirst($doc->priority ?? 'Normal') }}</span>
                    </td>
                    <td>
                        @php
                            $paymentClass = 'badge-paid'; // Default
                            if (strtolower($doc->payment_status ?? 'paid') === 'liquidated') $paymentClass = 'badge-liquidated';
                            if (strtolower($doc->payment_status ?? 'paid') === 'waived') $paymentClass = 'badge-waived';
                        @endphp
                        <span class="badge {{ $paymentClass }}">{{ ucfirst($doc->payment_status ?? 'Paid') }}</span>
                    </td>
                    <td>
                         @php
                            $statusClass = 'badge-pending'; // Default
                            if (strtolower($doc->status ?? 'pending') === 'processing') $statusClass = 'badge-processing';
                            if (strtolower($doc->status ?? 'pending') === 'ready') $statusClass = 'badge-ready';
                            if (strtolower($doc->status ?? 'pending') === 'completed') $statusClass = 'badge-completed';
                            if (strtolower($doc->status ?? 'pending') === 'cancelled') $statusClass = 'badge-cancelled';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($doc->status ?? 'Pending') }}</span>
                    </td>
                    <td>
                        <div class="action-icons">
                            {{-- Assumes these routes exist --}}
                            <a href="{{-- route('captain.document.show', $doc->id) --}}" class="action-icon view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{-- route('captain.document.edit', $doc->id) --}}" class="action-icon edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{-- route('captain.document.print', $doc->id) --}}" class="action-icon print" title="Generate/Print">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="no-results-found">
                            <i class="fas fa-file-alt"></i>
                            <p>No document requests found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination-container">
        {{ $documents->withQueryString()->links() }}
    </div>

@elseif($tab === 'types')

    <div class="no-results-found">
        <i class="fas fa-list-ul"></i>
        <p>Document Types Management<br>(Feature under development)</p>
    </div>

@elseif($tab === 'templates')

    <div class="no-results-found">
        <i class="fas fa-clone"></i>
        <p>Document Templates Management<br>(Feature under development)</p>
    </div>

@endif

{{-- You can add delete modals here if needed, similar to the profiling page --}}

@endsection