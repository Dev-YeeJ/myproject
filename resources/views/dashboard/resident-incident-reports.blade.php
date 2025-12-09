@extends('layouts.dashboard-layout')

@section('title', 'My Incident Reports')

@section('content')

{{-- Alerts --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<style>
    .incident-header-res {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
        color: white; border-radius: 16px; padding: 30px 40px; margin-bottom: 30px;
        display: flex; justify-content: space-between; align-items: center;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
    }
    .stat-card-res {
        background: white; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-bottom: 4px solid #DC2626; text-align: center;
    }
    .stat-val { font-size: 1.8rem; font-weight: 700; color: #1F2937; }
    .stat-lbl { color: #6B7280; font-size: 0.9rem; }
    
    .badge-status { padding: 5px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-Open { background: #DBEAFE; color: #1E40AF; }
    .status-Resolved { background: #D1FAE5; color: #065F46; }
    .status-Hearing { background: #E0E7FF; color: #4338CA; border: 1px solid #6366f1; }
    .status-Investigation { background: #FEF3C7; color: #92400E; }
    .status-Dismissed { background: #F3F4F6; color: #374151; text-decoration: line-through; }

    .log-box {
        background: #f8f9fa; border: 1px solid #e9ecef; padding: 10px;
        border-radius: 6px; font-size: 0.85rem; max-height: 150px; overflow-y: auto;
        font-family: monospace; color: #333;
    }
</style>

{{-- Header --}}
<div class="incident-header-res">
    <div>
        <h2 style="font-weight: 700; margin: 0;">My Incident Reports</h2>
        <p style="margin: 5px 0 0; opacity: 0.9;">File a complaint or track the status of your reported cases.</p>
    </div>
    <div style="font-size: 2.5rem; opacity: 0.8;"><i class="fas fa-shield-alt"></i></div>
</div>

{{-- Stats --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card-res">
            <div class="stat-val">{{ $stats['my_total_reports'] }}</div>
            <div class="stat-lbl">Total Reports Filed</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-res" style="border-bottom-color: #F59E0B;">
            <div class="stat-val">{{ $stats['open_cases'] }}</div>
            <div class="stat-lbl">Active Cases</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-res" style="border-bottom-color: #10B981;">
            <div class="stat-val">{{ $stats['resolved_cases'] }}</div>
            <div class="stat-lbl">Resolved Cases</div>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-secondary">Report History</h5>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#fileIncidentModal">
                <i class="fas fa-plus-circle me-1"></i> File New Report
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Case #</th>
                        <th>Date Reported</th>
                        <th>Type</th>
                        <th>Narrative</th>
                        <th>Status</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myIncidents as $incident)
                    <tr>
                        <td class="fw-bold text-danger">{{ $incident->case_number }}</td>
                        <td>{{ $incident->date_reported->format('M d, Y') }}</td>
                        <td>{{ $incident->incident_type }}</td>
                        <td>{{ Str::limit($incident->narrative, 30) }}</td>
                        <td>
                            @php
                                $statusClass = match($incident->status) {
                                    'Resolved' => 'status-Resolved',
                                    'Open' => 'status-Open',
                                    'Scheduled for Hearing' => 'status-Hearing',
                                    'Dismissed' => 'status-Dismissed',
                                    default => 'status-Investigation'
                                };
                            @endphp
                            <span class="badge-status {{ $statusClass }}">{{ $incident->status }}</span>
                        </td>
                        <td class="text-end">
                            {{-- View/Edit Button --}}
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $incident->id }}">
                                <i class="fas fa-eye"></i> Details
                            </button>
                        </td>
                    </tr>

                    {{-- VIEW / MANAGE MODAL per Incident --}}
                    <div class="modal fade" id="viewModal{{ $incident->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold">Case: {{ $incident->case_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        {{-- LEFT: Details --}}
                                        <div class="col-md-6 border-end">
                                            <h6 class="fw-bold text-danger mb-3">Report Details</h6>
                                            <p class="mb-1 small text-muted">RESPONDENT:</p>
                                            <p class="fw-bold">{{ $incident->respondent ?? 'N/A' }}</p>

                                            <p class="mb-1 small text-muted">LOCATION:</p>
                                            <p class="fw-bold">{{ $incident->location }}</p>

                                            <p class="mb-1 small text-muted">NARRATIVE:</p>
                                            <p class="bg-light p-2 rounded small">{{ $incident->narrative }}</p>

                                            <hr>
                                            <h6 class="fw-bold text-primary mb-2">Case History (Updates)</h6>
                                            <div class="log-box">
                                                {{-- Shows hearing dates and updates from Captain --}}
                                                {{ $incident->actions_taken ?? 'No updates yet.' }}
                                            </div>
                                        </div>

                                        {{-- RIGHT: Actions --}}
                                        <div class="col-md-6">
                                            @if($incident->status === 'Open')
                                                {{-- EDIT FORM (Only if Open) --}}
                                                <h6 class="fw-bold text-secondary mb-3">Edit Report</h6>
                                                <form action="{{ route('resident.incidents.update', $incident->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Incident Type</label>
                                                        <select name="incident_type" class="form-select form-select-sm">
                                                            <option selected>{{ $incident->incident_type }}</option>
                                                            <option>Noise Complaint</option>
                                                            <option>Theft</option>
                                                            <option>Property Dispute</option>
                                                            <option>Vandalism</option>
                                                            <option>Others</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="small fw-bold">Narrative Update</label>
                                                        <textarea name="narrative" class="form-control form-control-sm" rows="3">{{ $incident->narrative }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="small fw-bold">Date & Location</label>
                                                        <input type="text" name="location" value="{{ $incident->location }}" class="form-control form-control-sm mb-1">
                                                        <input type="datetime-local" name="date_reported" value="{{ $incident->date_reported->format('Y-m-d\TH:i') }}" class="form-control form-control-sm">
                                                    </div>

                                                    <button type="submit" class="btn btn-sm btn-primary w-100 mb-3">Save Changes</button>
                                                </form>

                                                <hr>
                                                
                                                {{-- CANCEL FORM --}}
                                                <h6 class="fw-bold text-secondary mb-2">Danger Zone</h6>
                                                <form action="{{ route('resident.incidents.cancel', $incident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to withdraw this report?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                        <i class="fas fa-times-circle"></i> Withdraw / Cancel Report
                                                    </button>
                                                </form>
                                            @else
                                                {{-- LOCKED VIEW --}}
                                                <div class="text-center py-5">
                                                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                                    <h5>Report Locked</h5>
                                                    <p class="small text-muted">
                                                        This case is currently <strong>{{ $incident->status }}</strong>. 
                                                        You cannot edit or cancel it anymore. Please wait for further updates from the Barangay.
                                                    </p>
                                                    @if($incident->status == 'Scheduled for Hearing')
                                                        <div class="alert alert-warning small">
                                                            <i class="fas fa-gavel"></i> Check the Case History for your Hearing Schedule!
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Modal --}}

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open mb-2" style="font-size: 2rem;"></i>
                            <p>You haven't filed any incident reports yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $myIncidents->links() }}</div>
    </div>
</div>

{{-- Modal for New Incident (Same as before) --}}
<div class="modal fade" id="fileIncidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('resident.incidents.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">File Incident Report</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Incident Type <span class="text-danger">*</span></label>
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
                    <div class="mb-3">
                        <label class="form-label">Date & Time of Incident <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_reported" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Purok 3, near Chapel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name of Respondent</label>
                        <input type="text" name="respondent" class="form-control" placeholder="Leave blank if unknown">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Narrative / Details <span class="text-danger">*</span></label>
                        <textarea name="narrative" class="form-control" rows="4" placeholder="Describe what happened..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('fileIncidentModal'));
            myModal.show();
        @endif
    });
</script>
@endsection