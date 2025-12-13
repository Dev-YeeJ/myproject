@extends('layouts.dashboard-layout')

@section('title', 'Manage Medicine Requests')

@section('nav-items')
    {{-- Navigation for BHW Role --}}
    <li class="nav-item">
        <a href="{{ route('health.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>    
        </a>
    </li>
    <li class="nav-item">
        {{-- Keep this active as it's part of the Health Services module --}}
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
{{-- STYLES (Matching Health Services Page) --}}
<style>
    /* Header styles */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-content .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .header-content .header-subtitle {
        opacity: 0.95;
        font-size: 1rem;
    }
    .header-actions .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .header-actions .btn-back:hover {
        background: white;
        color: #1E3A8A;
    }

    /* Stats Grid styles */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
        text-decoration: none; /* For when using cards as links */
        transition: transform 0.2s;
        border-bottom: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .stat-card.active {
        background: #F9FAFB;
        border-color: #2B5CE6;
    }
    
    .stat-info h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: #1F2937;
    }
    .stat-info p {
        color: #666;
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }
    .icon-yellow { background: #F59E0B; }
    .icon-green { background: #10B981; }
    .icon-red { background: #EF4444; }

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
        padding-bottom: 15px;
        border-bottom: 1px solid #E5E7EB;
    }
    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1F2937;
    }
    .status-filter {
        display: flex;
        gap: 10px;
    }
    .filter-btn {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        color: #6B7280;
        background: #F3F4F6;
        transition: all 0.2s;
    }
    .filter-btn:hover { background: #E5E7EB; color: #374151; }
    .filter-btn.active {
        background: #2B5CE6;
        color: white;
    }

    /* Table styles */
    .table-wrapper { overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
    .table th, .table td {
        padding: 16px 20px;
        text-align: left;
        border-bottom: 1px solid #E5E7EB;
        vertical-align: middle;
        font-size: 0.95rem;
    }
    .table th {
        font-weight: 600;
        color: #4B5563;
        background: #F9FAFB;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .table tbody tr:hover { background: #F9FAFB; }

    .user-info { font-weight: 600; color: #1F2937; }
    .user-sub { font-size: 0.85rem; color: #9CA3AF; }

    /* Badges & Buttons */
    .badge { padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; }
    .badge-stock-ok { background: #D1FAE5; color: #059669; }
    .badge-stock-low { background: #FEE2E2; color: #DC2626; }

    .btn-action { border: none; padding: 8px 14px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-approve { background: #10B981; color: white; }
    .btn-approve:hover { background: #059669; }
    .btn-reject { background: #EF4444; color: white; }
    .btn-reject:hover { background: #DC2626; }
    
    /* Modal */
    .modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
    .modal.show { display: flex; }
    .modal-box { background: white; padding: 30px; border-radius: 16px; width: 90%; max-width: 450px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    
    /* Responsive */
    @media (max-width: 992px) {
        .stats-grid { grid-template-columns: 1fr; }
        .header-section { flex-direction: column; align-items: flex-start; gap: 20px; }
    }
</style>

{{-- 1. Header Section --}}
<div class="header-section">
    <div class="header-content">
        <div class="header-title">Medicine Requests</div>
        <div class="header-subtitle">Review and manage resident requests for medicines.</div>
    </div>
    <div class="header-actions">
        <a href="{{ route('health.health-services') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

{{-- 2. Stats Grid (Clickable Filters) --}}
<div class="stats-grid">
    <a href="?status=Pending" class="stat-card {{ $status == 'Pending' ? 'active' : '' }}">
        <div class="stat-info">
            <h3>{{ number_format($counts['Pending']) }}</h3>
            <p>Pending Requests</p>
        </div>
        <div class="stat-icon icon-yellow"><i class="fas fa-clock"></i></div>
    </a>

    <a href="?status=Approved" class="stat-card {{ $status == 'Approved' ? 'active' : '' }}">
        <div class="stat-info">
            <h3>{{ number_format($counts['Approved']) }}</h3>
            <p>Approved / Completed</p>
        </div>
        <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
    </a>

    <a href="?status=Rejected" class="stat-card {{ $status == 'Rejected' ? 'active' : '' }}">
        <div class="stat-info">
            <h3>{{ number_format($counts['Rejected']) }}</h3>
            <p>Rejected Requests</p>
        </div>
        <div class="stat-icon icon-red"><i class="fas fa-times-circle"></i></div>
    </a>
</div>

{{-- 3. Main Table Content --}}
<div class="table-container">
    
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success mb-4" style="background:#ECFDF5; color:#065F46; padding:15px; border-radius:8px; border:1px solid #A7F3D0;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4" style="background:#FEF2F2; color:#991B1B; padding:15px; border-radius:8px; border:1px solid #FECACA;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="table-header">
        <div class="table-title">{{ $status }} Requests</div>
        
        {{-- Alternative small filters if needed --}}
        <div class="status-filter">
            <span style="font-size: 0.9rem; color: #6B7280;">Showing {{ $requests->count() }} records</span>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Resident Name</th>
                    <th>Medicine Requested</th>
                    <th>Quantity Needed</th>
                    <th>Inventory Status</th>
                    <th>Date Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div class="user-info">{{ $req->resident->user->last_name }}, {{ $req->resident->user->first_name }}</div>
                        <div class="user-sub">ID: {{ $req->resident_id }}</div>
                    </td>
                    <td>
                        <div class="user-info">{{ $req->medicine->item_name }}</div>
                        <div class="user-sub">{{ $req->medicine->dosage }}</div>
                    </td>
                    <td>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #1F2937;">{{ $req->quantity_requested }}</span> units
                    </td>
                    <td>
                        @if($status == 'Pending')
                            @if($req->medicine->quantity >= $req->quantity_requested)
                                <span class="badge badge-stock-ok">
                                    <i class="fas fa-check"></i> In Stock: {{ $req->medicine->quantity }}
                                </span>
                            @else
                                <span class="badge badge-stock-low">
                                    <i class="fas fa-exclamation-triangle"></i> Low: {{ $req->medicine->quantity }}
                                </span>
                            @endif
                        @else
                            <span style="color: #9CA3AF;">—</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $req->created_at->format('M d, Y') }}</div>
                        <div class="user-sub">{{ $req->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        @if($status == 'Pending')
                            <div style="display: flex; gap: 10px;">
                                <form action="{{ route('health.medicine.request.update', $req->id) }}" method="POST" onsubmit="return confirm('Approve this request? Stock will be deducted.');">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="Approved">
                                    <button type="submit" class="btn-action btn-approve" {{ $req->medicine->quantity < $req->quantity_requested ? 'disabled style=opacity:0.5;cursor:not-allowed;' : '' }}>
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn-action btn-reject" onclick="openRejectModal({{ $req->id }})">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        @else
                            <div style="color: #6B7280; font-size: 0.9rem;">
                                @if($status == 'Approved')
                                    <span style="color: #10B981; font-weight: 600;"><i class="fas fa-check-circle"></i> Approved</span>
                                @else
                                    <span style="color: #EF4444; font-weight: 600;"><i class="fas fa-times-circle"></i> Rejected</span>
                                    <div style="font-size: 0.8rem; margin-top: 4px;">"{{ $req->remarks }}"</div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 60px; color: #9CA3AF;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block; color: #E5E7EB;"></i>
                        No {{ strtolower($status) }} requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->appends(['status' => $status])->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="modal">
    <div class="modal-box">
        <h3 style="margin-top: 0; color: #1F2937; font-size: 1.5rem; margin-bottom: 20px;">Reject Request</h3>
        <p style="color: #6B7280; margin-bottom: 20px;">Please provide a reason for rejecting this request. This will be visible to the resident.</p>
        
        <form id="rejectForm" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="Rejected">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #374151; font-weight: 600;">Reason for Rejection</label>
                <textarea name="remarks" class="form-control" rows="4" required 
                    style="width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-family: inherit;"
                    placeholder="e.g., Out of stock, Incomplete requirements..."></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeRejectModal()" 
                    style="padding: 10px 20px; border: 1px solid #D1D5DB; background: white; border-radius: 8px; cursor: pointer; font-weight: 600; color: #4B5563;">
                    Cancel
                </button>
                <button type="submit" class="btn-action btn-reject">
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id) {
        document.getElementById('rejectForm').action = "/health/medicine/requests/" + id;
        document.getElementById('rejectModal').classList.add('show');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('show');
    }
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            closeRejectModal();
        }
    }
</script>
@endsection