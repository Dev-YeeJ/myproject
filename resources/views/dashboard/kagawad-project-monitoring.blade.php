@extends('layouts.dashboard-layout')

@section('title', 'Project Monitoring')

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
        {{-- ACTIVE PAGE --}}
        <a href="{{ route('kagawad.projects') }}" class="nav-link active">
            <i class="fas fa-tasks"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.incidents') }}" class="nav-link">
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
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    
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
    .stat-badge.purple { color: #A855F7; }

    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }
    .icon-blue-bg { background: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; }
    .icon-green-bg { background: #10B981; }
    .icon-purple-bg { background: #A855F7; }

    /* Directory Header Style */
    .directory-header {
        background: white; border-bottom: 1px solid #E5E7EB;
        padding: 0 30px; border-radius: 12px 12px 0 0;
        display: flex; flex-direction: column;
    }
    .header-top { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
    .directory-title { display: flex; align-items: center; gap: 12px; font-size: 1.1rem; font-weight: 700; color: #1F2937; }

    .filters-section { display: flex; align-items: center; gap: 10px; }
    .search-input {
        padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.95rem; background: #F9FAFB; min-width: 250px;
    }
    /* Updated Button Style to match Captain's "New Project" button */
    .btn-add-new {
        background: #2B5CE6; color: white; border: none; padding: 10px 20px;
        border-radius: 8px; font-weight: 700; cursor: pointer; display: flex;
        align-items: center; gap: 8px; text-decoration: none; transition: all 0.3s;
    }
    .btn-add-new:hover { background: #1E3A8A; transform: translateY(-2px); }

    /* Tab Navigation */
    .nav-tabs { display: flex; gap: 30px; margin-top: 10px; }
    .nav-link-tab {
        padding: 15px 5px; color: #6B7280; font-weight: 600; text-decoration: none;
        border-bottom: 3px solid transparent; transition: 0.3s;
    }
    .nav-link-tab:hover { color: #2B5CE6; }
    .nav-link-tab.active { color: #2B5CE6; border-bottom-color: #2B5CE6; }
    .badge-count {
        background: #EF4444; color: white; font-size: 0.75rem; padding: 2px 8px;
        border-radius: 10px; margin-left: 5px; vertical-align: middle;
    }

    /* Project Grid Container */
    .project-grid-container {
        background: white; 
        padding: 30px; border-radius: 0 0 12px 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Project Card Styling */
    .project-card {
        background: white; border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 25px; border: 1px solid #E5E7EB;
        transition: transform 0.2s; position: relative;
        display: flex; flex-direction: column; height: 100%;
    }
    .project-card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(0,0,0,0.12); }
    
    .status-badge {
        position: absolute; top: 20px; right: 20px;
        font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 20px;
    }
    .status-active { background: #EFF6FF; color: #2B5CE6; }
    .status-proposed { background: #FEF3C7; color: #92400E; }
    .status-completed { background: #D1FAE5; color: #065F46; }

    /* Modals */
    .modal {
        display: none; position: fixed; z-index: 1000; left: 0; top: 0;
        width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5);
        align-items: center; justify-content: center;
    }
    .modal-content {
        background: white; padding: 30px; border-radius: 12px;
        max-width: 500px; width: 90%;
    }
    .modal-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .modal-title { font-size: 1.3rem; font-weight: 700; color: #1F2937; }
    
    .form-control { width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 6px; margin-bottom: 5px; }
    .form-label { display: block; font-weight: 600; margin-bottom: 5px; color: #374151; }
    .form-group { margin-bottom: 15px; }
    
    .btn-primary { background: #2B5CE6; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
    .btn-secondary { background: #E5E7EB; color: #374151; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; }
    
    @media (max-width: 1200px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>

{{-- Notifications --}}
@if(session('success'))
<div style="background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #6EE7B7;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Project Monitoring</div>
    <div class="profiling-subtitle">Track infrastructure, manage proposals, and monitor expenses.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg, Malasiqui, Pangasinan</span>
    </div>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_active'] }}</h3>
            <p>Active Projects</p>
            <div class="stat-badge blue">
                <i class="fas fa-folder-open"></i>
                <span>Ongoing</span>
            </div>
        </div>
        <div class="stat-box-icon icon-blue-bg">
            <i class="fas fa-folder"></i>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['my_proposals'] }}</h3>
            <p>My Proposals</p>
            <div class="stat-badge orange">
                <i class="fas fa-file-contract"></i>
                <span>Pending Approval</span>
            </div>
        </div>
        <div class="stat-box-icon icon-orange-bg">
            <i class="fas fa-file-alt"></i>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['completed_projects'] }}</h3>
            <p>Completed</p>
            <div class="stat-badge green">
                <i class="fas fa-check-circle"></i>
                <span>Finished</span>
            </div>
        </div>
        <div class="stat-box-icon icon-green-bg">
            <i class="fas fa-check"></i>
        </div>
    </div>

    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $stats['total_projects'] }}</h3>
            <p>Total Projects</p>
            <div class="stat-badge purple">
                <i class="fas fa-layer-group"></i>
                <span>All Records</span>
            </div>
        </div>
        <div class="stat-box-icon icon-purple-bg">
            <i class="fas fa-th-list"></i>
        </div>
    </div>
</div>

{{-- Directory / Tool Bar --}}
<div class="directory-header">
    <div class="header-top">
        <div class="directory-title">
            <i class="fas fa-th-large"></i>
            <span>Project Directory</span>
        </div>
        <div class="filters-section">
            <form action="{{ route('kagawad.projects') }}" method="GET" style="display:flex; gap:10px;">
                <input type="hidden" name="view" value="{{ $view }}">
                <input type="text" name="search" class="search-input" placeholder="🔍 Search projects..." value="{{ request('search') }}">
                <button type="submit" style="display:none;"></button>
            </form>
            <button onclick="openModal('proposalModal')" class="btn-add-new">
                <i class="fas fa-plus"></i> Propose Project
            </button>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="nav-tabs">
        <a href="{{ route('kagawad.projects', ['view' => 'active']) }}" class="nav-link-tab {{ $view == 'active' ? 'active' : '' }}">
            Active Projects
        </a>
        <a href="{{ route('kagawad.projects', ['view' => 'proposals']) }}" class="nav-link-tab {{ $view == 'proposals' ? 'active' : '' }}">
            My Proposals 
            @if($stats['my_proposals'] > 0)
                <span class="badge-count">{{ $stats['my_proposals'] }}</span>
            @endif
        </a>
    </div>
</div>

{{-- Projects Grid --}}
<div class="project-grid-container">

    @php $projects = ($view == 'active') ? $activeProjects : $proposals; @endphp

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        @forelse($projects as $project)
            <div class="project-card" style="{{ $view == 'proposals' ? 'border-left: 5px solid #F59E0B;' : '' }}">
                {{-- Status Badge --}}
                @if($project->status == 'Proposed')
                    <span class="status-badge status-proposed">Proposed</span>
                @elseif($project->status == 'In Progress')
                    <span class="status-badge status-active">Active</span>
                @else
                    <span class="status-badge status-completed">{{ $project->status }}</span>
                @endif
                
                <h3 style="margin: 0; font-size: 1.1rem; color: #111827; padding-right: 80px;">{{ $project->title }}</h3>
                
                <div style="margin-bottom: 15px; font-size: 0.9rem; color: #4B5563; margin-top: 5px; flex-grow: 1;">
                    <span style="display:inline-block; background:#F3F4F6; color:#374151; padding:2px 8px; border-radius:4px; font-size:0.75rem; margin-right:5px; border: 1px solid #E5E7EB;">
                        {{ $project->category }}
                    </span>
                    {{ Str::limit($project->description, 80) }}
                </div>

                {{-- Progress Bar (Only for Active) --}}
                @if($view == 'active')
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 5px; font-weight: 600; color: #4B5563;">
                        <span>Progress</span><span>{{ $project->progress }}%</span>
                    </div>
                    <div style="background: #F3F4F6; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 15px;">
                        <div style="height: 100%; background: #2B5CE6; width: {{ $project->progress }}%;"></div>
                    </div>

                    {{-- Financials --}}
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; background: #F9FAFB; padding: 12px; border-radius: 8px; border: 1px solid #F3F4F6;">
                        <div>
                            <div style="color: #6B7280; font-size: 0.75rem;">Budget</div>
                            <div style="font-weight: 700; color: #1F2937;">₱{{ number_format($project->budget) }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="color: #6B7280; font-size: 0.75rem;">Spent</div>
                            <div style="font-weight: 700; color: {{ $project->amount_spent > $project->budget ? '#EF4444' : '#10B981' }}">
                                ₱{{ number_format($project->amount_spent) }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Proposal Info --}}
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; background: #FEF3C7; padding: 12px; border-radius: 8px; color: #92400E;">
                        <div>
                            <div style="font-size: 0.75rem; opacity: 0.8;">Estimated Budget</div>
                            <div style="font-weight: 700;">₱{{ number_format($project->budget) }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; opacity: 0.8;">Target Start</div>
                            <div style="font-weight: 700;">{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 15px; font-size: 0.85rem; color: #92400E; text-align: center; font-style: italic;">
                        <i class="fas fa-clock"></i> Waiting for Captain approval
                    </div>
                @endif

                {{-- ACTIONS --}}
                @if($view == 'active' && $project->status != 'Completed')
                <div style="margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button onclick="openExpenseModal({{ $project->id }}, '{{ $project->title }}')" 
                            style="background: #FFA500; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; color: white; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 5px; transition: 0.2s;">
                        <i class="fas fa-receipt"></i> Expense
                    </button>

                    <button onclick="openUpdateModal({{ $project->id }}, {{ $project->progress }})" 
                            style="background: #EFF6FF; border: 1px solid #BFDBFE; padding: 10px 16px; border-radius: 6px; cursor: pointer; color: #1E40AF; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 5px; transition: 0.2s;">
                        <i class="fas fa-edit"></i> Update
                    </button>
                </div>
                @endif
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6B7280;">
                <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 10px;"></i>
                <p>No projects found in this category.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">{{ $projects->appends(['view' => $view])->links('pagination::bootstrap-4') }}</div>

</div>

{{-- MODAL 1: PROPOSE PROJECT --}}
<div id="proposalModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-plus-circle" style="color: #2B5CE6;"></i> Propose New Project</h3>
        </div>
        <form action="{{ route('kagawad.projects.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Project Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Street Light Repair Purok 1">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option>Infrastructure</option>
                        <option>Health</option>
                        <option>Environment</option>
                        <option>Social Services</option>
                        <option>Peace & Order</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Est. Budget (₱)</label>
                    <input type="number" name="budget" class="form-control" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date (Optional)</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeModal('proposalModal')" class="btn-secondary" style="margin-right:10px;">Cancel</button>
                <button type="submit" class="btn-primary">Submit Proposal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: UPDATE PROGRESS --}}
<div id="updateModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-edit" style="color: #2B5CE6;"></i> Update Progress</h3>
        </div>
        <form id="updateForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Completion Percentage (%)</label>
                <input type="range" name="progress" id="progressRange" min="0" max="100" class="form-control" oninput="document.getElementById('progVal').innerText = this.value + '%'">
                <div style="text-align:center; font-weight:700; font-size:1.5rem; color:#2B5CE6; margin-top:10px;" id="progVal">0%</div>
            </div>
            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeModal('updateModal')" class="btn-secondary" style="margin-right:10px;">Cancel</button>
                <button type="submit" class="btn-primary">Save Update</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: ADD EXPENSE --}}
<div id="expenseModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-coins" style="color: #10B981;"></i> Add Project Expense</h3>
        </div>
        <p style="font-size:0.9rem; color:#666; margin-top:-10px; margin-bottom:20px;">Expense for: <strong id="expProjectTitle"></strong></p>
        
        <form action="{{ route('kagawad.projects.expense') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" id="expProjectId">
            
            <div class="form-group">
                <label class="form-label">Expense Title / Item</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Cement 50 bags">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label class="form-label">Amount (₱)</label>
                    <input type="number" name="amount" class="form-control" required step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Date Spent</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option>Materials</option>
                    <option>Labor</option>
                    <option>Equipment Rental</option>
                    <option>Logistics</option>
                    <option>Others</option>
                </select>
            </div>

            <div style="text-align:right; margin-top: 20px;">
                <button type="button" onclick="closeModal('expenseModal')" class="btn-secondary" style="margin-right:10px;">Cancel</button>
                <button type="submit" class="btn-primary" style="background:#10B981;">Submit Expense</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    function openUpdateModal(id, currentProgress) {
        let form = document.getElementById('updateForm');
        form.action = "/kagawad/projects/" + id + "/progress"; // Ensure route matches web.php
        document.getElementById('progressRange').value = currentProgress;
        document.getElementById('progVal').innerText = currentProgress + '%';
        openModal('updateModal');
    }

    function openExpenseModal(id, title) {
        document.getElementById('expProjectId').value = id;
        document.getElementById('expProjectTitle').innerText = title;
        openModal('expenseModal');
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>
@endsection