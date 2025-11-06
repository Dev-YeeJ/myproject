{{-- resources/views/dashboard/health-health-services.blade.php --}}
{{-- This is the Health Services page for the BHW (Health) role --}}

@extends('layouts.dashboard-layout')

@section('title', 'Health & Social Services')

@section('nav-items')
    {{-- Navigation for BHW Role --}}
    <li class="nav-item">
        <a href="{{ route('dashboard.health') }}" class="nav-link"> {{-- Link back to BHW Dashboard --}}
            <i class="fas fa-home"></i>
            <span>Dashboard</span>   
        </a>
    </li>
    <li class="nav-item">
        {{-- This is now the active page --}}
        <a href="{{ route('health.health-services') }}" class="nav-link active"> 
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
@endsection

@section('content')
{{-- STYLES: Copied from captain-health-services.blade.php --}}
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
    }
    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
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

    /* Action buttons in table */
    .action-btn {
        background: none;
        border: none;
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
        margin-right: 5px;
    }
    .action-btn.view { color: #2B5CE6; }
    .action-btn.view:hover { background: #DBEAFE; }
    .action-btn.edit { color: #F59E0B; }
    .action-btn.edit:hover { background: #FEF3C7; }
    .action-btn.delete { color: #EF4444; }
    .action-btn.delete:hover { background: #FEE2E2; }

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
        .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .table-search {
            width: 100%;
        }
    }
</style>

{{-- CONTENT: Medicine Inventory --}}

<div class="header-section">
    <div class="header-title">Health & Social Services</div>
    <div class="header-subtitle">Manage medicine inventory and resident assistance requests.</div>
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

    {{-- 4. Pending Requests --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['pending_requests'] ?? 0) }}</h3>
            <p>Pending Requests</p>
        </div>
        <div class="stat-icon icon-purple"><i class="fas fa-folder-open"></i></div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Medicine Inventory</div>
        <div class="d-flex gap-2">
            <input type="text" class="form-control table-search" placeholder="Search medicine...">
            
            {{-- BHW-specific routes --}}
            <a href="{{ route('health.medicine.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Medicine
            </a>
            <a href="{{ route('health.medicine.requests') }}" class="btn btn-outline-secondary">
                <i class="fas fa-tasks"></i> Manage Requests
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Brand</th>
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Expiration Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($medicines as $medicine)
                    <tr>
                        <td><strong>{{ $medicine->item_name }}</strong></td>
                        <td>{{ $medicine->brand_name ?? 'N/A' }}</td>
                        <td>{{ $medicine->dosage }}</td>
                        <td>
                            {{-- Style if low stock --}}
                            <span class="{{ $medicine->status === 'Low Stock' || $medicine->status === 'Out of Stock' ? 'text-danger fw-bold' : '' }}">
                                {{ $medicine->quantity }}
                            </span>
                        </td>
                        <td>
                            {{-- Style if expired --}}
                            <span class="{{ $medicine->status === 'Expired' ? 'text-danger fw-bold' : '' }}">
                                {{ $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            {{-- Status Badge --}}
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
                        <td>
                            {{-- Placeholder buttons --}}
                            <button class="action-btn view" title="View"><i class="fas fa-eye"></i></button>
                            <button class="action-btn edit" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No medicines found in the inventory.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
    {{-- You can add specific JS here if needed --}}
@endsection