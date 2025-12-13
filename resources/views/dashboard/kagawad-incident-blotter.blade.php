@extends('layouts.dashboard-layout')

@section('title', 'Incident & Blotter Management')

@section('nav-items')
    {{-- COMPLETE KAGAWAD NAVIGATION --}}
    <li class="nav-item">
        <a href="{{ route('kagawad.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.residents') }}" class="nav-link">
            <i class="fas fa-users"></i>
            <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.projects') }}" class="nav-link">
            <i class="fas fa-tasks"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- ACTIVE PAGE --}}
        <a href="{{ route('kagawad.incidents') }}" class="nav-link active">
            <i class="fas fa-gavel"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.announcements.index') }}" class="nav-link">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- Main Layout & Header (Matched to Captain) --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 40px; border-radius: 16px; margin-bottom: 30px; position: relative;
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }

    /* --- Stats Grid --- */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: flex; justify-content: space-between; align-items: center;
    }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    .stat-badge.blue { color: #2B5CE6; }
    .stat-badge.orange { color: #FF8C42; }
    .stat-badge.green { color: #10B981; }

    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }

    /* Directory Header Style */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 25px 35px; border-radius: 16px 16px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .directory-title { display: flex; align-items: center; gap: 15px; font-size: 1.25rem; font-weight: 700; }
    
    .filters-section { display: flex; align-items: center; flex-wrap: wrap; gap: 15px; }
    .search-input, .filter-select {
        padding: 12px 20px; border: 1px solid #E5E7EB; border-radius: 10px;
        font-size: 1rem; background: white; min-width: 180px;
        transition: border-color 0.3s;
    }
    .search-input:focus, .filter-select:focus { outline: none; border-color: #2B5CE6; }

    /* Table Styles */
    .table-container {
        background: white; border-radius: 0 0 16px 16px;
        overflow-x: auto;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; min-width: 1000px; }
    .table thead { background: #F8FAFC; }
    
    .table th {
        padding: 20px 25px;
        font-weight: 700; color: #475569; font-size: 0.9rem; 
        text-transform: uppercase; border-bottom: 2px solid #E2E8F0; text-align: left;
        letter-spacing: 0.05em; white-space: nowrap;
    }
    
    .table td { 
        padding: 20px 25px; 
        vertical-align: middle; 
        border-bottom: 1px solid #F1F5F9; 
        color: #334155; 
        font-size: 0.95rem;
    }
    .table tbody tr:hover { background: #F8FAFC; transition: background 0.2s ease; }

    /* Badges */
    .status-badge { 
        padding: 8px 16px; 
        border-radius: 30px; 
        font-size: 0.75rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        display: inline-block;
        white-space: nowrap;
    }
    .status-Open { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
    .status-Scheduled { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; }
    .status-Resolved { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
    .status-Mediation { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
    .status-Investigation { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .status-Dismissed { background: #F9FAFB; color: #4B5563; border: 1px solid #E5E7EB; }

    /* Buttons */
    .btn-log-case {
        position: absolute; top: 40px; right: 40px;
        background: rgba(255,255,255,0.2); color: white;
        border: 1px solid rgba(255,255,255,0.4);
        padding: 12px 24px; border-radius: 10px; font-weight: 600;
        transition: all 0.3s; text-decoration: none; display: flex; align-items: center; gap: 10px;
        cursor: pointer;
    }
    .btn-log-case:hover { background: white; color: #2B5CE6; transform: translateY(-2px); }

    /* Log Box */
    .history-log-box {
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;
        height: 250px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.9rem; color: #334155;
        white-space: pre-wrap; line-height: 1.6;
    }

    .pagination-container { padding: 30px; background: white; border-radius: 0 0 16px 16px; border-top: 1px solid #F1F5F9; }
    
    /* Modals */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 12px; width: 90%; max-width: 800px; position: relative; display: flex; flex-direction: column; max-height: 90vh; }
    .modal-header { padding: 20px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; border-radius: 12px 12px 0 0; }
    .modal-title { font-size: 1.25rem; font-weight: 700; color: #1F2937; margin: 0; }
    .modal-body { padding: 20px; overflow-y: auto; }
    .modal-footer { padding: 20px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; gap: 10px; background: #F8FAFC; border-radius: 0 0 12px 12px; }
    
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.95rem; }
    .btn-primary { background: #2B5CE6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-secondary { background: #E5E7EB; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; }

    @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>

@if(session('success'))
<div class="alert alert-success d-flex align-items-center mb-3" style="background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; border-radius: 12px; padding: 20px;">
    <i class="fas fa-check-circle mr-3 fa-lg"></i>
    <span style="font-weight: 500;">{{ session('success') }}</span>
</div>
@endif

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Incident & Blotter Management</div>
    <div class="profiling-subtitle">Assist in mediation, schedule hearings, and track case resolutions.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg Security</span>
    </div>
    
    <button class="btn-log-case" onclick="openModal('addIncidentModal')">
        <i class="fas fa-plus-circle fa-lg"></i> 
        <span>Log New Case</span>
    </button>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['for_mediation'] }}</h3>
            <p>For Mediation</p>
            <div class="stat-badge orange"><i class="fas fa-handshake"></i><span>Active Cases</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-handshake"></i></div>
    </div>
    
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['scheduled'] }}</h3>
            <p>Hearings</p>
            <div class="stat-badge blue"><i class="fas fa-calendar-alt"></i><span>Scheduled</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-calendar-check"></i></div>
    </div>
    
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['resolved_this_month'] }}</h3>
            <p>Resolved</p>
            <div class="stat-badge green"><i class="fas fa-check-double"></i><span>This Month</span></div>
        </div>
        <div class="stat-box-icon icon-green-bg"><i class="fas fa-check"></i></div>
    </div>
</div>

{{-- Directory Header --}}
<div class="directory-header">
    <div class="directory-title">
        <i class="fas fa-book-open fa-lg"></i>
        <span>Case Directory</span>
    </div>
    <form action="{{ route('kagawad.incidents') }}" method="GET" class="filters-section">
        <input type="text" name="search" class="search-input" placeholder="🔍 Search Case #..." value="{{ $search }}">
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="All">All Statuses</option>
            <option value="Open" {{ $status == 'Open' ? 'selected' : '' }}>Open</option>
            <option value="For Mediation" {{ $status == 'For Mediation' ? 'selected' : '' }}>For Mediation</option>
            <option value="Under Investigation" {{ $status == 'Under Investigation' ? 'selected' : '' }}>Under Investigation</option>
            <option value="Scheduled for Hearing" {{ $status == 'Scheduled for Hearing' ? 'selected' : '' }}>Hearing Set</option>
            <option value="Resolved" {{ $status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="Dismissed" {{ $status == 'Dismissed' ? 'selected' : '' }}>Dismissed</option>
        </select>
        @if($search || $status && $status != 'All')
            <a href="{{ route('kagawad.incidents') }}" class="btn btn-light text-primary fw-bold" style="padding: 12px 20px; border-radius: 10px; text-decoration: none;">Reset</a>
        @endif
    </form>
</div>

{{-- Table Container --}}
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Case Number</th>
                <th>Date Filed</th>
                <th>Type</th>
                <th>Complainant</th>
                <th>Respondent</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $inc)
            <tr>
                <td>
                    <span class="fw-bold text-dark">{{ $inc->case_number }}</span>
                </td>
                <td>{{ $inc->date_reported->format('M d, Y') }}</td>
                <td><span class="text-muted fw-bold">{{ $inc->incident_type }}</span></td>
                <td><span class="fw-bold text-primary">{{ $inc->complainant }}</span></td>
                <td>{{ $inc->respondent ?? 'N/A' }}</td>
                <td>
                    @php
                        $statusClass = match($inc->status) {
                            'Open' => 'status-Open',
                            'Scheduled for Hearing' => 'status-Scheduled',
                            'Resolved' => 'status-Resolved',
                            'Dismissed' => 'status-Dismissed',
                            'For Mediation' => 'status-Mediation',
                            'Under Investigation' => 'status-Investigation',
                            default => 'status-Mediation'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $inc->status }}</span>
                </td>
                <td class="text-end">
                    <button class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem;" onclick="openManageModal({{ json_encode($inc) }})">
                        <i class="fas fa-tasks"></i> Manage
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><div class="no-results-found text-center py-5 text-muted">No records found.</div></td></tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination-container">
        {{ $incidents->appends(['search' => $search, 'status' => $status])->links('pagination::bootstrap-4') }}
    </div>
</div>

{{-- MODALS --}}

{{-- 1. Manage Case Modal --}}
<div id="manageModal" class="modal">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h5 class="modal-title" id="manageModalTitle">Manage Case</h5>
            <span onclick="closeModal('manageModal')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
        </div>
        <div class="modal-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="border-right: 1px solid #E5E7EB; padding-right: 20px;">
                    <label class="small fw-bold text-secondary text-uppercase mb-2" style="display:block;">Case Narrative</label>
                    <div class="bg-light p-3 rounded mb-4 border" style="background:#F9FAFB; padding:15px; border-radius:8px; border:1px solid #E5E7EB; font-size: 0.9rem;" id="manageNarrative"></div>
                    
                    <label class="small fw-bold text-secondary text-uppercase mb-2" style="display:block;">History Log</label>
                    <div class="history-log-box" id="manageHistory"></div>
                </div>
                
                <div>
                    <h6 class="fw-bold text-primary mb-3">Update / Mediation</h6>
                    <form id="manageForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="small fw-bold">Action Type</label>
                            <select name="action" class="form-control" id="actionSelect" onchange="toggleActionFields()">
                                <option value="add_log">Add Investigation Note (Log Only)</option>
                                <option value="update_status">Update Status (Mediation/Resolve)</option>
                                <option value="schedule_hearing">Schedule Hearing</option>
                            </select>
                        </div>

                        <div class="form-group" id="statusGroup" style="display:none;">
                            <label class="small fw-bold">New Status</label>
                            <select name="new_status" class="form-control">
                                <option value="Under Investigation">Under Investigation</option>
                                <option value="For Mediation">For Mediation</option>
                                <option value="Resolved">Resolved (Amicable Settlement)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="small fw-bold" id="remarksLabel">Investigation Note / Findings</label>
                            <textarea name="remarks" class="form-control" rows="5" required placeholder="Enter details..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary w-100 py-3">Save Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Add Incident Modal --}}
<div id="addIncidentModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <form action="{{ route('kagawad.incidents.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Record Walk-in Incident</h5>
                <span onclick="closeModal('addIncidentModal')" style="cursor: pointer; font-size: 1.5rem;">&times;</span>
            </div>
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Complainant</label>
                        <input type="text" name="complainant" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Respondent</label>
                        <input type="text" name="respondent" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Incident Type</label>
                    <select name="incident_type" class="form-control">
                        <option>Noise Complaint</option>
                        <option>Theft</option>
                        <option>Property Dispute</option>
                        <option>Physical Injury</option>
                        <option>Others</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Date & Time</label>
                        <input type="datetime-local" name="date_reported" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Narrative</label>
                    <textarea name="narrative" class="form-control" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('addIncidentModal')">Close</button>
                <button type="submit" class="btn-primary">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openManageModal(incident) {
        document.getElementById('manageModalTitle').innerText = 'Manage Case: ' + incident.case_number;
        document.getElementById('manageNarrative').innerText = incident.narrative;
        document.getElementById('manageHistory').innerText = incident.actions_taken;
        document.getElementById('manageForm').action = "/kagawad/incidents/" + incident.id + "/update"; // Adjust route as needed
        
        openModal('manageModal');
    }

    function toggleActionFields() {
        var action = document.getElementById('actionSelect').value;
        var statusGroup = document.getElementById('statusGroup');
        var remarksLabel = document.getElementById('remarksLabel');

        if (action === 'update_status') {
            statusGroup.style.display = 'block';
            remarksLabel.innerText = 'Reason for Status Change';
        } else if (action === 'schedule_hearing') {
            statusGroup.style.display = 'none';
            remarksLabel.innerText = 'Hearing Date, Time, and Venue';
        } else {
            statusGroup.style.display = 'none';
            remarksLabel.innerText = 'Investigation Note / Findings';
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection