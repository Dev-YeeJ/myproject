@extends('layouts.dashboard-layout')

@section('title', 'Announcements')

@section('content')
<style>
    /* Resident-specific green theme for header */
    .header-section {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%); 
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
    <div class="header-title">Barangay Announcements</div>
    <div class="header-subtitle">Latest news, updates, and events in Brgy. Calbueg</div>
</div>

<div class="container-fluid px-0">
    {{-- Search Bar --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('resident.announcements.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-success text-white">Search</button>
            </form>
        </div>
    </div>

    <div class="row">
        @forelse($announcements as $announcement)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 position-relative">
                {{-- Image Display --}}
                @if($announcement->image_path)
                    <img src="{{ asset('storage/' . $announcement->image_path) }}" class="card-img-top" alt="Announcement Image" style="height: 200px; object-fit: cover;">
                @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-bullhorn text-muted fa-3x"></i>
                    </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title font-weight-bold text-dark">{{ $announcement->title }}</h5>
                        {{-- Date Badge --}}
                        <span class="badge bg-success bg-opacity-10 text-success">
                            {{ $announcement->created_at->format('M d') }}
                        </span>
                    </div>
                    
                    {{-- Meta Info --}}
                    <p class="card-text text-muted small mb-3">
                        <i class="far fa-clock me-1"></i> Posted {{ $announcement->created_at->diffForHumans() }}
                        <span class="mx-1">•</span> 
                        {{-- Display Author Role safely --}}
                        by {{ $announcement->user->role == 'barangay_captain' ? 'Captain' : 'Secretary' }}
                    </p>
                    
                    {{-- Content --}}
                    <p class="card-text text-secondary">
                        {{ $announcement->content }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="fas fa-folder-open fa-3x mb-3"></i>
                <p>No announcements found.</p>
                <p class="small">Check back later for updates from the Barangay.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection