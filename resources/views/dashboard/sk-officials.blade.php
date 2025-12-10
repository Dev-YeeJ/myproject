{{-- resources/views/dashboard/sk-officials.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'SK Officials Management')

@section('nav-items')
    {{-- 1. Dashboard Overview --}}
    <li class="nav-item">
        <a href="{{ route('sk.dashboard') }}" class="nav-link {{ request()->routeIs('sk.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- 2. KK Youth Profiling --}}
    <li class="nav-item">
        <a href="{{ route('sk.youth-profiling') }}" class="nav-link {{ request()->routeIs('sk.youth-profiling') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>KK Profiling</span>
        </a>
    </li>

    {{-- 3. Projects & Events --}}
    <li class="nav-item">
        <a href="{{ route('sk.projects') }}" class="nav-link {{ request()->routeIs('sk.projects') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Projects & Events</span>
        </a>
    </li>

    {{-- 4. SK Officials (Active) --}}
    <li class="nav-item">
        <a href="{{ route('sk.officials') }}" class="nav-link {{ request()->routeIs('sk.officials') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i>
            <span>SK Officials</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- THEME STYLES (Blue Gradient) --- */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 40px; border-radius: 16px;
        margin-bottom: 30px; position: relative;
        box-shadow: 0 4px 12px rgba(43, 92, 230, 0.2);
    }
    .header-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .header-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
        margin-top: 10px;
    }
    .barangay-badge .badge-icon {
        background: white; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #2B5CE6;
    }

    /* --- OFFICIAL CARD STYLES --- */
    .avatar-placeholder {
        width: 80px; 
        height: 80px; 
        background: #EFF6FF; /* Soft Blue */
        color: #2B5CE6;       /* Blue Icon */
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 15px;
        border: 2px solid #DBEAFE;
    }
    .card-official {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        overflow: hidden;
    }
    .card-official:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #2B5CE6;
    }
    .badge-active {
        background-color: #D1FAE5; 
        color: #065F46; 
        font-size: 0.75rem; 
        padding: 4px 10px; 
        border-radius: 99px;
        font-weight: 600;
    }
    .text-position {
        color: #DC2626; /* Red for SK positions to stand out */
        font-weight: 700;
        margin-bottom: 8px;
    }
</style>

{{-- Header Section --}}
<div class="header-section">
    <div class="header-title">SK Officials Management</div>
    <div class="header-subtitle">Organizational Structure & Appointments</div>
    <div class="barangay-badge">
        <span class="badge-icon">SK</span>
        <span>Barangay Calbueg, Malasiqui</span>
    </div>
</div>

{{-- Main Content Area --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="font-weight-bold text-dark mb-0">Current Appointments</h4>
    <button class="btn btn-danger font-weight-bold shadow-sm" data-toggle="modal" data-target="#officialModal" data-mode="add">
        <i class="fas fa-user-plus mr-2"></i>Appoint Official
    </button>
</div>

{{-- Success & Error Alerts --}}
@if(session('success')) 
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div> 
@endif

@if($errors->any()) 
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
        <ul class="mb-0 pl-3">
            @foreach($errors->all() as $error) 
                <li>{{ $error }}</li> 
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div> 
@endif

{{-- Officials Grid --}}
<div class="row">
    @forelse($officials as $official)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card card-official h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                {{-- Avatar --}}
                <div class="avatar-placeholder">
                    <i class="fas fa-user"></i>
                </div>
                
                {{-- Name & Position --}}
                <h5 class="font-weight-bold text-dark mb-1">
                    {{ $official->resident->first_name }} {{ $official->resident->last_name }}
                </h5>
                <p class="text-position">{{ $official->position }}</p>
                
                {{-- Committee Badge --}}
                <span class="badge badge-light border mb-3 px-3 py-2 text-muted">
                    {{ $official->committee ?? 'No Committee Assigned' }}
                </span>

                {{-- Term Details --}}
                <div class="d-flex justify-content-center align-items-center text-muted small mb-4">
                    <div class="mr-3">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ $official->term_start->format('Y') }} - {{ $official->term_end->format('Y') }}
                    </div>
                    @if($official->is_active)
                        <span class="badge-active"><i class="fas fa-circle mr-1" style="font-size: 6px;"></i> Active</span>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="border-top pt-3">
                    <button class="btn btn-sm btn-light text-primary font-weight-bold mr-2" 
                        data-toggle="modal" 
                        data-target="#officialModal" 
                        data-mode="edit"
                        data-id="{{ $official->id }}"
                        data-pos="{{ $official->position }}"
                        data-com="{{ $official->committee }}"
                        data-start="{{ $official->term_start->format('Y-m-d') }}"
                        data-end="{{ $official->term_end->format('Y-m-d') }}">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    
                    <form action="{{ route('sk.officials.destroy', $official->id) }}" method="POST" class="d-inline">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light text-danger font-weight-bold" onclick="return confirm('Are you sure you want to remove this official? This action cannot be undone.')">
                            <i class="fas fa-trash-alt mr-1"></i> Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 bg-white rounded border border-dashed shadow-sm">
            <div class="text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"><i class="fas fa-users-slash"></i></div>
            <h4 class="text-muted font-weight-bold">No Officials Appointed Yet</h4>
            <p class="text-muted mb-4">Start by appointing an SK Chairperson or Kagawad from the resident list.</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#officialModal" data-mode="add">
                Appoint First Official
            </button>
        </div>
    </div>
    @endforelse
</div>

{{-- Unified Modal (Add/Edit) --}}
<div class="modal fade" id="officialModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalTitle">Appoint Official</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="officialForm" method="POST">
                @csrf
                <div id="methodField"></div> {{-- Placeholder for PUT method --}}
                
                <div class="modal-body p-4">
                    {{-- Resident Selection (Only visible in Add Mode) --}}
                    <div class="form-group" id="residentGroup">
                        <label class="font-weight-bold text-dark">Select Resident <span class="text-danger">*</span></label>
                        <select name="resident_id" class="form-control custom-select" required>
                            <option value="">-- Select Eligible Resident (15-30yo) --</option>
                            @foreach($eligible as $resident)
                                {{-- NOTICE: Using date_of_birth to calculate age --}}
                                <option value="{{ $resident->id }}">{{ $resident->last_name }}, {{ $resident->first_name }} ({{ \Carbon\Carbon::parse($resident->date_of_birth)->age }} yo)</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Only showing residents aged 15-30 who are not yet officials.</small>
                    </div>

                    {{-- Position Selection --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Position <span class="text-danger">*</span></label>
                        <select name="position" id="oPos" class="form-control custom-select" required>
                            <option value="SK Chairperson">SK Chairperson</option>
                            <option value="SK Kagawad">SK Kagawad</option>
                            <option value="SK Secretary">SK Secretary</option>
                            <option value="SK Treasurer">SK Treasurer</option>
                        </select>
                    </div>

                    {{-- Committee --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Committee Assignment</label>
                        <input type="text" name="committee" id="oCom" class="form-control" placeholder="e.g. Committee on Sports & Youth Development">
                    </div>

                    {{-- Term Dates --}}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Term Start <span class="text-danger">*</span></label>
                                <input type="date" name="term_start" id="oStart" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Term End <span class="text-danger">*</span></label>
                                <input type="date" name="term_end" id="oEnd" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">Save Official</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts to handle Modal Logic --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#officialModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var mode = button.data('mode');
            var modal = $(this);
            var form = modal.find('#officialForm');

            if (mode === 'edit') {
                // Switch to Edit Mode
                modal.find('#modalTitle').text('Edit Official Details');
                modal.find('#methodField').html('<input type="hidden" name="_method" value="PUT">');
                
                // Hide Resident Selector (Cannot change person once appointed)
                modal.find('#residentGroup').hide();
                modal.find('select[name="resident_id"]').prop('required', false);

                // Populate Fields
                modal.find('#oPos').val(button.data('pos'));
                modal.find('#oCom').val(button.data('com'));
                modal.find('#oStart').val(button.data('start'));
                modal.find('#oEnd').val(button.data('end'));
                
                // Set Action URL
                form.attr('action', '/sk/officials/' + button.data('id'));
            
            } else {
                // Switch to Add Mode
                modal.find('#modalTitle').text('Appoint New Official');
                modal.find('#methodField').html('');
                
                // Show Resident Selector
                modal.find('#residentGroup').show();
                modal.find('select[name="resident_id"]').prop('required', true);
                
                // Reset Form
                form.trigger('reset');
                form.attr('action', '{{ route("sk.officials.store") }}');
            }
        });
    });
</script>
@endsection