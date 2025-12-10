{{-- resources/views/dashboard/sk-projects.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'SK Projects & Events')

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

    {{-- 3. Projects & Events (Active) --}}
    <li class="nav-item">
        <a href="{{ route('sk.projects') }}" class="nav-link {{ request()->routeIs('sk.projects') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Projects & Events</span>
        </a>
    </li>

    {{-- 4. SK Officials --}}
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
    .budget-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(43, 92, 230, 0.15);
    }
    .header-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 4px; }
    
    /* Navigation Toggles */
    .view-toggles {
        margin-bottom: 30px; display: flex; gap: 0;
        border-radius: 10px; overflow: hidden;
        border: 2px solid #2B5CE6;
        width: fit-content;
        background: white;
    }
    .btn-toggle {
        padding: 12px 24px; border: none; font-weight: 600;
        display: flex; align-items: center; gap: 10px;
        cursor: pointer; transition: all 0.3s; font-size: 0.95rem;
        text-decoration: none; background: white; color: #2B5CE6;
    }
    .btn-toggle.active { background: #2B5CE6; color: white; }
    .btn-toggle:not(.active):hover { background: #EFF6FF; }

    /* Custom Styling for Status & Progress */
    .progress-thin { height: 6px; border-radius: 10px; background-color: #E5E7EB; }
    .status-badge { font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .status-planning { background-color: #FEF3C7; color: #D97706; } /* Yellow/Orange */
    .status-progress { background-color: #DBEAFE; color: #1E40AF; } /* Blue */
    .status-completed { background-color: #D1FAE5; color: #065F46; } /* Green */
    .status-cancelled { background-color: #F3F4F6; color: #6B7280; } /* Gray */
</style>

{{-- Navigation Toggles --}}
<div class="view-toggles">
    <a href="{{ route('sk.dashboard') }}" class="btn-toggle">
        <i class="fas fa-home"></i>
        <span>Overview</span>
    </a>
    <a href="{{ route('sk.youth-profiling') }}" class="btn-toggle">
        <i class="fas fa-users"></i>
        <span>Youth</span>
    </a>
    <a href="{{ route('sk.projects') }}" class="btn-toggle active">
        <i class="fas fa-tasks"></i>
        <span>Projects</span>
    </a>
    <a href="{{ route('sk.officials') }}" class="btn-toggle">
        <i class="fas fa-user-tie"></i>
        <span>Officials</span>
    </a>
</div>

{{-- 1. Budget Overview Header --}}
<div class="card budget-header border-0 mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="header-title">SK Projects & Events</h2>
                <p class="mb-0 opacity-90">Manage youth development programs and track budget utilization.</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0 border-md-left border-white-50 pl-md-4">
                <small class="text-uppercase font-weight-bold opacity-75 d-block">Available Funds</small>
                <h1 class="font-weight-bold mb-0">₱{{ number_format($remainingBudget, 2) }}</h1>
                <small class="opacity-75">Fiscal Year {{ now()->year }}</small>
            </div>
        </div>
    </div>
</div>

{{-- 2. Validation & Success Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
        <ul class="mb-0 pl-3">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

{{-- 3. Filter Tabs & Add Button --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
    <ul class="nav nav-pills mb-3 mb-md-0">
        <li class="nav-item">
            <a class="nav-link {{ $status == 'All' ? 'active bg-primary text-white' : 'text-secondary bg-light mr-1' }}" href="?status=All">All Projects</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'In Progress' ? 'active bg-primary text-white' : 'text-secondary bg-light mr-1' }}" href="?status=In Progress">In Progress</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'Completed' ? 'active bg-success text-white' : 'text-secondary bg-light' }}" href="?status=Completed">Completed</a>
        </li>
    </ul>
    
    <button class="btn btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#projectModal" data-mode="add">
        <i class="fas fa-plus mr-2"></i>Create New Project
    </button>
</div>

{{-- 4. Projects Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="pl-4 py-3 border-top-0">Project Title</th>
                        <th class="py-3 border-top-0">Timeline</th>
                        <th class="py-3 border-top-0">Budget</th>
                        <th class="py-3 border-top-0">Status</th>
                        <th class="py-3 border-top-0" style="width: 20%;">Progress</th>
                        <th class="py-3 border-top-0 text-right pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        {{-- Title --}}
                        <td class="pl-4">
                            <div class="font-weight-bold text-dark">{{ $project->title }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $project->description }}</small>
                        </td>
                        
                        {{-- Timeline --}}
                        <td class="small text-muted">
                            <div class="d-flex align-items-center">
                                <i class="far fa-calendar-alt mr-2"></i>
                                <div>
                                    <div class="text-dark">{{ $project->start_date->format('M d, Y') }}</div>
                                    @if($project->end_date)
                                    <div class="text-muted">to {{ $project->end_date->format('M d, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Budget --}}
                        <td class="text-danger font-weight-bold">₱{{ number_format($project->budget, 2) }}</td>

                        {{-- Status Badge --}}
                        <td>
                            @php
                                $badgeClass = match($project->status) {
                                    'Planning' => 'status-planning',
                                    'In Progress' => 'status-progress',
                                    'Completed' => 'status-completed',
                                    'Cancelled' => 'status-cancelled',
                                    default => 'badge-light'
                                };
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">{{ $project->status }}</span>
                        </td>

                        {{-- Progress Bar --}}
                        <td>
                            <div class="d-flex align-items-center mb-1">
                                <span class="small font-weight-bold text-dark mr-auto">Completion</span>
                                <span class="small font-weight-bold text-muted">{{ $project->progress }}%</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar {{ $project->status == 'Completed' ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $project->progress }}%"></div>
                            </div>
                        </td>

                        {{-- Action Buttons --}}
                        <td class="text-right pr-4">
                            {{-- Edit Button --}}
                            <button class="btn btn-sm btn-light text-primary font-weight-bold mr-1" 
                                data-toggle="modal" 
                                data-target="#projectModal" 
                                data-mode="edit"
                                data-id="{{ $project->id }}" 
                                data-title="{{ $project->title }}" 
                                data-desc="{{ $project->description }}"
                                data-budget="{{ $project->budget }}"
                                data-start="{{ $project->start_date->format('Y-m-d') }}"
                                data-end="{{ $project->end_date ? $project->end_date->format('Y-m-d') : '' }}"
                                data-status="{{ $project->status }}"
                                data-progress="{{ $project->progress }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Delete Button --}}
                            <form action="{{ route('sk.projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger font-weight-bold">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"><i class="fas fa-folder-open"></i></div>
                            <p class="text-muted mb-0">No projects found for this category.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($projects->hasPages())
        <div class="card-footer bg-white border-0 pt-3">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>

{{-- 5. Unified Create/Edit Modal --}}
<div class="modal fade" id="projectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalTitle">Create New Project</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="projectForm" method="POST">
                @csrf
                <div id="methodField"></div> {{-- Placeholder for PUT input --}}
                
                <div class="modal-body p-4">
                    {{-- Basic Info Section --}}
                    <h6 class="text-uppercase text-muted small font-weight-bold mb-3 border-bottom pb-2">Project Details</h6>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="pTitle" class="form-control" placeholder="e.g. Annual Linggo ng Kabataan" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Description</label>
                        <textarea name="description" id="pDesc" class="form-control" rows="3" placeholder="Brief description of the event..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Allocated Budget (₱) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">₱</span></div>
                                    <input type="number" name="budget" id="pBudget" class="form-control" step="0.01" min="0" required>
                                </div>
                                <small class="form-text text-muted">Must fit within available funds.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="pStart" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">End Date</label>
                                <input type="date" name="end_date" id="pEnd" class="form-control">
                            </div>
                        </div>
                    </div>

                    {{-- Status Section (Hidden for Add Mode) --}}
                    <div id="statusSection" style="display: none;">
                        <h6 class="text-uppercase text-muted small font-weight-bold mt-4 mb-3 border-bottom pb-2">Status Update</h6>
                        <div class="row bg-light p-3 rounded mx-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark">Current Status</label>
                                    <select name="status" id="pStatus" class="form-control custom-select">
                                        <option value="Planning">Planning</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark">Progress Percentage (%)</label>
                                    <input type="number" name="progress" id="pProgress" class="form-control" min="0" max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 6. JavaScript Logic for Modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#projectModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var mode = button.data('mode');
            var modal = $(this);
            var form = modal.find('#projectForm');

            if (mode === 'edit') {
                // Edit Mode Configuration
                modal.find('#modalTitle').text('Update Project Details');
                modal.find('#methodField').html('<input type="hidden" name="_method" value="PUT">');
                modal.find('#statusSection').show(); // Show status controls
                
                // Populate Fields
                modal.find('#pTitle').val(button.data('title'));
                modal.find('#pDesc').val(button.data('desc'));
                modal.find('#pBudget').val(button.data('budget'));
                modal.find('#pStart').val(button.data('start'));
                modal.find('#pEnd').val(button.data('end'));
                modal.find('#pStatus').val(button.data('status'));
                modal.find('#pProgress').val(button.data('progress'));
                
                // Set Action URL
                form.attr('action', '/sk/projects/' + button.data('id'));
            } else {
                // Add Mode Configuration
                modal.find('#modalTitle').text('Create New Project');
                modal.find('#methodField').html('');
                modal.find('#statusSection').hide(); // Hide status controls
                
                // Reset Form
                form.trigger('reset');
                form.attr('action', '{{ route("sk.projects.store") }}');
            }
        });
    });
</script>
@endsection