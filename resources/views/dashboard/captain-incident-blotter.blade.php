@extends('layouts.dashboard-layout')

@section('title', 'Incident & Blotter Management')

@section('content')
<style>
    /* Status Badges */
    .status-badge { padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-Open { background-color: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
    .status-Scheduled { background-color: #E0E7FF; color: #4338CA; border: 1px solid #A5B4FC; }
    .status-Resolved { background-color: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
    .status-Mediation { background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .status-Dismissed { background-color: #F3F4F6; color: #374151; border: 1px solid #D1D5DB; }
    
    .history-log-box {
        background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 15px;
        height: 200px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.8rem; color: #374151;
        white-space: pre-wrap; 
    }

    .modal-header-custom { background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .priority-dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .p-high { background-color: #EF4444; } .p-med { background-color: #F59E0B; } .p-low { background-color: #10B981; }
</style>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Blotter Management</h2>
        <p class="text-muted mb-0">Track cases, schedule hearings, and manage resolutions.</p>
    </div>
    <button class="btn btn-danger shadow-sm" data-toggle="modal" data-target="#logIncidentModal">
        <i class="fas fa-plus-circle me-1"></i> Log New Case
    </button>
</div>

{{-- KPI Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary">
            <small class="text-muted text-uppercase fw-bold">Total Cases</small>
            <h2 class="mb-0 fw-bold">{{ $stats['total_cases'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-4 border-warning">
            <small class="text-muted text-uppercase fw-bold">Hearings Set</small>
            <h2 class="mb-0 fw-bold">{{ $stats['hearings_set'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-4 border-danger">
            <small class="text-muted text-uppercase fw-bold">High Priority</small>
            <h2 class="mb-0 fw-bold">{{ $stats['high_priority'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 border-start border-4 border-success">
            <small class="text-muted text-uppercase fw-bold">Resolved</small>
            <h2 class="mb-0 fw-bold">{{ $stats['resolved_cases'] }}</h2>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('captain.incident.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search Case #, Complainant..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="All" {{ $statusFilter == 'All' ? 'selected' : '' }}>All Statuses</option>
                    <option value="Open" {{ $statusFilter == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="Scheduled for Hearing" {{ $statusFilter == 'Scheduled for Hearing' ? 'selected' : '' }}>Hearing Scheduled</option>
                    <option value="Resolved" {{ $statusFilter == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filter</button></div>
            <div class="col-md-2"><a href="{{ route('captain.incident.index') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4">Case Details</th>
                        <th>Parties</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td class="ps-4">
                            @if($record->priority == 'High') <span class="priority-dot p-high"></span>
                            @elseif($record->priority == 'Medium') <span class="priority-dot p-med"></span>
                            @else <span class="priority-dot p-low"></span> @endif
                            
                            <span class="fw-bold text-dark">{{ $record->case_number }}</span>
                            <div class="small text-muted">{{ $record->incident_type }} &bull; {{ $record->date_reported->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="small">
                                <strong>Comp:</strong> {{ $record->complainant }}<br>
                                <strong>Resp:</strong> {{ $record->respondent ?? 'N/A' }}
                            </div>
                        </td>
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
                        <td style="max-width: 200px;">
                            <small class="text-muted fst-italic">
                                {{ Str::limit(Str::afterLast($record->actions_taken, ']'), 30, '...') }}
                            </small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                {{-- PROCESS Button (Workflow) --}}
                                <button class="btn btn-sm btn-outline-primary fw-bold" data-toggle="modal" data-target="#processModal{{ $record->id }}">
                                    <i class="fas fa-tasks"></i> Process
                                </button>
                                
                                {{-- EDIT Button (Data) --}}
                                <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editModal{{ $record->id }}" title="Edit Details">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- DELETE Button --}}
                                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteModal{{ $record->id }}" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- ================= PROCESS MODAL (Status/Hearings) ================= --}}
                    <div class="modal fade" id="processModal{{ $record->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header modal-header-custom">
                                    <h5 class="modal-title fw-bold">Process Case: {{ $record->case_number }}</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-7 border-right">
                                            <div class="mb-3">
                                                <label class="small fw-bold text-secondary">NARRATIVE</label>
                                                <div class="bg-light p-2 rounded small border">{{ $record->narrative }}</div>
                                            </div>
                                            <div>
                                                <label class="small fw-bold text-secondary">CASE HISTORY</label>
                                                <div class="history-log-box">{{ $record->actions_taken }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <h6 class="fw-bold mb-3">Update Workflow</h6>
                                            <form action="{{ route('captain.incident.process', $record->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="form-group mb-3">
                                                    <label class="small fw-bold text-muted">Action</label>
                                                    <select name="action_type" class="form-control" id="actSel{{ $record->id }}" onchange="toggleP{{ $record->id }}()">
                                                        <option value="status_update">General Status Update</option>
                                                        <option value="schedule_hearing">Schedule Hearing</option>
                                                        <option value="resolve_case">Mark as Resolved</option>
                                                    </select>
                                                </div>

                                                <div class="form-group mb-3" id="statGrp{{ $record->id }}">
                                                    <label class="small fw-bold text-muted">New Status</label>
                                                    <select name="new_status" class="form-control">
                                                        <option value="Under Investigation">Under Investigation</option>
                                                        <option value="For Mediation">For Mediation</option>
                                                        <option value="Dismissed">Dismissed</option>
                                                    </select>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="small fw-bold text-muted" id="remLbl{{ $record->id }}">Remarks</label>
                                                    <textarea name="remarks" class="form-control" rows="4" required placeholder="Enter details..."></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-primary w-100">Save Update</button>
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
                            if(sel.value === 'schedule_hearing') {
                                grp.style.display = 'none'; lbl.innerHTML = 'Hearing Date & Time';
                            } else if(sel.value === 'resolve_case') {
                                grp.style.display = 'none'; lbl.innerHTML = 'Resolution Notes';
                            } else {
                                grp.style.display = 'block'; lbl.innerHTML = 'Remarks';
                            }
                        }
                    </script>

                    {{-- ================= EDIT DETAILS MODAL ================= --}}
                    <div class="modal fade" id="editModal{{ $record->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('captain.incident.update_details', $record->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">Edit Details</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group mb-2">
                                            <label class="small fw-bold">Complainant</label>
                                            <input type="text" name="complainant" class="form-control" value="{{ $record->complainant }}">
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small fw-bold">Respondent</label>
                                            <input type="text" name="respondent" class="form-control" value="{{ $record->respondent }}">
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <label class="small fw-bold">Type</label>
                                                <select name="incident_type" class="form-control">
                                                    <option selected>{{ $record->incident_type }}</option>
                                                    <option>Noise Complaint</option><option>Theft</option><option>Property Dispute</option>
                                                    <option>Physical Injury</option><option>Harassment</option><option>Others</option>
                                                </select>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <label class="small fw-bold">Priority</label>
                                                <select name="priority" class="form-control">
                                                    <option selected>{{ $record->priority }}</option>
                                                    <option>Low</option><option>Medium</option><option>High</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small fw-bold">Date</label>
                                            <input type="datetime-local" name="date_reported" class="form-control" value="{{ $record->date_reported->format('Y-m-d\TH:i') }}">
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="small fw-bold">Location</label>
                                            <input type="text" name="location" class="form-control" value="{{ $record->location }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="small fw-bold">Narrative</label>
                                            <textarea name="narrative" class="form-control" rows="3">{{ $record->narrative }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ================= DELETE MODAL ================= --}}
                    <div class="modal fade" id="deleteModal{{ $record->id }}" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-body text-center pt-4">
                                    <i class="fas fa-exclamation-circle text-danger fa-3x mb-3"></i>
                                    <h5>Delete Record?</h5>
                                    <p class="text-muted small">Are you sure? This cannot be undone.</p>
                                    <form action="{{ route('captain.incident.destroy', $record->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100 mb-2">Yes, Delete it</button>
                                        <button type="button" class="btn btn-light w-100" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $records->links() }}</div>
    </div>
</div>

{{-- Log Incident Modal (Same as before) --}}
<div class="modal fade" id="logIncidentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('captain.incident.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Log New Incident</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label>Complainant</label><input type="text" name="complainant" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label>Respondent</label><input type="text" name="respondent" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-6"><label>Type</label><select name="incident_type" class="form-control"><option>Noise Complaint</option><option>Theft</option><option>Others</option></select></div>
                        <div class="col-6"><label>Priority</label><select name="priority" class="form-control"><option>Low</option><option>Medium</option><option>High</option></select></div>
                    </div>
                    <div class="form-group mb-2"><label>Date</label><input type="datetime-local" name="date_reported" class="form-control" required></div>
                    <div class="form-group mb-2"><label>Location</label><input type="text" name="location" class="form-control" required></div>
                    <div class="form-group"><label>Narrative</label><textarea name="narrative" class="form-control" rows="3" required></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Log Case</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection