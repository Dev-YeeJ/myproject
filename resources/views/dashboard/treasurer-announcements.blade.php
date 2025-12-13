@extends('layouts.dashboard-layout')

@section('title', 'Announcements Management')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('treasurer.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('treasurer.financial') }}" class="nav-link">
            <i class="fas fa-dollar-sign"></i>
            <span>Financial Management</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- ACTIVE PAGE --}}
        <a href="{{ route('treasurer.announcements.index') }}" class="nav-link active">
            <i class="fas fa-bell"></i>
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
    }
    .profiling-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .profiling-subtitle { opacity: 0.95; font-size: 1rem; margin-bottom: 15px; }
    
    .barangay-badge {
        display: inline-flex; align-items: center; gap: 10px;
        background: rgba(255, 165, 0, 0.2); padding: 8px 16px;
        border-radius: 8px; font-weight: 600;
    }
    .barangay-badge .badge-icon {
        background: #FFA500; width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: white;
    }

    /* --- STATS BOXES (Matched) --- */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-box {
        background: white; padding: 24px; border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
    }
    .stat-content h3 { font-size: 2.5rem; font-weight: 700; margin: 0 0 8px 0; }
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
    .icon-purple-bg { background: #A855F7; } .stat-badge.purple { color: #A855F7; }

    /* --- FILTER BAR --- */
    .filter-bar {
        background: white; border-radius: 12px; padding: 15px; margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; gap: 10px;
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

    /* --- ANNOUNCEMENT CARD --- */
    .announcement-card {
        border: none; border-radius: 12px; overflow: hidden; height: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;
        background: white;
    }
    .announcement-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    
    .card-img-container { height: 200px; overflow: hidden; position: relative; background: #F3F4F6; }
    .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .announcement-card:hover .card-img-top { transform: scale(1.05); }
    
    .card-badges { position: absolute; top: 12px; right: 12px; display: flex; flex-direction: column; gap: 6px; align-items: flex-end; }
    .custom-badge { 
        padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; 
        text-transform: uppercase; letter-spacing: 0.5px; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .badge-published { background: #D1FAE5; color: #065F46; }
    .badge-draft { background: #FEF3C7; color: #92400E; }
    .badge-audience { background: rgba(255,255,255,0.9); color: #1F2937; backdrop-filter: blur(4px); }

    .card-body { padding: 20px; }
    .card-title { font-size: 1.1rem; font-weight: 700; color: #1F2937; margin-bottom: 10px; line-height: 1.4; }
    .card-meta { font-size: 0.85rem; color: #6B7280; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .card-text { font-size: 0.95rem; color: #4B5563; line-height: 1.6; margin-bottom: 20px; }
    
    .no-results { text-align: center; padding: 60px; color: #9CA3AF; }
</style>

{{-- Header Section --}}
<div class="profiling-header">
    <div class="profiling-title">Announcements</div>
    <div class="profiling-subtitle">View barangay news, updates, and public advisories.</div>
    <div class="barangay-badge">
        <span class="badge-icon">PH</span>
        <span>Barangay Calbueg Information</span>
    </div>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $announcements->total() }}</h3>
            <p>Total Posts</p>
            <div class="stat-badge blue"><i class="fas fa-bullhorn"></i><span>All Time</span></div>
        </div>
        <div class="stat-box-icon icon-blue-bg"><i class="fas fa-newspaper"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            {{-- In a real app, calculate these counts in controller --}}
            <h3>{{ $announcements->where('is_published', true)->count() }}</h3>
            <p>Published</p>
            <div class="stat-badge green"><i class="fas fa-check-circle"></i><span>Active</span></div>
        </div>
        <div class="stat-box-icon icon-green-bg"><i class="fas fa-globe"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $announcements->where('is_published', false)->count() }}</h3>
            <p>Drafts</p>
            <div class="stat-badge orange"><i class="fas fa-pen-square"></i><span>Pending</span></div>
        </div>
        <div class="stat-box-icon icon-orange-bg"><i class="fas fa-edit"></i></div>
    </div>
    <div class="stat-box">
        <div class="stat-content">
            <h3>{{ $announcements->where('audience', 'Residents')->count() }}</h3>
            <p>For Residents</p>
            <div class="stat-badge purple"><i class="fas fa-users"></i><span>Public</span></div>
        </div>
        <div class="stat-box-icon icon-purple-bg"><i class="fas fa-home"></i></div>
    </div>
</div>

{{-- Filter Bar --}}
<form action="{{ route('treasurer.announcements.index') }}" method="GET" class="filter-bar">
    <input type="text" name="search" class="search-input" placeholder="🔍 Search announcements by title or content..." value="{{ $search ?? '' }}">
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
                    <span class="custom-badge {{ $announcement->is_published ? 'badge-published' : 'badge-draft' }}">
                        {{ $announcement->is_published ? 'Published' : 'Draft' }}
                    </span>
                    <span class="custom-badge badge-audience">
                        <i class="fas fa-eye me-1 text-primary"></i> {{ $announcement->audience }}
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <div class="card-meta">
                    <i class="far fa-clock"></i> {{ $announcement->created_at->format('M d, Y • h:i A') }}
                </div>
                <h5 class="card-title">{{ $announcement->title }}</h5>
                <p class="card-text">
                    {{ Str::limit($announcement->content, 120) }}
                </p>
            </div>
            {{-- Treasurer View Only - No Actions --}}
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="no-results">
            <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
            <p class="h5 text-secondary">No announcements found.</p>
            <p class="small">Try adjusting your search.</p>
        </div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-5">
    {{ $announcements->links('pagination::bootstrap-5') }}
</div>

@endsection