@extends('layouts.dashboard-layout')

@section('title', 'My Incident Reports')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link">
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
        <a href="{{ route('resident.health-services') }}" class="nav-link">
            <i class="fas fa-heartbeat"></i>
            <span>Health Services</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('resident.incidents.index') }}" class="nav-link active">
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

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success d-flex align-items-center mb-3" style="background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; border-radius: 12px; padding: 20px;">
    <i class="fas fa-check-circle mr-3 fa-lg"></i>
    <span style="font-weight: 500;">{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger d-flex align-items-center mb-3" style="background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 12px; padding: 20px;">
    <i class="fas fa-exclamation-circle mr-3 fa-lg"></i>
    <span style="font-weight: 500;">{{ session('error') }}</span>
</div>
@endif

<style>
    /* --- HEADER STYLES (Matched to Captain) --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }

    /* --- STATS BOXES (Matched) --- */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
        transition: transform 0.2s;
    }
    .stat-box:hover { transform: translateY(-3px); }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #1F2937; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    
    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }

    /* Colors */
    .icon-blue-bg { background: #2B5CE6; } .stat-badge.blue { color: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; } .stat-badge.orange { color: #FF8C42; }
    .icon-green-bg { background: #10B981; } .stat-badge.green { color: #10B981; }

    /* --- DIRECTORY HEADER & FILTER --- */
    .directory-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 25px 35px; border-radius: 16px 16px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .directory-title { display: flex; align-items: center; gap: 15px; font-size: 1.25rem; font-weight: 700; }
    
    .filters-section { display: flex; align-items: center; gap: 15px; }
    .search-input {
        padding: 12px 20px; border: 1px solid #E5E7EB; border-radius: 10px;
        font-size: 1rem; background: white; min-width: 250px;
        transition: border-color 0.3s;
    }
    .search-input:focus { outline: none; border-color: #2B5CE6; }

    /* --- TABLE STYLES --- */
    .table-container {
        background: white; border-radius: 0 0 16px 16px;
        overflow-x: auto;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table { width: 100%; margin: 0; border-collapse: separate; border-spacing: 0; }
    .table thead { background: #F8FAFC; }
    
    .table th {
        padding: 20px 25px;
        font-weight: 700; color: #475569; font-size: 0.9rem; 
        text-transform: uppercase; border-bottom: 2px solid #E2E8F0;
        letter-spacing: 0.05em;
    }
    
    .table td { 
        padding: 20px 25px; vertical-align: middle; 
        border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 0.95rem;
    }
    .table tbody tr:hover { background: #F8FAFC; transition: background 0.2s ease; }

    /* --- BADGES --- */
    .status-badge { 
        padding: 8px 16px; border-radius: 30px; font-size: 0.75rem; 
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        display: inline-block; white-space: nowrap;
    }
    .status-Open { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
    .status-Resolved { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
    .status-Mediation { background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; }
    .status-Investigation { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .status-Dismissed { background: #F9FAFB; color: #4B5563; border: 1px solid #E5E7EB; }

    /* --- BUTTONS --- */
    .btn-log-case {
        position: absolute; top: 40px; right: 40px;
        background: rgba(255,255,255,0.2); color: white;
        border: 1px solid rgba(255,255,255,0.4);
        padding: 12px 24px; border-radius: 10px; font-weight: 600;
        transition: all 0.3s; text-decoration: none; display: flex; align-items: center; gap: 10px;
    }
    .btn-log-case:hover { background: white; color: #2B5CE6; transform: translateY(-2px); }

    .btn-view {
        width: 38px; height: 38px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE;
        transition: all 0.2s;
    }
    .btn-view:hover { background: #2563EB; color: white; }

    /* --- LOG BOX --- */
    .history-log-box {
        background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 15px;
        height: 200px; overflow-y: auto; font-family: 'Courier New', monospace; 
        font-size: 0.85rem; color: #334155; white-space: pre-wrap;
    }
</style>

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">My Incident Reports</div>
    <div class="profiling-subtitle">File complaints and track the status of your reported cases.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Resident Portal</span>
    </div>
    
    {{-- BS4 Modal Trigger --}}
    <button class="btn-log-case" data-toggle="modal" data-target="#fileIncidentModal">
        <i class="fas fa-plus-circle fa-lg"></i> 
        <span>File New Report</span>
    </button>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['my_total_reports'] }}</h3>
            <p>Total Reports</p>
            <div class="stat-badge blue"><i class="fas fa-file-alt"></i><span>Filed</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-folder"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['open_cases'] }}</h3>
            <p>Active Cases</p>
            <div class="stat-badge orange"><i class="fas fa-clock"></i><span>In Progress</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-hourglass-half"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['resolved_cases'] }}</h3>
            <p>Resolved</p>
            <div class="stat-badge green"><i class="fas fa-check-circle"></i><span>Closed</span></div>
        </div>
        <div class="stat-box-icon icon-green-bg"><i class="fas fa-check"></i></div>
    </div>
</div>

{{-- Directory Header --}}
<div class="directory-header">
    <div class="directory-title">
        <i class="fas fa-list-alt fa-lg"></i>
        <span>Report History</span>
    </div>
    <form action="{{ route('resident.incidents.index') }}" method="GET" class="filters-section">
        <input type="text" name="search" class="search-input" placeholder="🔍 Search by Case # or Type..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-light text-primary fw-bold" style="padding: 12px 20px; border-radius: 10px;">Search</button>
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
                <th>Narrative</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($myIncidents as $incident)
            <tr>
                <td>
                    <span class="fw-bold text-dark">{{ $incident->case_number }}</span>
                </td>
                <td>{{ $incident->date_reported->format('M d, Y') }}</td>
                <td><span class="text-muted fw-bold">{{ $incident->incident_type }}</span></td>
                <td>{{ Str::limit($incident->narrative, 40) }}</td>
                <td>
                    @php
                        $statusClass = match($incident->status) {
                            'Open' => 'status-Open',
                            'Scheduled for Hearing' => 'status-Mediation',
                            'Resolved' => 'status-Resolved',
                            'Dismissed' => 'status-Dismissed',
                            'For Mediation' => 'status-Mediation',
                            'Under Investigation' => 'status-Investigation',
                            default => 'status-Mediation'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $incident->status }}</span>
                </td>
                <td class="text-end">
                    {{-- BS4 Modal Trigger --}}
                    <button class="btn-view" data-toggle="modal" data-target="#viewModal{{ $incident->id }}" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>

            {{-- VIEW / MANAGE MODAL per Incident --}}
            <div class="modal fade" id="viewModal{{ $incident->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light border-bottom-0 py-3">
                            <h5 class="modal-title fw-bold pl-2">Case Details: {{ $incident->case_number }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row">
                                {{-- LEFT: Information --}}
                                <div class="col-md-6 border-right">
                                    <h6 class="fw-bold text-primary mb-3 text-uppercase small">Case Information</h6>
                                    
                                    <div class="mb-3">
                                        <label class="small text-muted d-block mb-0">Respondent</label>
                                        <span class="fw-bold">{{ $incident->respondent ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-muted d-block mb-0">Location</label>
                                        <span class="fw-bold">{{ $incident->location }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-muted d-block mb-0">Narrative</label>
                                        <div class="bg-light p-3 rounded border text-muted small">{{ $incident->narrative }}</div>
                                    </div>

                                    <h6 class="fw-bold text-primary mt-4 mb-2 text-uppercase small">Status History</h6>
                                    <div class="history-log-box">{{ $incident->actions_taken ?? 'No updates available.' }}</div>
                                </div>

                                {{-- RIGHT: Actions (Only if Open) --}}
                                <div class="col-md-6 pl-md-4">
                                    @if($incident->status === 'Open')
                                        <div class="alert alert-info small mb-4">
                                            <i class="fas fa-info-circle mr-1"></i> This case is currently <strong>Open</strong>. You may edit the details or withdraw it if it is no longer relevant.
                                        </div>

                                        <form action="{{ route('resident.incidents.update', $incident->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <h6 class="fw-bold text-secondary mb-3 small text-uppercase">Edit Details</h6>
                                            
                                            <div class="form-group mb-2">
                                                <label class="small fw-bold">Incident Type</label>
                                                <select name="incident_type" class="form-control form-control-sm">
                                                    <option selected>{{ $incident->incident_type }}</option>
                                                    <option>Noise Complaint</option><option>Theft</option><option>Property Dispute</option><option>Vandalism</option><option>Others</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-2">
                                                <label class="small fw-bold">Update Narrative</label>
                                                <textarea name="narrative" class="form-control form-control-sm" rows="3">{{ $incident->narrative }}</textarea>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="small fw-bold">Date & Time</label>
                                                <input type="datetime-local" name="date_reported" value="{{ $incident->date_reported->format('Y-m-d\TH:i') }}" class="form-control form-control-sm">
                                            </div>

                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold mb-3">Save Changes</button>
                                        </form>

                                        <hr>

                                        <form action="{{ route('resident.incidents.cancel', $incident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this report?');">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="fas fa-times-circle"></i> Withdraw / Cancel Report
                                            </button>
                                        </form>
                                    @else
                                        {{-- LOCKED VIEW --}}
                                        <div class="text-center py-5 mt-4">
                                            <div class="mb-3 text-secondary opacity-50">
                                                <i class="fas fa-lock fa-4x"></i>
                                            </div>
                                            <h6 class="fw-bold text-secondary">Case Locked</h6>
                                            <p class="small text-muted px-4">
                                                This case is currently marked as <span class="badge badge-secondary">{{ $incident->status }}</span>. 
                                                <br>Editing or cancelling is disabled at this stage. Please contact the barangay hall if you have urgent concerns.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="6">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                        <p>No incident reports found.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="p-3">
        {{ $myIncidents->links() }}
    </div>
</div>

{{-- FILE NEW INCIDENT MODAL --}}
<div class="modal fade" id="fileIncidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('resident.incidents.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">File Incident Report</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small fw-bold text-muted">Incident Type <span class="text-danger">*</span></label>
                        <select name="incident_type" class="form-control" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="Noise Complaint">Noise Complaint</option>
                            <option value="Theft">Theft</option>
                            <option value="Property Dispute">Property Dispute</option>
                            <option value="Vandalism">Vandalism</option>
                            <option value="Physical Injury">Physical Injury</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="small fw-bold text-muted">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_reported" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="small fw-bold text-muted">Respondent (Optional)</label>
                            <input type="text" name="respondent" class="form-control" placeholder="Name involved">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small fw-bold text-muted">Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control" placeholder="Specific location..." required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small fw-bold text-muted">Narrative <span class="text-danger">*</span></label>
                        <textarea name="narrative" class="form-control" rows="4" placeholder="Describe the incident in detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pr-4">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->any())
            $('#fileIncidentModal').modal('show');
        @endif
    });
</script>
@endsection