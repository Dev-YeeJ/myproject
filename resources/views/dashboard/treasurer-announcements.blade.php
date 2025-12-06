@extends('layouts.dashboard-layout')

@section('title', 'Announcements')

@section('nav-items')
    {{-- Reusing the nav items we defined previously --}}
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
        <a href="{{ route('treasurer.announcements.index') }}" class="nav-link active">
            <i class="fas fa-bell"></i>
            <span>Announcements</span>
        </a>
    </li>
@endsection

@section('content')
<style>
    .header-section {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white; padding: 30px; border-radius: 16px; margin-bottom: 30px;
    }
    .search-box {
        background: white; padding: 10px 20px; border-radius: 30px; display: flex; align-items: center; width: 100%; max-width: 400px;
    }
    .search-box input { border: none; outline: none; width: 100%; margin-left: 10px; }
    
    .announcement-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
    }
    .announcement-card {
        background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: transform 0.2s;
        display: flex; flex-direction: column; height: 100%;
    }
    .announcement-card:hover { transform: translateY(-5px); }
    .card-img { height: 160px; background: #eee; object-fit: cover; width: 100%; }
    .card-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-date { color: #059669; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
    .card-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; color: #1F2937; }
    .card-text { color: #6B7280; font-size: 0.9rem; line-height: 1.5; margin-bottom: 15px; flex-grow: 1; }
    .card-footer { border-top: 1px solid #F3F4F6; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #9CA3AF; }

    @media (max-width: 992px) { .announcement-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .announcement-grid { grid-template-columns: 1fr; } }
</style>

<div class="header-section">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
        <div>
            <h2 style="margin:0; font-size:1.8rem;">Announcements</h2>
            <p style="margin:5px 0 0 0; opacity:0.9;">Latest updates and news for the Barangay</p>
        </div>
        <form action="{{ route('treasurer.announcements.index') }}" method="GET">
            <div class="search-box">
                <i class="fas fa-search" style="color:#9CA3AF"></i>
                <input type="text" name="search" placeholder="Search announcements..." value="{{ request('search') }}">
            </div>
        </form>
    </div>
</div>

<div class="announcement-grid">
    @forelse($announcements as $announcement)
    <div class="announcement-card">
        @if($announcement->image_path)
            <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Announcement Image" class="card-img">
        @else
            <div class="card-img" style="display:flex; align-items:center; justify-content:center; background:#F3F4F6; color:#CBD5E1;">
                <i class="fas fa-image fa-3x"></i>
            </div>
        @endif
        
        <div class="card-body">
            <div class="card-date">
                <i class="far fa-calendar-alt"></i> {{ $announcement->created_at->format('M d, Y') }}
            </div>
            <h3 class="card-title">{{ $announcement->title }}</h3>
            <div class="card-text">
                {{ Str::limit($announcement->content, 120) }}
            </div>
            <div class="card-footer">
                <span><i class="fas fa-user-circle"></i> {{ $announcement->user->first_name ?? 'Admin' }}</span>
                <span>{{ $announcement->created_at->diffForHumans() }}</span>   
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #6B7280;">
        <i class="fas fa-bullhorn fa-3x" style="margin-bottom:15px; opacity:0.5;"></i>
        <p>No announcements found.</p>
    </div>
    @endforelse
</div>

<div style="margin-top: 30px;">
    {{ $announcements->links() }}
</div>
@endsection