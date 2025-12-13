@extends('layouts.dashboard-layout')

@section('title', 'Health & Social Services')

@section('nav-items')
    {{-- Navigation for BHW Role --}}
    <li class="nav-item">
        <a href="{{ route('health.dashboard') }}" class="nav-link"> {{-- Link back to BHW Dashboard --}}
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
     <li class="nav-item">
        <a href="{{ route('health.announcements') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
{{-- STYLES --}}
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
    .icon-green { background: #10B981; } /* For Category */

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
        flex-wrap: wrap; /* Added for responsiveness */
        gap: 15px; /* Added for spacing */
    }
    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        flex-shrink: 0; /* Prevents title from shrinking */
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

    {{-- 4. Total Categories --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['total_categories'] ?? 0) }}</h3>
            <p>Total Categories</p>
        </div>
        <div class="stat-icon icon-green"><i class="fas fa-tags"></i></div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="table-title">Medicine Inventory</div>

        <div class="d-flex gap-2 flex-grow-1 justify-content-end align-items-center flex-wrap">

            <form action="{{ route('health.health-services') }}" method="GET" class="m-0">
                @if($searchQuery)
                    <input type="hidden" name="search" value="{{ $searchQuery }}">
                @endif
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 180px; text-align: left;">
                        {{ $selectedCategory ?? 'All Categories' }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li>
                            <button type="submit" name="category" value="" class="dropdown-item {{ !$selectedCategory ? 'active' : '' }}">
                                All Categories
                            </button>
                        </li>
                        
                        @foreach ($categories as $category)
                            <li>
                                <button type="submit" name="category" value="{{ $category }}" class="dropdown-item {{ $selectedCategory == $category ? 'active' : '' }}">
                                    {{ $category }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </form>

            <form action="{{ route('health.health-services') }}" method="GET" class="d-flex gap-2 m-0">
                @if($selectedCategory)
                    <input type="hidden" name="category" value="{{ $selectedCategory }}">
                @endif

                <input type="text" name="search" class="form-control" style="width: 250px;" placeholder="Search medicine..." value="{{ $searchQuery ?? '' }}">
                
                <button type="submit" class="btn btn-info text-white" title="Search">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('health.health-services') }}" class="btn btn-outline-secondary" title="Clear Filters">
                <i class="fas fa-times"></i>
            </a>

            <div class="ms-md-2 border-start-md ps-md-3">
                <a href="{{ route('health.medicine.create') }}" class="btn btn-primary" title="Add Medicine">
                    <i class="fas fa-plus"></i> Add
                </a>
                <a href="{{ route('health.medicine.requests') }}" class="btn btn-outline-secondary" title="Manage Requests">
                    <i class="fas fa-tasks"></i> Requests
                </a>
            </div>
        </div>
    </div>

    {{-- Alert for Success/Error Messages --}}
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
                    <th>Category</th> 
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
                        <td>{{ $medicine->category ?? 'N/A' }}</td> 
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
                        <td>
                            {{-- UPDATED ACTIONS --}}
                            <a href="{{ route('health.medicine.show', $medicine) }}" class="action-btn view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('health.medicine.edit', $medicine) }}" class="action-btn edit" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            
                            {{-- Delete button must be a form --}}
                            <form action="{{ route('health.medicine.destroy', $medicine) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete \'{{ $medicine->item_name }}\'? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            @if ($searchQuery || $selectedCategory)
                                No medicines found matching your filters.
                            @else
                                No medicines found in the inventory.
                            @endif
                        </td>
                    </tr>
                @endempty
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-3">
        {{ $medicines->appends(request()->query())->links() }}
    </div>
</div>

@endsection

@section('scripts')
    {{-- This page needs Bootstrap's JS for the dropdown to work. --}}
    {{-- Ensure your dashboard-layout.blade.php includes it. --}}
@endsection