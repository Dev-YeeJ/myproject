@extends('layouts.dashboard-layout')

@section('title', 'Health & Social Services')

@section('nav-items')
    {{-- Navigation for BHW Role --}}
    <li class="nav-item">
        <a href="{{ route('health.dashboard') }}" class="nav-link">
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
    .header-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .header-subtitle { opacity: 0.95; font-size: 1rem; }

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
    .stat-info h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-info p { color: #666; margin: 0; font-size: 0.95rem; }
    .stat-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue { background: #2B5CE6; }
    .icon-yellow { background: #F59E0B; }
    .icon-red { background: #EF4444; }
    .icon-green { background: #10B981; }

    /* Custom Tabs (Bootstrap 4 Style) */
    .nav-tabs { border-bottom: 2px solid #E5E7EB; margin-bottom: 20px; }
    .nav-tabs .nav-link { 
        border: none; color: #6B7280; font-weight: 600; padding: 12px 20px; font-size: 1.1rem; 
        background: transparent;
    }
    .nav-tabs .nav-link:hover { color: #2B5CE6; border-color: transparent; }
    .nav-tabs .nav-link.active { 
        color: #2B5CE6; border-bottom: 3px solid #2B5CE6; background: transparent; 
    }

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
    }
    .table-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 0; }

    /* Table styles */
    .table th { font-weight: 600; color: #4B5563; background: #F9FAFB; border-top: none; }
    .table td { vertical-align: middle; border-top: 1px solid #dee2e6; }
    
    /* Program Badges */
    .badge-upcoming { background-color: #3B82F6; color: white; }
    .badge-completed { background-color: #10B981; color: white; }
    .badge-cancelled { background-color: #EF4444; color: white; }

    /* Responsive adjustments */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
</style>

{{-- CONTENT START --}}

<div class="header-section">
    <div class="header-title">Health & Social Services</div>
    <div class="header-subtitle">Manage medicine inventory, requests, and barangay health programs.</div>
</div>

{{-- STATS GRID --}}
<div class="stats-grid">
    {{-- 1. Total Medicines --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['total_medicines'] ?? 0) }}</h3>
            <p>Inventory Items</p>
        </div>
        <div class="stat-icon icon-blue"><i class="fas fa-pills"></i></div>
    </div>

    {{-- 2. Upcoming Programs --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['upcoming_programs'] ?? 0) }}</h3>
            <p>Upcoming Programs</p>
        </div>
        <div class="stat-icon icon-green"><i class="fas fa-calendar-alt"></i></div>
    </div>

    {{-- 3. Low Stock --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['low_stock_medicines'] ?? 0) }}</h3>
            <p>Low Stock Items</p>
        </div>
        <div class="stat-icon icon-yellow"><i class="fas fa-exclamation-triangle"></i></div>
    </div>

    {{-- 4. Pending Requests --}}
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($stats['pending_requests'] ?? 0) }}</h3>
            <p>Pending Requests</p>
        </div>
        <div class="stat-icon icon-red"><i class="fas fa-user-clock"></i></div>
    </div>
</div>

{{-- ALERTS (BS4 uses data-dismiss) --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- TABS NAVIGATION (BS4 uses href="#id") --}}
<ul class="nav nav-tabs" id="healthTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab" aria-controls="inventory" aria-selected="true">
            <i class="fas fa-boxes mr-2"></i> Medicine Inventory
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="programs-tab" data-toggle="tab" href="#programs" role="tab" aria-controls="programs" aria-selected="false">
            <i class="fas fa-calendar-check mr-2"></i> Health Programs
        </a>
    </li>
</ul>

{{-- TABS CONTENT --}}
<div class="tab-content" id="healthTabsContent">

    {{-- ========================================================= --}}
    {{-- TAB 1: MEDICINE INVENTORY --}}
    {{-- ========================================================= --}}
    <div class="tab-pane fade show active" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
        <div class="table-container">
            <div class="table-header">
                <div class="table-title">Medicine Inventory</div>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    {{-- Category Dropdown (BS4 data-toggle) --}}
                    <form action="{{ route('health.health-services') }}" method="GET" class="m-0">
                        @if($searchQuery) <input type="hidden" name="search" value="{{ $searchQuery }}"> @endif
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width: 180px; text-align: left;">
                                {{ $selectedCategory ?? 'All Categories' }}
                            </button>
                            <div class="dropdown-menu">
                                <button type="submit" name="category" value="" class="dropdown-item">All Categories</button>
                                @foreach ($categories as $category)
                                    <button type="submit" name="category" value="{{ $category }}" class="dropdown-item">{{ $category }}</button>
                                @endforeach
                            </div>
                        </div>
                    </form>

                    {{-- Search Form --}}
                    <form action="{{ route('health.health-services') }}" method="GET" class="d-flex m-0">
                        @if($selectedCategory) <input type="hidden" name="category" value="{{ $selectedCategory }}"> @endif
                        <input type="text" name="search" class="form-control mr-2" style="width: 250px;" placeholder="Search medicine..." value="{{ $searchQuery ?? '' }}">
                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-search"></i></button>
                    </form>

                    {{-- Clear Filter --}}
                    <a href="{{ route('health.health-services') }}" class="btn btn-outline-secondary ml-1" title="Clear Filters"><i class="fas fa-times"></i></a>

                    {{-- Action Buttons --}}
                    <div class="ml-2 pl-2 border-left">
                        <a href="{{ route('health.medicine.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                        <a href="{{ route('health.medicine.requests') }}" class="btn btn-outline-secondary"><i class="fas fa-tasks"></i> Requests</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Medicine Name</th>
                            <th>Brand</th>
                            <th>Category</th> 
                            <th>Dosage</th>
                            <th>Quantity</th>
                            <th>Expiration</th>
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
                                    <span class="{{ $medicine->status === 'Low Stock' || $medicine->status === 'Out of Stock' ? 'text-danger font-weight-bold' : '' }}">
                                        {{ $medicine->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $medicine->status === 'Expired' ? 'text-danger font-weight-bold' : '' }}">
                                        {{ $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($medicine->status === 'In Stock') <span class="badge badge-success">In Stock</span>
                                    @elseif ($medicine->status === 'Low Stock') <span class="badge badge-warning">Low Stock</span>
                                    @elseif ($medicine->status === 'Expired') <span class="badge badge-danger">Expired</span>
                                    @else <span class="badge badge-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('health.medicine.show', $medicine) }}" class="btn btn-sm btn-light text-primary" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('health.medicine.edit', $medicine) }}" class="btn btn-sm btn-light text-warning" title="Edit"><i class="fas fa-pen"></i></a>
                                    <form action="{{ route('health.medicine.destroy', $medicine) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No medicines found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $medicines->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TAB 2: HEALTH PROGRAMS --}}
    {{-- ========================================================= --}}
    <div class="tab-pane fade" id="programs" role="tabpanel" aria-labelledby="programs-tab">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0 font-weight-bold text-primary">Barangay Health Programs</h5>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProgramModal">
                    <i class="fas fa-plus-circle"></i> New Program
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Program Title</th>
                            <th>Schedule</th>
                            <th>Location</th>
                            <th>Organizer</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programs as $program)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $program->title }}</div>
                                    @if($program->description)
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                            {{ $program->description }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $program->schedule_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $program->schedule_date->format('h:i A') }}</small>
                                </td>
                                <td>{{ $program->location }}</td>
                                <td>{{ $program->organizer ?? 'N/A' }}</td>
                                <td>
                                    @if($program->status == 'Upcoming') <span class="badge badge-upcoming">Upcoming</span>
                                    @elseif($program->status == 'Completed') <span class="badge badge-completed">Completed</span>
                                    @else <span class="badge badge-cancelled">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Edit Button (Triggers Modal) --}}
                                    <button class="btn btn-sm btn-light text-primary" data-toggle="modal" data-target="#editProgramModal{{ $program->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    {{-- Delete Form --}}
                                    <form action="{{ route('health.programs.destroy', $program->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this program?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- MODAL: EDIT PROGRAM (Inside Loop) --}}
                            <div class="modal fade" id="editProgramModal{{ $program->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('health.programs.update', $program->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Program</h5>
                                                {{-- BS4 Close Button --}}
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $program->title }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Date & Time</label>
                                                    <input type="datetime-local" name="schedule_date" class="form-control" value="{{ $program->schedule_date->format('Y-m-d\TH:i') }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Location</label>
                                                    <input type="text" name="location" class="form-control" value="{{ $program->location }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Organizer</label>
                                                    <input type="text" name="organizer" class="form-control" value="{{ $program->organizer }}">
                                                </div>
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="Upcoming" {{ $program->status == 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                                                        <option value="Completed" {{ $program->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="Cancelled" {{ $program->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $program->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">No health programs scheduled.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $programs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL: CREATE PROGRAM --}}
<div class="modal fade" id="addProgramModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('health.programs.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Create New Health Program</h5>
                    {{-- BS4 Close Button with white text --}}
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Program Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Libreng Bakuna" required>
                    </div>
                    <div class="form-group">
                        <label>Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="schedule_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Barangay Hall" required>
                    </div>
                    <div class="form-group">
                        <label>Organizer (Optional)</label>
                        <input type="text" name="organizer" class="form-control" placeholder="e.g. Dr. Juan Cruz">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Details about the program..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Program</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
{{-- Scripts are loaded in layout, no extra scripts needed here --}}
@endsection