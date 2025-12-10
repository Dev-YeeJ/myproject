@extends('layouts.dashboard-layout')

@section('title', 'SK Oversight Module')

@section('nav-items')
    <li class="nav-item"><a href="{{ route('captain.dashboard') }}" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.resident-profiling') }}" class="nav-link"><i class="fas fa-users"></i> <span>Resident Profiling</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.document-services') }}" class="nav-link"><i class="far fa-file-alt"></i> <span>Documents Services</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.financial') }}" class="nav-link"><i class="fas fa-dollar-sign"></i> <span>Financial Management</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.health-services') }}" class="nav-link"><i class="fas fa-heart"></i> <span>Health & Social Services</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.incident.index') }}" class="nav-link"><i class="fas fa-exclamation-triangle"></i> <span>Incident & Blotter</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.project.monitoring') }}" class="nav-link"><i class="fas fa-flag"></i> <span>Project Monitoring</span></a></li>
    <li class="nav-item"><a href="{{ route('captain.announcements.index') }}" class="nav-link"><i class="fas fa-bell"></i> <span>Announcements</span></a></li>
    
    {{-- Active Link --}}
    <li class="nav-item">
        <a href="{{ route('captain.sk.overview') }}" class="nav-link active">
            <i class="fas fa-user-graduate"></i> <span>SK Module (Oversight)</span>
        </a>
    </li>
    
    <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
@endsection

@section('content')
<style>
    .sk-oversight-header { background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%); color: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; position: relative; overflow: hidden; }
    .card-icon-bg { position: absolute; right: 20px; top: 20px; font-size: 5rem; opacity: 0.1; transform: rotate(-15deg); color: white; }
</style>

<div class="sk-oversight-header">
    <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
        <div><h1 class="font-weight-bold">Sangguniang Kabataan Oversight</h1><p class="mb-0">Monitoring & Supervision Dashboard</p></div>
        <div class="text-right"><h2 class="font-weight-bold">{{ $youthStats['total_youth'] }}</h2><span>Total KK Members (15-30 y/o)</span></div>
    </div>
    <i class="fas fa-users card-icon-bg"></i>
</div>

<div class="row">
    {{-- Left: Budget --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold border-0 pt-4"><i class="fas fa-coins text-warning mr-2"></i> SK Fund Status</div>
            <div class="card-body">
                <div class="text-center mb-4">
                    {{-- FIXED KEY HERE --}}
                    <h3 class="font-weight-bold text-success">₱{{ number_format($budgetStats['available_cash'], 2) }}</h3>
                    <small class="text-muted text-uppercase font-weight-bold">Actual Cash Remaining</small>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1"><span>Total Allocation (10%)</span><span class="font-weight-bold">₱{{ number_format($budgetStats['allocation'], 2) }}</span></div>
                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-primary" style="width: 100%"></div></div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1"><span>Committed (Projects)</span><span class="text-info font-weight-bold">₱{{ number_format($budgetStats['committed'], 2) }}</span></div>
                    <div class="progress" style="height: 6px;">
                        @php $comPercent = ($budgetStats['allocation'] > 0) ? ($budgetStats['committed'] / $budgetStats['allocation']) * 100 : 0; @endphp
                        <div class="progress-bar bg-info" style="width: {{ $comPercent }}%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1"><span>Actual Spent</span><span class="text-danger font-weight-bold">₱{{ number_format($budgetStats['spent'], 2) }}</span></div>
                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-danger" style="width: {{ $budgetStats['utilization_rate'] }}%"></div></div>
                </div>
            </div>
        </div>

        {{-- Officials --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold border-0 pt-4"><i class="fas fa-user-tie text-primary mr-2"></i> Current Officials</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($officials as $official)
                    <div class="list-group-item border-0 d-flex align-items-center py-3">
                        <div class="mr-3"><div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-user text-secondary"></i></div></div>
                        <div><h6 class="mb-0 font-weight-bold">{{ $official->resident->first_name }} {{ $official->resident->last_name }}</h6><small class="text-danger font-weight-bold">{{ $official->position }}</small></div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">No officials recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Demographics & Projects --}}
    <div class="col-md-8">
        <div class="row mb-4">
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body text-center p-3"><i class="fas fa-vote-yea fa-2x text-primary mb-2"></i><h4 class="font-weight-bold mb-0">{{ $youthStats['registered_voters'] }}</h4><small class="text-muted">Registered Voters</small></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body text-center p-3"><i class="fas fa-user-slash fa-2x text-warning mb-2"></i><h4 class="font-weight-bold mb-0">{{ $youthStats['out_of_school'] }}</h4><small class="text-muted">Out-of-School / Unemployed</small></div></div></div>
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body text-center p-3"><i class="fas fa-graduation-cap fa-2x text-info mb-2"></i><h4 class="font-weight-bold mb-0">{{ $youthStats['students'] }}</h4><small class="text-muted">Students</small></div></div></div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold border-0 pt-4 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-project-diagram text-success mr-2"></i> Projects Monitoring</span>
                <span class="badge badge-pill badge-light">{{ $skProjects->count() }} Total</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light"><tr><th>Title</th><th>Budget</th><th>Spent</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($skProjects as $project)
                            <tr>
                                <td class="font-weight-bold">{{ $project->title }}</td>
                                <td>₱{{ number_format($project->budget, 2) }}</td>
                                <td class="{{ $project->amount_spent > $project->budget ? 'text-danger' : 'text-success' }}">₱{{ number_format($project->amount_spent, 2) }}</td>
                                <td><span class="badge {{ $project->status == 'Completed' ? 'badge-success' : 'badge-primary' }}">{{ $project->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No active projects.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection