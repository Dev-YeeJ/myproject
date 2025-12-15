@extends('layouts.dashboard-layout')

@section('title', 'Health Services')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('resident.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('resident.health-services') }}" class="nav-link active">
            <i class="fas fa-heartbeat"></i>
            <span>Health Services</span>
        </a>
    </li>

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
    /* --- Blue Theme Palette --- */
    :root {
        --primary-blue: #2B5CE6;
        --dark-blue: #1E3A8A;
        --light-blue-bg: #EFF6FF;
        --text-dark: #1F2937;
        --text-grey: #6B7280;
    }

    /* Header */
    .health-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        color: white; padding: 40px; border-radius: 16px;
        margin-bottom: 30px; display: flex; flex-direction: column;
        justify-content: center;
    }
    .health-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .health-subtitle { opacity: 0.9; font-size: 1rem; max-width: 600px; }

    /* Stats */
    .stats-container {
        display: grid; grid-template-columns: repeat(3, 1fr);
        gap: 20px; margin-bottom: 30px;
    }
    .stat-bar {
        background: white; border-radius: 12px;
        padding: 20px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex; align-items: center; justify-content: space-between;
        border-left: 5px solid transparent;
    }
    .stat-bar.pending { border-left-color: #F59E0B; }
    .stat-bar.approved { border-left-color: #10B981; }
    .stat-bar.rejected { border-left-color: #EF4444; }

    .stat-label { font-size: 0.95rem; color: var(--text-grey); font-weight: 600; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-dark); line-height: 1; }
    .stat-icon-circle {
        width: 50px; height: 50px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }
    .bg-light-orange { background: #FEF3C7; color: #D97706; }
    .bg-light-green { background: #D1FAE5; color: #059669; }
    .bg-light-red { background: #FEE2E2; color: #DC2626; }

    /* View Toggles */
    .view-switcher {
        display: flex; justify-content: flex-start; gap: 10px;
        margin-bottom: 25px; border-bottom: 1px solid #E5E7EB;
        padding-bottom: 10px;
    }
    .switch-btn {
        background: transparent; border: none; padding: 10px 20px;
        font-weight: 600; color: var(--text-grey); cursor: pointer;
        border-radius: 8px; transition: all 0.2s;
        display: flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .switch-btn.active { background: var(--light-blue-bg); color: var(--primary-blue); }
    .switch-btn:hover:not(.active) { background: #F3F4F6; color: var(--text-dark); }

    /* Content Layout - Wide Cards (2 Columns) */
    .medicine-grid {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;
    }
    .med-card {
        background: white; border: 1px solid #E5E7EB;
        border-radius: 12px; padding: 20px;
        display: flex; flex-direction: column;
        transition: transform 0.2s;
    }
    .med-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    
    .med-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 12px;
    }
    .med-name { font-size: 1.2rem; font-weight: 700; color: var(--text-dark); }
    .med-brand { font-size: 0.9rem; color: var(--text-grey); margin-top: -2px; }
    .stock-badge {
        background: #EFF6FF; color: var(--primary-blue);
        padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;
    }

    /* Horizontal Details Tags */
    .med-details {
        display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
    }
    .detail-tag {
        font-size: 0.8rem; padding: 4px 10px; border-radius: 6px;
        background: #F9FAFB; border: 1px solid #E5E7EB; color: #4B5563;
        display: flex; align-items: center; gap: 5px;
    }
    .detail-tag i { color: var(--primary-blue); font-size: 0.7rem; }

    .med-footer { margin-top: auto; }
    .btn-request {
        width: 100%; background: var(--primary-blue); color: white;
        border: none; padding: 10px; border-radius: 8px; font-weight: 600;
        cursor: pointer; transition: background 0.2s;
    }
    .btn-request:hover { background: var(--dark-blue); }

    /* --- New Program Card Styles --- */
    .program-card {
        background: white; border-radius: 12px; border: 1px solid #E5E7EB;
        overflow: hidden; display: flex; flex-direction: column;
        transition: transform 0.2s;
    }
    .program-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    .program-date-box {
        background: var(--light-blue-bg); color: var(--primary-blue);
        padding: 15px; text-align: center; font-weight: 700;
        border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .program-body { padding: 20px; flex: 1; display: flex; flex-direction: column;}
    .program-title { font-size: 1.15rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
    .program-info { font-size: 0.9rem; color: #4B5563; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .program-desc { font-size: 0.85rem; color: #6B7280; margin-top: 10px; line-height: 1.5; }
    .badge-upcoming { background: #DBEAFE; color: #1E40AF; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }

    /* Table Styles */
    .table-wrapper { background: white; border-radius: 12px; overflow: hidden; border: 1px solid #E5E7EB; }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th {
        background: #F8FAFC; padding: 15px 20px; text-align: left;
        font-size: 0.85rem; text-transform: uppercase; color: #64748B; font-weight: 600;
        border-bottom: 1px solid #E2E8F0;
    }
    .custom-table td { padding: 15px 20px; border-bottom: 1px solid #E2E8F0; color: #334155; }
    .custom-table tr:last-child td { border-bottom: none; }
    
    /* Status Badges */
    .status-badge { padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    .status-pending { background: #FFF7ED; color: #C2410C; }
    .status-approved { background: #F0FDF4; color: #15803D; }
    .status-rejected { background: #FEF2F2; color: #B91C1C; }

    /* Modal */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-box { background: white; width: 90%; max-width: 500px; border-radius: 12px; padding: 25px; }

    /* Responsive */
    @media(max-width: 992px) { .medicine-grid { grid-template-columns: 1fr; } .stats-container { grid-template-columns: 1fr; } }
</style>

@php $view = request('view', 'available'); @endphp

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success" style="background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
</div>
@endif

{{-- 1. Header Section --}}
<div class="health-header">
    <div class="health-title">Health Services</div>
    <div class="health-subtitle">Browse medicines, check upcoming health programs, and request assistance online.</div>
</div>

{{-- 2. Stats Row --}}
<div class="stats-container">
    <div class="stat-bar pending">
        <div>
            <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-icon-circle bg-light-orange"><i class="fas fa-clock"></i></div>
    </div>
    <div class="stat-bar approved">
        <div>
            <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">Ready for Pickup</div>
        </div>
        <div class="stat-icon-circle bg-light-green"><i class="fas fa-check"></i></div>
    </div>
    <div class="stat-bar rejected">
        <div>
            <div class="stat-value">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">Rejected / Denied</div>
        </div>
        <div class="stat-icon-circle bg-light-red"><i class="fas fa-times"></i></div>
    </div>
</div>

{{-- 3. View Switcher --}}
<div class="view-switcher">
    <a href="{{ route('resident.health-services', ['view' => 'available']) }}" 
       class="switch-btn {{ $view === 'available' ? 'active' : '' }}">
       <i class="fas fa-pills"></i> Available Medicines
    </a>
    <a href="{{ route('resident.health-services', ['view' => 'programs']) }}" 
       class="switch-btn {{ $view === 'programs' ? 'active' : '' }}">
       <i class="fas fa-calendar-check"></i> Health Programs
    </a>
    <a href="{{ route('resident.health-services', ['view' => 'history']) }}" 
       class="switch-btn {{ $view === 'history' ? 'active' : '' }}">
       <i class="fas fa-history"></i> Request History
    </a>
</div>

{{-- 4. Content Area --}}
@if($view === 'available')
    
    <div class="medicine-grid">
        @forelse($medicines as $medicine)
        <div class="med-card">
            <div class="med-header">
                <div>
                    <div class="med-name">{{ $medicine->item_name }}</div>
                    <div class="med-brand">{{ $medicine->brand_name ?? 'Generic' }}</div>
                </div>
                <div class="stock-badge">{{ $medicine->quantity }} Units</div>
            </div>
            
            <div class="med-details">
                <div class="detail-tag"><i class="fas fa-prescription-bottle"></i> {{ $medicine->dosage }}</div>
                <div class="detail-tag"><i class="fas fa-tag"></i> {{ $medicine->category }}</div>
                <div class="detail-tag"><i class="fas fa-calendar-alt"></i> Exp: {{ \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') }}</div>
            </div>

            <div class="med-footer">
                <button type="button" class="btn-request" onclick="openModal({{ $medicine->id }}, '{{ $medicine->item_name }}', {{ $medicine->quantity }})">
                    Request This Item
                </button>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6B7280; background: white; border-radius: 12px; border: 1px dashed #E5E7EB;">
            <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <p>No medicines available in the inventory right now.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $medicines->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@elseif($view === 'programs')

    {{-- HEALTH PROGRAMS VIEW --}}
    <div class="medicine-grid">
        @forelse($programs as $program)
        <div class="program-card">
            <div class="program-date-box">
                <i class="fas fa-calendar-day"></i> {{ $program->schedule_date->format('M d, Y') }}
                <span style="font-weight: 400; font-size: 0.9rem;">| {{ $program->schedule_date->format('h:i A') }}</span>
            </div>
            <div class="program-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="program-title">{{ $program->title }}</div>
                    <span class="badge-upcoming">{{ $program->status }}</span>
                </div>
                
                <div class="program-info">
                    <i class="fas fa-map-marker-alt text-primary"></i> {{ $program->location }}
                </div>
                <div class="program-info">
                    <i class="fas fa-user-md text-primary"></i> Organized by: {{ $program->organizer ?? 'Barangay Health Center' }}
                </div>

                <div class="program-desc">
                    {{ Str::limit($program->description, 100) }}
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6B7280; background: white; border-radius: 12px; border: 1px dashed #E5E7EB;">
            <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 10px;"></i>
            <p>No upcoming health programs scheduled at this time.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $programs->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@elseif($view === 'history')

    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Date Requested</th>
                    <th>Medicine Details</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myRequestsPagination as $req)
                <tr>
                    <td>{{ $req->created_at->format('M d, Y') }}</td>
                    <td>
                        <span style="font-weight: 600; color: #1F2937;">{{ $req->medicine->item_name }}</span><br>
                        <span style="font-size: 0.85rem; color: #6B7280;">{{ $req->medicine->dosage }}</span>
                    </td>
                    <td style="font-weight: 600;">{{ $req->quantity_requested }}</td>
                    <td>
                        @if($req->status == 'Pending') <span class="status-badge status-pending">Pending</span>
                        @elseif($req->status == 'Approved') <span class="status-badge status-approved">Pickup Ready</span>
                        @else <span class="status-badge status-rejected">Rejected</span>
                        @endif
                    </td>
                    <td style="font-size: 0.9rem; color: #6B7280;">{{ $req->remarks ?? 'No remarks' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #6B7280;">
                        No request history found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $myRequestsPagination->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

@endif

{{-- Request Modal --}}
<div id="requestModal" class="modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 1.25rem;">Request Medicine</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        
        <form action="{{ route('resident.health.request.store') }}" method="POST">
            @csrf
            <input type="hidden" name="medicine_id" id="modalMedId">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Medicine</label>
                <input type="text" id="modalMedName" class="form-control" readonly style="background: #F3F4F6; border: 1px solid #E5E7EB; width: 100%; padding: 10px; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Quantity (Max: <span id="modalMax"></span>)</label>
                <input type="number" name="quantity_requested" id="modalQty" class="form-control" min="1" required style="border: 1px solid #E5E7EB; width: 100%; padding: 10px; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">Purpose / Notes</label>
                <textarea name="purpose" class="form-control" rows="3" style="border: 1px solid #E5E7EB; width: 100%; padding: 10px; border-radius: 8px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #E5E7EB; background: white; border-radius: 8px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 10px 20px; background: #2B5CE6; color: white; border: none; border-radius: 8px; cursor: pointer;">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id, name, max) {
        document.getElementById('modalMedId').value = id;
        document.getElementById('modalMedName').value = name;
        document.getElementById('modalQty').max = max;
        document.getElementById('modalMax').innerText = max;
        document.getElementById('requestModal').classList.add('show');
    }
    function closeModal() {
        document.getElementById('requestModal').classList.remove('show');
    }
    window.onclick = function(e) {
        if(e.target == document.getElementById('requestModal')) closeModal();
    }
</script>
@endsection