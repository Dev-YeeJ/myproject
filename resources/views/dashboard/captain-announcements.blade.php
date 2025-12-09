@extends('layouts.dashboard-layout')

@section('title', 'Announcements')

@section('nav-items')
    {{-- Active class on Dashboard link --}}
    <li class="nav-item">
        <a href="{{ route('captain.dashboard') }}" class="nav-link ">
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
        {{-- UPDATED: Link to the new document services route --}}
        <a href="{{ route('captain.document-services') }}" class="nav-link">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('captain.financial') }}" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- UPDATED: Link to the new health services route --}}
        <a href="{{ route('captain.health-services') }}" class="nav-link ">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-exclamation-triangle"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link active"> {{-- Add route later --}}
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-check-circle"></i>
            <span>SK Module</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link"> {{-- Add route later --}}
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* Reuse header sizing from captain-resident-profiling */
    .header-section {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        padding: 40px; 
    }
    .header-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .header-subtitle {
        opacity: 0.95;
        font-size: 1rem;
    }
</style>

<div class="header-section">
    <div class="header-title">Announcements</div>
    <div class="header-subtitle">Manage barangay news and updates</div>
    <div style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%);">
        <a href="{{ route('captain.announcements.create') }}" class="btn btn-light text-primary fw-bold px-4 py-2">
            <i class="fas fa-plus me-2"></i>Create New
        </a>
    </div>
</div>

<div class="container-fluid px-0">
    {{-- Search --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('captain.announcements.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($announcements as $announcement)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 position-relative">
                @if($announcement->image_path)
                    <img src="{{ asset('storage/' . $announcement->image_path) }}" class="card-img-top" alt="Announcement Image" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-bullhorn text-muted fa-3x"></i>
                    </div>
                @endif
                
                {{-- Badges Container (Absolute Positioned) --}}
                <div style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; gap: 5px; align-items: flex-end;">
                    {{-- Status Badge --}}
                    <span class="badge {{ $announcement->is_published ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $announcement->is_published ? 'Published' : 'Draft' }}
                    </span>
                    
                    {{-- Audience Badge Logic --}}
                    @php
                        $badgeClass = 'bg-secondary';
                        $iconClass = 'fa-users';
                        
                        switch($announcement->audience) {
                            case 'All': 
                                $badgeClass = 'bg-primary'; 
                                $iconClass = 'fa-globe';
                                break;
                            case 'Residents': 
                                $badgeClass = 'bg-info text-dark'; 
                                $iconClass = 'fa-home';
                                break;
                            case 'Barangay Officials': 
                                $badgeClass = 'bg-danger'; 
                                $iconClass = 'fa-user-tie';
                                break;
                            case 'SK Officials': 
                                $badgeClass = 'bg-warning text-dark'; 
                                $iconClass = 'fa-running'; // or fa-basketball-ball
                                break;
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }}">
                        <i class="fas {{ $iconClass }} me-1"></i> {{ $announcement->audience }}
                    </span>
                </div>

                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-dark mb-2">{{ $announcement->title }}</h5>
                    <p class="card-text text-muted small mb-2">
                        <i class="far fa-clock me-1"></i> {{ $announcement->created_at->format('M d, Y h:i A') }}
                    </p>
                    <p class="card-text text-secondary">
                        {{ Str::limit($announcement->content, 120) }}
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-end gap-2 pb-3">
                    <a href="{{ route('captain.announcements.edit', $announcement->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('captain.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-folder-open fa-3x mb-3"></i>
                <p>No announcements found.</p>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection