{{-- resources/views/dashboard/sk-youth-profiling.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Katipunan ng Kabataan Profile')

@section('nav-items')
    {{-- 1. Dashboard Overview --}}
    <li class="nav-item">
        <a href="{{ route('sk.dashboard') }}" class="nav-link {{ request()->routeIs('sk.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- 2. KK Youth Profiling (Active) --}}
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
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 40px; border-radius: 16px;
        margin-bottom: 30px; position: relative;
        box-shadow: 0 4px 12px rgba(43, 92, 230, 0.2);
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 255, 255, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: white; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #2B5CE6;
    }

    /* Hero Stat */
    .total-registered {
        position: absolute; top: 40px; right: 40px; text-align: right;
    }
    .total-registered-label { font-size: 0.9rem; opacity: 0.9; margin-bottom: 4px; }
    .total-registered-count { font-size: 2.5rem; font-weight: 700; }
    .total-registered-sublabel { font-size: 0.85rem; opacity: 0.9; }

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

    /* Table Styles */
    .avatar-small {
        width: 40px; height: 40px;
        background-color: #EFF6FF; color: #2B5CE6;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
        border: 1px solid #DBEAFE;
    }
    .badge-soft-primary { background-color: #EFF6FF; color: #2563EB; }
    .badge-soft-success { background-color: #ECFDF5; color: #059669; }
    .badge-soft-secondary { background-color: #F3F4F6; color: #4B5563; }

    @media (max-width: 768px) {
        .total-registered { position: static; text-align: left; margin-top: 20px; }
        .view-toggles { width: 100%; overflow-x: auto; }
    }
</style>

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Katipunan ng Kabataan</div>
    <div class="profiling-subtitle">Official Registry of Barangay Youth (15-30 Years Old)</div>
    <div class="barangay-badge">
        <span class="badge-icon">SK</span>
        <span>Barangay Calbueg, Malasiqui</span>
    </div>
    
    <div class="total-registered">
        <div class="total-registered-label">Total KK Members</div>
        <div class="total-registered-count">{{ $youths->total() }}</div>
        <div class="total-registered-sublabel">Registered Residents</div>
    </div>
</div>

{{-- Navigation Toggles --}}
<div class="view-toggles">
    <a href="{{ route('sk.dashboard') }}" class="btn-toggle">
        <i class="fas fa-home"></i>
        <span>Overview</span>
    </a>
    <a href="{{ route('sk.youth-profiling') }}" class="btn-toggle active">
        <i class="fas fa-users"></i>
        <span>Youth</span>
    </a>
    <a href="{{ route('sk.projects') }}" class="btn-toggle">
        <i class="fas fa-tasks"></i>
        <span>Projects</span>
    </a>
    <a href="{{ route('sk.officials') }}" class="btn-toggle">
        <i class="fas fa-user-tie"></i>
        <span>Officials</span>
    </a>
</div>

{{-- Search & Filter Bar --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('sk.youth-profiling') }}" method="GET">
            <div class="row align-items-center">
                {{-- Search Input --}}
                <div class="col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Search by name..." value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter Dropdown --}}
                <div class="col-md-4 mb-2 mb-md-0">
                    <select name="filter" class="form-control custom-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <option value="student" {{ request('filter') == 'student' ? 'selected' : '' }}>Students</option>
                        <option value="working" {{ request('filter') == 'working' ? 'selected' : '' }}>Working Youth (Employed)</option>
                        <option value="voter" {{ request('filter') == 'voter' ? 'selected' : '' }}>Registered Voters</option>
                        <option value="female" {{ request('filter') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="male" {{ request('filter') == 'male' ? 'selected' : '' }}>Male</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-3 text-md-right d-flex gap-2 justify-content-end">
                    {{-- Filter Button --}}
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-filter"></i>
                    </button>

                    {{-- Print Button (New Feature) --}}
                    <a href="{{ route('sk.youth-profiling.print', ['filter' => request('filter')]) }}" 
                       target="_blank" 
                       class="btn btn-dark font-weight-bold ml-1" 
                       title="Print Official List">
                        <i class="fas fa-print mr-1"></i> Print
                    </a>

                    {{-- Clear Button --}}
                    @if(request('search') || request('filter'))
                        <a href="{{ route('sk.youth-profiling') }}" class="btn btn-light border ml-1 text-muted" title="Clear Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="pl-4 py-3 border-top-0">Member Name</th>
                        <th class="py-3 border-top-0">Age</th>
                        <th class="py-3 border-top-0">Gender</th>
                        <th class="py-3 border-top-0">Occupation</th>
                        <th class="py-3 border-top-0">Voter Status</th>
                        <th class="py-3 border-top-0 text-right pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($youths as $youth)
                    <tr>
                        {{-- Name & Household --}}
                        <td class="pl-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-small mr-3">
                                    {{ substr($youth->first_name, 0, 1) }}{{ substr($youth->last_name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $youth->last_name }}, {{ $youth->first_name }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-home mr-1"></i> 
                                        Household #{{ $youth->household->household_number ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        {{-- Age (Using calculated_age from Controller) --}}
                        <td>
                            <span class="font-weight-bold text-dark">{{ $youth->calculated_age }}</span>
                            <small class="text-muted d-block">Years old</small>
                        </td>

                        {{-- Gender --}}
                        <td>{{ $youth->gender }}</td>

                        {{-- Occupation Status --}}
                        <td>
                            @if($youth->occupation == 'Student') 
                                <span class="badge badge-soft-primary px-3 py-2 rounded-pill">Student</span>
                            @elseif($youth->monthly_income > 0) 
                                <span class="badge badge-soft-success px-3 py-2 rounded-pill">Employed</span>
                            @else 
                                <span class="badge badge-soft-secondary px-3 py-2 rounded-pill">Unemployed</span>
                            @endif
                        </td>

                        {{-- Voter Status --}}
                        <td>
                            @if($youth->is_registered_voter)
                                <div class="text-success small font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> Registered
                                </div>
                            @else
                                <div class="text-muted small">
                                    <i class="fas fa-times-circle mr-1"></i> Not Registered
                                </div>
                            @endif
                        </td>

                        {{-- Action Button (Quick View) --}}
                        <td class="text-right pr-4">
                            <button class="btn btn-sm btn-light text-primary font-weight-bold border" 
                                data-toggle="modal" 
                                data-target="#viewResidentModal"
                                data-name="{{ $youth->first_name }} {{ $youth->last_name }}"
                                data-age="{{ $youth->calculated_age }}"
                                data-bday="{{ \Carbon\Carbon::parse($youth->date_of_birth)->format('M d, Y') }}"
                                data-gender="{{ $youth->gender }}"
                                data-civil="{{ $youth->civil_status }}"
                                data-address="{{ $youth->address ?? 'Barangay Calbueg' }}"
                                data-occupation="{{ $youth->occupation }}"
                                data-contact="{{ $youth->contact_number ?? 'N/A' }}"
                                data-voter="{{ $youth->is_registered_voter ? 'Yes' : 'No' }}">
                                <i class="far fa-eye mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 2rem; opacity: 0.3;"><i class="fas fa-user-slash"></i></div>
                            <h5 class="text-muted font-weight-bold">No Records Found</h5>
                            <p class="text-muted mb-0">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($youths->hasPages())
        <div class="card-footer bg-white border-0 pt-3">
            {{ $youths->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Quick View Modal --}}
<div class="modal fade" id="viewResidentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-dark">Resident Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-small mx-auto bg-light text-primary mb-2" style="width: 70px; height: 70px; font-size: 1.5rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="font-weight-bold mb-0" id="mName">--</h4>
                    <span class="badge badge-primary" id="mAge">-- years old</span>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Birthdate</small>
                        <p class="font-weight-bold text-dark mb-0" id="mBday">--</p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Gender</small>
                        <p class="font-weight-bold text-dark mb-0" id="mGender">--</p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Civil Status</small>
                        <p class="font-weight-bold text-dark mb-0" id="mCivil">--</p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Contact</small>
                        <p class="font-weight-bold text-dark mb-0" id="mContact">--</p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Occupation</small>
                        <p class="font-weight-bold text-dark mb-0" id="mOcc">--</p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-uppercase text-muted font-weight-bold">Registered Voter</small>
                        <p class="font-weight-bold text-dark mb-0" id="mVoter">--</p>
                    </div>
                    <div class="col-12">
                        <small class="text-uppercase text-muted font-weight-bold">Address</small>
                        <p class="font-weight-bold text-dark mb-0" id="mAddress">--</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Logic --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#viewResidentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            
            // Populate Modal Data
            modal.find('#mName').text(button.data('name'));
            modal.find('#mAge').text(button.data('age') + ' years old');
            modal.find('#mBday').text(button.data('bday'));
            modal.find('#mGender').text(button.data('gender'));
            modal.find('#mCivil').text(button.data('civil'));
            modal.find('#mAddress').text(button.data('address'));
            modal.find('#mOcc').text(button.data('occupation'));
            modal.find('#mContact').text(button.data('contact'));
            
            var isVoter = button.data('voter');
            var voterText = modal.find('#mVoter');
            voterText.text(isVoter);
            
            if(isVoter === 'Yes') {
                voterText.removeClass('text-danger').addClass('text-success');
            } else {
                voterText.removeClass('text-success').addClass('text-danger');
            }
        });
    });
</script>
@endsection