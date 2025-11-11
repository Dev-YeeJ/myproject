@extends('layouts.dashboard-layout')

@section('title', 'Health & Social Services (View-Only)')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
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
        {{-- UPDATED: Link to the new document services route --}}
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new health services route --}}
        <a href="{{ route('captain.health-services') }}" class="nav-link active">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-check-circle"></i>
            <span>SK Module</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')
{{-- STYLES (Copied from your BHW view for consistency) --}}
<style>
    /* Header styles */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px;
    }
    .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .header-subtitle {
        opacity: 0.95;
        font-size: 1rem;
    }

    /* Stats Grid styles */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-info h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    .stat-info p {
        color: #666;
        margin: 0 0 8px 0;
        font-size: 0.95rem;
    }
    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    .icon-blue { background: #2B5CE6; }
    .icon-yellow { background: #F59E0B; }
    .icon-red { background: #EF4444; }
    .icon-purple { background: #A855F7; }

    /* Table Container styles */
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
    }
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    .table-search {
        width: 300px;
    }

    /* Table styles */
    .table-wrapper {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    .table th, .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #E5E7EB;
        vertical-align: middle;
        font-size: 0.95rem;
    }
    .table th {
        font-weight: 600;
        color: #4B5563;
        background: #F9FAFB;
    }
    .table tbody tr:hover {
        background: #F9FAFB;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .table-search {
            width: 100%;
        }
    }
</style>

{{-- CONTENT: Medicine Inventory (View-Only) --}}

<div class="header-section">
    <div class="header-title">Health & Social Services</div>
    <div class="header-subtitle">View medicine inventory and resident assistance requests.</div>
</div>

<div class="stats-grid">
    {{-- 1. Total Medicines --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['total_medicines'] ?? 0) }}</h3>
            <p>Medicine Types</p>
        </div>
        <div class="stat-icon icon-blue"><i class="fas fa-pills"></i></div>
    </div>

    {{-- 2. Low Stock --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['low_stock_medicines'] ?? 0) }}</h3>
            <p>Low Stock Items</p>
        </div>
        <div class="stat-icon icon-yellow"><i class="fas fa-exclamation-triangle"></i></div>
    </div>

    {{-- 3. Expired --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['expired_medicines'] ?? 0) }}</h3>
            <p>Expired Items</p>
        </div>
        <div class="stat-icon icon-red"><i class="fas fa-calendar-times"></i></div>
    </div>
    
    {{-- 4. Pending Requests (Placeholder) --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['pending_requests'] ?? 0) }}</h3>
            <p>Pending Requests</p>
        </div>
        <div class="stat-icon icon-purple"><i class="fas fa-tasks"></i></div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Medicine Inventory</div>

        {{-- 
            MODIFICATION: 
            The "Add Medicine" and "Manage Requests" buttons have been removed 
            to make this page view-only for the Captain.
        --}}
    </div>

    {{-- Alert for Success/Error Messages (from other pages) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Brand</th>
                    {{-- <th>Category</th>  --}} {{-- Category was missing from your captain store/create logic --}}
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Expiration Date</th>
                    <th>Status</th>
                    {{-- MODIFICATION: Removed "Actions" column --}}
                </tr>
            </thead>
            <tbody>
                @forelse ($medicines as $medicine)
                    <tr>
                        <td><strong>{{ $medicine->item_name }}</strong></td>
                        <td>{{ $medicine->brand_name ?? 'N/A' }}</td>
                        {{-- <td>{{ $medicine->category ?? 'N/A' }}</td> --}}
                        <td>{{ $medicine->dosage }}</td>
                        <td>
                            <span class="{{ $medicine->status === 'Low Stock' || $medicine->status === 'Out of Stock' ? 'text-danger fw-bold' : '' }}">
                                {{ $medicine->quantity }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $medicine->status === 'Expired' ? 'text-danger fw-bold' : '' }}">
                                {{ $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if ($medicine->status === 'In Stock')
                                <span class="badge bg-success">{{ $medicine->status }}</span>
                            @elseif ($medicine->status === 'Low Stock')
                                <span class="badge bg-warning">{{ $medicine->status }}</span>
                            @elseif ($medicine->status === 'Expired')
                                <span class="badge bg-danger">{{ $medicine->status }}</span>
                            @else {{-- Out of Stock --}}
                                <span class="badge bg-danger">{{ $medicine->status }}</span>
                            @endif
                        </td>
                        {{-- MODIFICATION: Removed "Actions" column --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            No medicines found in the inventory.
                        </td>
                    </tr>
                @endempty
            </tbody>
        </table>
    </div>
</div>

@endsection