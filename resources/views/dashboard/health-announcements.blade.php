{{-- resources/views/dashboard/health-announcements.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Health Announcements')

@section('nav-items')
    {{-- Health Worker Navigation --}}
    <li class="nav-item">
        <a href="{{ route('health.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a href="{{ route('health.health-services') }}" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Health & Social Services</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('health.announcements') }}" class="nav-link active">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    /* --- HEADER STYLES (Matched to Captain) --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        position: relative;
        box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
        border: 1px solid rgba(255, 165, 0, 0.3);
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }

    /* --- STATS BOXES (Matched to Captain) --- */
    .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
        transition: transform 0.2s;
    }
    .stat-box:hover { transform: translateY(-3px); }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; color: #1F2937; }
    .stat-content p { color: #666; margin: 0 0 8px 0; font-size: 0.95rem; }
    .stat-badge { font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
    
    .stat-box-icon {
        width: 70px; height: 70px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: white;
    }

    /* Colors */
    .icon-blue-bg { background: #2B5CE6; } .stat-badge.blue { color: #2B5CE6; }
    .icon-orange-bg { background: #FFA500; } .stat-badge.orange { color: #FF8C42; }
    .icon-green-bg { background: #10B981; } .stat-badge.green { color: #10B981; }

    /* --- FILTER BAR --- */
    .filter-bar {
        background: white; border-radius: 12px; padding: 15px; margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; gap: 10px;
        border: 1px solid #F3F4F6;
    }
    .search-input {
        flex: 1; padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 8px;
        font-size: 0.95rem; background: #F9FAFB; transition: border-color 0.3s;
    }
    .search-input:focus { outline: none; border-color: #2B5CE6; background: white; }
    .btn-search {
        background: #2B5CE6; color: white; border: none; padding: 0 24px;
        border-radius: 8px; font-weight: 600; transition: background 0.3s;
    }
    .btn-search:hover { background: #1E3A8A; }

    /* --- ANNOUNCEMENT CARD (Matched to Captain) --- */
    .announcement-card {
        border: none; border-radius: 12px; overflow: hidden; height: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;
        background: white; border: 1px solid #F3F4F6;
        display: flex; flex-direction: column;
    }
    .announcement-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08); }
    
    .card-img-container { height: 200px; overflow: hidden; position: relative; background: #F3F4F6; }
    .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .announcement-card:hover .card-img-top { transform: scale(1.05); }
    
    .card-badges { position: absolute; top: 12px; right: 12px; display: flex; flex-direction: column; gap: 6px; align-items: flex-end; }
    .custom-badge { 
        padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; 
        text-transform: uppercase; letter-spacing: 0.5px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .badge-audience { background: rgba(255,255,255,0.95); color: #2B5CE6; backdrop-filter: blur(4px); }

    .card-body { padding: 24px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-title { font-size: 1.15rem; font-weight: 700; color: #1F2937; margin-bottom: 10px; line-height: 1.4; }
    .card-meta { font-size: 0.85rem; color: #6B7280; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .card-text { font-size: 0.95rem; color: #4B5563; line-height: 1.6; margin-bottom: 20px; flex-grow: 1; }
    
    .card-actions { 
        padding: 16px 24px; background: #F9FAFB; border-top: 1px solid #F3F4F6; 
    }
    .btn-read-more {
        width: 100%;
        background: white; border: 1px solid #E5E7EB; color: #4B5563;
        padding: 10px; border-radius: 8px; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-read-more:hover { border-color: #2B5CE6; color: #2B5CE6; background: #EFF6FF; }

    .no-results { text-align: center; padding: 60px; color: #9CA3AF; }

    /* Modal Tweaks */
    .modal-header { background: #2B5CE6; color: white; border-bottom: none; }
    .modal-title { font-weight: 700; }
    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    .modal-body img { max-width: 100%; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .modal-meta-box { background: #F3F4F6; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2B5CE6; }
</style>

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Announcements</div>
    <div class="profiling-subtitle">Latest news, health advisories, and barangay updates.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg Information Board</span>
    </div>
</div>

{{-- Stats Row (Simplified for Viewer) --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $announcements->total() }}</h3>
            <p>Total Updates</p>
            <div class="stat-badge blue"><i class="fas fa-bullhorn"></i><span>Available</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-newspaper"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            {{-- Assuming controller filters for "All" and "Barangay Officials" --}}
            <h3>{{ $announcements->where('audience', 'Barangay Officials')->count() }}</h3>
            <p>Official Memos</p>
            <div class="stat-badge orange"><i class="fas fa-file-alt"></i><span>Internal</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-briefcase"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $announcements->where('audience', 'All')->count() }}</h3>
            <p>Public News</p>
            <div class="stat-badge green"><i class="fas fa-users"></i><span>General</span></div>
        </div>
        <div class="stat-box-icon icon-green-bg"><i class="fas fa-globe"></i></div>
    </div>
</div>

{{-- Filter Bar --}}
<form action="{{ route('health.announcements') }}" method="GET" class="filter-bar">
    <input type="text" name="search" class="search-input" placeholder="🔍 Search announcements..." value="{{ $search ?? '' }}">
    <button type="submit" class="btn-search">Search</button>
</form>

{{-- Grid Content --}}
<div class="row g-4">
    @forelse($announcements as $announcement)
    <div class="col-md-6 col-lg-4">
        <div class="announcement-card h-100">
            <div class="card-img-container">
                @if($announcement->image_path)
                    <img src="{{ asset('storage/' . $announcement->image_path) }}" class="card-img-top" alt="Announcement Image">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light text-secondary">
                        <i class="fas fa-image fa-3x opacity-25"></i>
                    </div>
                @endif
                
                {{-- Badges --}}
                <div class="card-badges">
                    <span class="custom-badge badge-audience">
                        <i class="fas fa-eye me-1"></i> {{ $announcement->audience }}
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="card-meta">
                    <span><i class="far fa-clock text-primary"></i> {{ $announcement->created_at->format('M d, Y') }}</span>
                    <span>•</span>
                    <span><i class="far fa-user text-primary"></i> {{ $announcement->user->name ?? 'Admin' }}</span>
                </div>
                <h5 class="card-title">{{ $announcement->title }}</h5>
                <p class="card-text">
                    {{ Str::limit(strip_tags($announcement->content), 100) }}
                </p>
            </div>
            
            <div class="card-actions">
                <button type="button" class="btn-read-more" data-bs-toggle="modal" data-bs-target="#modal-{{ $announcement->id }}">
                    <i class="fas fa-book-open me-2"></i> Read Full Details
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL FOR EACH ANNOUNCEMENT --}}
    <div class="modal fade" id="modal-{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $announcement->title }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($announcement->image_path)
                        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Full Image">
                    @endif
                    
                    <div class="modal-meta-box">
                        <div class="d-flex justify-content-between mb-2">
                            <strong><i class="far fa-calendar-alt me-2"></i> Posted:</strong> 
                            <span>{{ $announcement->created_at->format('F d, Y h:i A') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong><i class="far fa-user me-2"></i> Author:</strong> 
                            <span>{{ $announcement->user->name ?? 'Barangay Admin' }}</span>
                        </div>
                    </div>

                    <div class="mt-3" style="line-height: 1.8; color: #374151;">
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @empty
    <div class="col-12">
        <div class="no-results">
            <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
            <p class="h5 text-secondary">No announcements found.</p>
            <p class="small">Try changing your search keywords.</p>
        </div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5">
    {{ $announcements->links() }}
</div>

@endsection