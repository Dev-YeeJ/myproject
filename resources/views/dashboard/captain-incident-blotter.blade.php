@extends('layouts.dashboard-layout')

@section('title', 'Incident & Blotter Management')

@section('nav-items')
    {{-- Navigation items remain the same --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link">
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
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.financial') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.incident.index') }}" class="nav-link active">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.project.monitoring') }}" class="nav-link">
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.announcements.index') }}" class="nav-link">
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

    /* --- DIRECTORY HEADER & FILTER --- */
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

    /* --- TABLE STYLES (EXPANDED COLUMNS) --- */
    .table-container {
        background: white; border-radius: 0 0 16px 16px;
        overflow-x: auto; /* Allow horizontal scroll if needed */
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

    /* --- BADGES --- */
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
    .status-Dismissed { background: #F9FAFB; color: #4B5563; border: 1px solid #E5E7EB; }

    .priority-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .p-high { background-color: #EF4444; } .p-med { background-color: #F59E0B; } .p-low { background-color: #10B981; }

    /* --- ACTION ICONS --- */
    .action-icons { display: flex; gap: 10px; justify-content: flex-end; }
    .action-icon {
        width: 38px; height: 38px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; border: 1px solid transparent; background: transparent;
        font-size: 1rem;
    }
    .action-icon.process { color: #2563EB; background: #EFF6FF; border-color: #DBEAFE; } 
    .action-icon.process:hover { background: #2563EB; color: white; transform: translateY(-2px); }
    
    .action-icon.edit { color: #059669; background: #ECFDF5; border-color: #D1FAE5; } 
    .action-icon.edit:hover { background: #059669; color: white; transform: translateY(-2px); }
    
    .action-icon.delete { color: #DC2626; background: #FEF2F2; border-color: #FEE2E2; } 
    .action-icon.delete:hover { background: #DC2626; color: white; transform: translateY(-2px); }

    /* --- BUTTONS --- */
    .btn-log-case {
        position: absolute; top: 40px; right: 40px;
        background: rgba(255,255,255,0.2); color: white;
        border: 1px solid rgba(255,255,255,0.4);
        padding: 12px 24px; border-radius: 10px; font-weight: 600;
        transition: all 0.3s; text-decoration: none; display: flex; align-items: center; gap: 10px;
    }
    .btn-log-case:hover { background: white; color: #2B5CE6; transform: translateY(-2px); }

    /* --- LOG BOX --- */
    .history-log-box {
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;
        height: 250px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.9rem; color: #334155;
        white-space: pre-wrap; line-height: 1.6;
    }

    .pagination-container { padding: 30px; background: white; border-radius: 0 0 16px 16px; border-top: 1px solid #F1F5F9; }
</style>

@if(session('success'))
<div class="alert alert-success d-flex align-items-center mb-3" style="background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; border-radius: 12px; padding: 20px;">
    <i class="fas fa-check-circle me-3 fa-lg"></i>
    <span style="font-weight: 500;">{{ session('success') }}</span>
</div>
@endif

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Incident & Blotter Management</div>
    <div class="profiling-subtitle">Track cases, schedule hearings, and manage peace and order resolutions.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg Security</span>
    </div>
    <button class="btn-log-case" data-bs-toggle="modal" data-bs-target="#logIncidentModal">
        <i class="fas fa-plus-circle fa-lg"></i> 
        <span>Log New Case</span>
    </button>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_cases'] }}</h3>
            <p>Total Cases</p>
            <div class="stat-badge blue"><i class="fas fa-folder-open"></i><span>All Records</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-folder"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['hearings_set'] }}</h3>
            <p>Hearings</p>
            <div class="stat-badge orange"><i class="fas fa-gavel"></i><span>Scheduled</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-calendar-alt"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['high_priority'] }}</h3>
            <p>High Priority</p>
            <div class="stat-badge red"><i class="fas fa-exclamation-circle"></i><span>Needs Action</span></div>
        </div>
        <div class="stat-box-icon icon-red-bg"><i class="fas fa-fire"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['resolved_cases'] }}</h3>
            <p>Resolved</p>
            <div class="stat-badge green"><i class="fas fa-check-double"></i><span>Closed</span></div>
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
    <form action="{{ route('captain.incident.index') }}" method="GET" class="filters-section">
        <input type="text" name="search" class="search-input" placeholder="🔍 Search Case #..." value="{{ $search }}">
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="All">All Statuses</option>
            <option value="Open" {{ $statusFilter == 'Open' ? 'selected' : '' }}>Open</option>
            <option value="Scheduled for Hearing" {{ $statusFilter == 'Scheduled for Hearing' ? 'selected' : '' }}>Hearing Set</option>
            <option value="Under Investigation" {{ $statusFilter == 'Under Investigation' ? 'selected' : '' }}>Investigation</option>
            <option value="Resolved" {{ $statusFilter == 'Resolved' ? 'selected' : '' }}>Resolved</option>
        </select>
        @if($search || $statusFilter && $statusFilter != 'All')
            <a href="{{ route('captain.incident.index') }}" class="btn btn-light text-primary fw-bold" style="padding: 12px 20px; border-radius: 10px;">Reset</a>
        @endif
    </form>
</div>

{{-- Table Container --}}
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                {{-- Separated Columns --}}
                <th>Case Number</th>
                <th>Date Filed</th>
                <th>Type</th>
                <th>Complainant</th>
                <th>Respondent</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        @if($record->priority == 'High') <span class="priority-dot p-high" title="High Priority"></span>
                        @elseif($record->priority == 'Medium') <span class="priority-dot p-med" title="Medium Priority"></span>
                        @else <span class="priority-dot p-low" title="Low Priority"></span> @endif
                        <span class="fw-bold text-dark">{{ $record->case_number }}</span>
                    </div>
                </td>
                <td>{{ $record->date_reported->format('M d, Y') }}</td>
                <td><span class="text-muted fw-bold">{{ $record->incident_type }}</span></td>
                <td class="fw-bold text-primary">{{ $record->complainant }}</td>
                <td>{{ $record->respondent ?? 'N/A' }}</td>
                <td>
                    @php
                        $statusClass = match($record->status) {
                            'Open' => 'status-Open',
                            'Scheduled for Hearing' => 'status-Scheduled',
                            'Resolved' => 'status-Resolved',
                            'Dismissed' => 'status-Dismissed',
                            default => 'status-Mediation'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $record->status }}</span>
                </td>
                <td>
                    <div class="action-icons">
                        <button class="action-icon process" data-bs-toggle="modal" data-bs-target="#processModal{{ $record->id }}" title="Process">
                            <i class="fas fa-tasks"></i>
                        </button>
                        <button class="action-icon edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $record->id }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-icon delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $record->id }}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>

            {{-- PROCESS MODAL --}}
            <div class="modal fade" id="processModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light border-bottom-0 py-3">
                            <h5 class="modal-title fw-bold ps-2">Process Case: {{ $record->case_number }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <label class="small fw-bold text-secondary text-uppercase mb-2">Narrative</label>
                                    <div class="bg-light p-3 rounded mb-4 border" style="font-size: 0.9rem;">{{ $record->narrative }}</div>
                                    <label class="small fw-bold text-secondary text-uppercase mb-2">History Log</label>
                                    <div class="history-log-box">{{ $record->actions_taken }}</div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <form action="{{ route('captain.incident.process', $record->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="mb-3">
                                            <label class="small fw-bold text-muted mb-1">Action</label>
                                            <select name="action_type" class="form-select" id="actSel{{ $record->id }}" onchange="toggleP{{ $record->id }}()">
                                                <option value="status_update">Update Status</option>
                                                <option value="schedule_hearing">Schedule Hearing</option>
                                                <option value="resolve_case">Mark Resolved</option>
                                            </select>
                                        </div>
                                        <div class="mb-3" id="statGrp{{ $record->id }}">
                                            <label class="small fw-bold text-muted mb-1">New Status</label>
                                            <select name="new_status" class="form-select">
                                                <option value="Under Investigation">Under Investigation</option>
                                                <option value="For Mediation">For Mediation</option>
                                                <option value="Dismissed">Dismissed</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small fw-bold text-muted mb-1" id="remLbl{{ $record->id }}">Remarks</label>
                                            <textarea name="remarks" class="form-control" rows="5" required placeholder="Enter details..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3">Save Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function toggleP{{ $record->id }}() {
                    var sel = document.getElementById('actSel{{ $record->id }}');
                    var grp = document.getElementById('statGrp{{ $record->id }}');
                    var lbl = document.getElementById('remLbl{{ $record->id }}');
                    if(sel.value === 'schedule_hearing') { grp.style.display = 'none'; lbl.innerHTML = 'Hearing Date & Time'; }
                    else if(sel.value === 'resolve_case') { grp.style.display = 'none'; lbl.innerHTML = 'Resolution Notes'; }
                    else { grp.style.display = 'block'; lbl.innerHTML = 'Remarks'; }
                }
            </script>

            {{-- EDIT MODAL --}}
            <div class="modal fade" id="editModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <form action="{{ route('captain.incident.update_details', $record->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header bg-light border-bottom-0 py-3">
                                <h5 class="modal-title fw-bold ps-2">Edit Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-muted">Complainant</label>
                                        <input type="text" name="complainant" class="form-control" value="{{ $record->complainant }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-muted">Respondent</label>
                                        <input type="text" name="respondent" class="form-control" value="{{ $record->respondent }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-muted">Type</label>
                                        <select name="incident_type" class="form-select">
                                            <option selected>{{ $record->incident_type }}</option>
                                            <option>Noise Complaint</option><option>Theft</option><option>Property Dispute</option><option>Physical Injury</option><option>Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-muted">Priority</label>
                                        <select name="priority" class="form-select">
                                            <option selected>{{ $record->priority }}</option>
                                            <option>Low</option><option>Medium</option><option>High</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold text-muted">Date</label>
                                        <input type="datetime-local" name="date_reported" class="form-control" value="{{ $record->date_reported->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold text-muted">Location</label>
                                        <input type="text" name="location" class="form-control" value="{{ $record->location }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="small fw-bold text-muted">Narrative</label>
                                        <textarea name="narrative" class="form-control" rows="3">{{ $record->narrative }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                                <button type="submit" class="btn btn-success text-white fw-bold px-4">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- DELETE MODAL --}}
            <div class="modal fade" id="deleteModal{{ $record->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content text-center p-4 border-0 shadow-lg">
                        <div class="mb-3 text-danger"><i class="fas fa-exclamation-circle fa-3x"></i></div>
                        <h5 class="fw-bold mb-2">Delete Record?</h5>
                        <p class="text-muted small mb-4">Are you sure? This cannot be undone.</p>
                        <form action="{{ route('captain.incident.destroy', $record->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 mb-2 py-2 fw-bold">Yes, Delete it</button>
                            <button type="button" class="btn btn-light w-100 py-2" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

            @empty
            <tr><td colspan="7"><div class="no-results-found text-center py-5 text-muted">No records found.</div></td></tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="pagination-container">
        {{ $records->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Log Incident Modal --}}
<div class="modal fade" id="logIncidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('captain.incident.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">Log New Incident</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Complainant Name</label>
                            <input type="text" name="complainant" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Respondent Name</label>
                            <input type="text" name="respondent" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Incident Type</label>
                            <select name="incident_type" class="form-select">
                                <option>Noise Complaint</option><option>Theft</option><option>Property Dispute</option><option>Physical Injury</option><option>Others</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted">Priority</label>
                            <select name="priority" class="form-select">
                                <option>Low</option><option>Medium</option><option>High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Date & Time</label>
                            <input type="datetime-local" name="date_reported" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Narrative</label>
                            <textarea name="narrative" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Log Case</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection