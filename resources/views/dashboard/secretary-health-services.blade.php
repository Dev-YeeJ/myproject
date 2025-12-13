@extends('layouts.dashboard-layout')

@section('title', 'Health & Social Services')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('secretary.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.resident-profiling') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.financial-management') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- ACTIVE PAGE --}}
        <a href="{{ route('secretary.health-services') }}" class="nav-link active">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.incident-blotter') }}" class="nav-link">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.project-monitoring') }}" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.announcements.index') }}" class="nav-link">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('secretary.sk-overview') }}" class="nav-link">
            <i class="fas fa-user-graduate"></i>
            <span>SK Module</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- HEADER STYLES --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
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

    /* --- STATS BOXES --- */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
    }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    
    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }

    .icon-blue-bg { background: #2B5CE6; } .stat-badge.blue { color: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; } .stat-badge.orange { color: #FF8C42; }
    .icon-green-bg { background: #10B981; } .stat-badge.green { color: #10B981; }
    .icon-red-bg { background: #EF4444; } .stat-badge.red { color: #EF4444; }
    .icon-purple-bg { background: #A855F7; } .stat-badge.purple { color: #A855F7; }

    /* --- DIRECTORY HEADER & FILTER --- */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 20px 30px; border-radius: 12px 12px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .directory-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 700; }
    
    .filters-section { display: flex; align-items: center; gap: 10px; }
    .search-input {
        padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.95rem; background: white; min-width: 250px;
        transition: border-color 0.3s;
    }
    .search-input:focus { outline: none; border-color: #2B5CE6; }

    /* --- TABLE STYLES --- */
    .table-container {
        background: white; border-radius: 0 0 12px 12px;
        overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    }
    .table { width: 100%; margin: 0; border-collapse: collapse; }
    .table thead { background: #F9FAFB; }
    .table th {
        padding: 16px 20px; font-weight: 700; color: #1F2937;
        font-size: 0.9rem; text-transform: uppercase; border-bottom: 2px solid #E5E7EB; text-align: left;
    }
    .table td { padding: 18px 20px; vertical-align: middle; border-bottom: 1px solid #F3F4F6; color: #4B5563; }
    .table tbody tr:hover { background: #F9FAFB; }

    /* --- BADGES --- */
    .badge { padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
    .bg-success { background: #D1FAE5 !important; color: #065F46; }
    .bg-warning { background: #FEF3C7 !important; color: #92400E; }
    .bg-danger { background: #FEE2E2 !important; color: #991B1B; }

    .no-results { text-align: center; padding: 40px; color: #9CA3AF; }
</style>

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Health & Social Services</div>
    <div class="profiling-subtitle">View medicine inventory and track supplies managed by Health Workers.</div>
    <div class="barangay-badge">
        <span class="badge-icon">HC</span>
        <span>Barangay Health Center</span>
    </div>
    
    {{-- Button Removed as requested --}}
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ number_format($stats['total_medicines'] ?? 0) }}</h3>
            <p>Total Medicines</p>
            <div class="stat-badge blue"><i class="fas fa-pills"></i><span>Inventory</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-capsules"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ number_format($stats['low_stock_medicines'] ?? 0) }}</h3>
            <p>Low Stock Items</p>
            <div class="stat-badge orange"><i class="fas fa-exclamation-triangle"></i><span>Restock Needed</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-cubes"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ number_format($stats['expired_medicines'] ?? 0) }}</h3>
            <p>Expired Items</p>
            <div class="stat-badge red"><i class="fas fa-ban"></i><span>Dispose</span></div>
        </div>
        <div class="stat-box-icon icon-red-bg"><i class="fas fa-calendar-times"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ number_format($stats['pending_requests'] ?? 0) }}</h3>
            <p>Pending Requests</p>
            <div class="stat-badge purple"><i class="fas fa-clock"></i><span>Action Required</span></div>
        </div>
        <div class="stat-box-icon icon-purple-bg"><i class="fas fa-hand-holding-medical"></i></div>
    </div>
</div>

{{-- Directory Header --}}
<div class="directory-header">
    <div class="directory-title">
        <i class="fas fa-medkit"></i>
        <span>Medicine Inventory</span>
    </div>
    <div class="filters-section">
        <input type="text" class="search-input" placeholder="🔍 Search medicine name or brand...">
    </div>
</div>

{{-- Table --}}
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Medicine Name</th>
                <th>Brand</th>
                <th>Dosage</th>
                <th>Stock Quantity</th>
                <th>Expiration Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicines as $medicine)
            <tr>
                <td style="font-weight: 600; color: #1F2937;">{{ $medicine->item_name }}</td>
                <td>{{ $medicine->brand_name ?? 'Generic' }}</td>
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
                        <span class="badge bg-success">IN STOCK</span>
                    @elseif ($medicine->status === 'Low Stock')
                        <span class="badge bg-warning">LOW STOCK</span>
                    @elseif ($medicine->status === 'Expired')
                        <span class="badge bg-danger">EXPIRED</span>
                    @else
                        <span class="badge bg-danger">OUT OF STOCK</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="no-results">
                        <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                        <p>No medicines found in the inventory.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(method_exists($medicines, 'links'))
    <div class="p-3 bg-white border-top">
        {{ $medicines->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection