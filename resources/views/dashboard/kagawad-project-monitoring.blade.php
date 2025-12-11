@extends('layouts.dashboard-layout')

@section('title', 'Project Monitoring')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('kagawad.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i> <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.residents') }}" class="nav-link">
            <i class="fas fa-users"></i> <span>Resident Profiling</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('kagawad.projects') }}" class="nav-link active">
            <i class="fas fa-tasks"></i> <span>Project Monitoring</span>
        </a>
    </li>
    {{-- Other nav items... --}}
@endsection

@section('content')
<style>
    /* Styling consistent with your theme */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .header-content h2 { margin: 0; font-weight: 700; font-size: 1.8rem; }
    .header-content p { margin: 5px 0 0; opacity: 0.9; }
    
    .btn-propose {
        background: #FFA500; color: white; border: none; padding: 12px 24px;
        border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center; gap: 8px; transition: transform 0.2s;
    }
    .btn-propose:hover { background: #e59400; transform: translateY(-2px); color: white;}

    /* Tab Navigation */
    .nav-tabs { border-bottom: 2px solid #E5E7EB; margin-bottom: 20px; display: flex; gap: 20px; }
    .nav-tab-item {
        padding: 10px 5px; color: #6B7280; text-decoration: none;
        font-weight: 600; border-bottom: 3px solid transparent; cursor: pointer;
    }
    .nav-tab-item.active { color: #2B5CE6; border-bottom-color: #2B5CE6; }
    .nav-tab-item:hover { color: #2B5CE6; }

    /* Project Cards */
    .project-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .project-card {
        background: white; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); position: relative;
        display: flex; flex-direction: column; height: 100%;
    }
    .badge-status {
        position: absolute; top: 20px; right: 20px; padding: 4px 12px;
        border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    }
    .status-proposed { background: #FEF3C7; color: #92400E; }
    .status-progress { background: #DBEAFE; color: #1E40AF; }
    .status-completed { background: #D1FAE5; color: #065F46; }

    .project-title { font-size: 1.1rem; font-weight: 700; color: #1F2937; margin-bottom: 5px; }
    .project-cat { font-size: 0.85rem; color: #6B7280; margin-bottom: 15px; }
    
    .progress-bar-bg { height: 8px; background: #F3F4F6; border-radius: 4px; margin: 15px 0 5px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: #2B5CE6; border-radius: 4px; }
    
    .project-stats { display: flex; justify-content: space-between; font-size: 0.85rem; color: #4B5563; margin-top: auto; padding-top: 15px; border-top: 1px solid #F3F4F6; }
    
    .card-actions { margin-top: 15px; display: flex; gap: 10px; }
    .btn-card { flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #E5E7EB; background: white; cursor: pointer; font-size: 0.85rem; font-weight: 600; text-align: center; color: #374151; }
    .btn-card:hover { background: #F9FAFB; border-color: #D1D5DB; }
    .btn-primary-outline { border-color: #2B5CE6; color: #2B5CE6; }
    .btn-primary-outline:hover { background: #EFF6FF; }

    /* Modal */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
    .modal-content { background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #D1D5DB; border-radius: 8px; }

    @media(max-width: 900px) { .project-grid { grid-template-columns: 1fr 1fr; } }
    @media(max-width: 600px) { .project-grid { grid-template-columns: 1fr; } .header-section { flex-direction: column; align-items: flex-start; gap: 15px; } }
</style>

{{-- Notifications --}}
@if(session('success'))
<div style="background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #6EE7B7;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="header-section">
    <div class="header-content">
        <h2>Project Monitoring</h2>
        <p>Track progress, manage expenses, and submit proposals.</p>
    </div>
    <button onclick="openModal('proposalModal')" class="btn-propose">
        <i class="fas fa-plus-circle"></i> Propose Project
    </button>
</div>

{{-- Tabs --}}
<div class="nav-tabs">
    <a href="{{ route('kagawad.projects', ['view' => 'active']) }}" class="nav-tab-item {{ $view == 'active' ? 'active' : '' }}">
        Active Projects <span style="background: #EFF6FF; color: #2B5CE6; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;">{{ $stats['total_active'] }}</span>
    </a>
    <a href="{{ route('kagawad.projects', ['view' => 'proposals']) }}" class="nav-tab-item {{ $view == 'proposals' ? 'active' : '' }}">
        Proposals <span style="background: #FEF3C7; color: #92400E; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;">{{ $stats['my_proposals'] }}</span>
    </a>
</div>

{{-- Content Grid --}}
<div class="project-grid">
    @php $projects = ($view == 'active') ? $activeProjects : $proposals; @endphp

    @forelse($projects as $project)
    <div class="project-card">
        {{-- Status Badge --}}
        @if($project->status == 'Proposed')
            <span class="badge-status status-proposed">Proposed</span>
        @elseif($project->status == 'In Progress')
            <span class="badge-status status-progress">Active</span>
        @else
            <span class="badge-status status-completed">{{ $project->status }}</span>
        @endif

        <div class="project-title">{{ $project->title }}</div>
        <div class="project-cat"><i class="fas fa-tag"></i> {{ $project->category }}</div>

        <div style="font-size: 0.9rem; color: #4B5563; margin-bottom: 10px; flex-grow: 1;">
            {{ Str::limit($project->description, 80) }}
        </div>

        {{-- Progress Bar (Only for Active) --}}
        @if($view == 'active')
            <div style="display:flex; justify-content:space-between; font-size:0.8rem; font-weight:600;">
                <span>Progress</span>
                <span>{{ $project->progress }}%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $project->progress }}%;"></div>
            </div>
        @else
            <div style="margin: 15px 0; font-size: 0.85rem; color: #6B7280; font-style: italic;">
                <i class="fas fa-clock"></i> Waiting for Captain approval
            </div>
        @endif

        <div class="project-stats">
            <div><i class="fas fa-coins"></i> Budget: ₱{{ number_format($project->budget) }}</div>
            @if($view == 'active')
            <div>Spent: ₱{{ number_format($project->amount_spent) }}</div>
            @endif
        </div>

        {{-- Actions --}}
        @if($view == 'active' && $project->status != 'Completed')
        <div class="card-actions">
            <button class="btn-card" onclick="openUpdateModal({{ $project->id }}, {{ $project->progress }})">
                <i class="fas fa-sliders-h"></i> Update
            </button>
            <button class="btn-card btn-primary-outline" onclick="openExpenseModal({{ $project->id }}, '{{ $project->title }}')">
                <i class="fas fa-receipt"></i> Add Expense
            </button>
        </div>
        @endif
    </div>
    @empty
    <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #6B7280;">
        <i class="fas fa-folder-open fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
        <p>No projects found in this category.</p>
    </div>
    @endforelse
</div>

<div style="margin-top: 20px;">
    {{ $projects->appends(['view' => $view])->links('pagination::bootstrap-4') }}
</div>

{{-- MODAL 1: PROPOSE PROJECT --}}
<div id="proposalModal" class="modal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; margin-bottom: 20px;">
            <h3 style="margin:0;">Propose New Project</h3>
            <span onclick="closeModal('proposalModal')" style="cursor:pointer; font-size:1.5rem;">&times;</span>
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
            <button type="submit" class="btn-propose" style="width:100%; justify-content:center;">Submit Proposal</button>
        </form>
    </div>
</div>

{{-- MODAL 2: UPDATE PROGRESS --}}
<div id="updateModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <h3 style="margin-top:0;">Update Progress</h3>
        <form id="updateForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Completion Percentage (%)</label>
                <input type="range" name="progress" id="progressRange" min="0" max="100" class="form-control" oninput="document.getElementById('progVal').innerText = this.value + '%'">
                <div style="text-align:center; font-weight:700; font-size:1.5rem; color:#2B5CE6; margin-top:10px;" id="progVal">0%</div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('updateModal')" class="btn-card">Cancel</button>
                <button type="submit" class="btn-propose" style="background:#2B5CE6; border:none;">Save Update</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: ADD EXPENSE --}}
<div id="expenseModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">Add Project Expense</h3>
        <p style="font-size:0.9rem; color:#666; margin-bottom:20px;">Expense for: <strong id="expProjectTitle"></strong></p>
        
        <form action="{{ route('kagawad.projects.expense') }}" method="POST" enctype="multipart/form-data">
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

            {{-- Optional: If you want proof --}}
            {{-- <div class="form-group">
                <label class="form-label">Receipt Photo (Optional)</label>
                <input type="file" name="proof_image" class="form-control">
            </div> --}}

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="button" onclick="closeModal('expenseModal')" class="btn-card">Cancel</button>
                <button type="submit" class="btn-propose" style="background:#10B981; border:none;">Submit Expense</button>
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

    function openUpdateModal(id, currentProgress) {
        let form = document.getElementById('updateForm');
        form.action = "/kagawad/projects/" + id + "/progress"; // Adjust route as needed
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