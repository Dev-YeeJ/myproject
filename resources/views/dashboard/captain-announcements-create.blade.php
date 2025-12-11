@extends('layouts.dashboard-layout')

@section('title', 'Create Announcement')

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
    <a href="{{ route('captain.financial') }}" class="nav-link {{ request()->routeIs('captain.financial*') ? 'active' : '' }}">
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
    <a href="{{ route('captain.incident.index') }}" class="nav-link {{ request()->routeIs('captain.incident.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident & Blotter</span>
    </a>
</li>
    <li class="nav-item">
        <a href="{{ route('captain.project.monitoring') }}" class="nav-link "> {{-- Add route later --}}
            <i class="fas fa-flag"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('captain.announcements.index') }}" class="nav-link active {{ request()->routeIs('captain.announcements.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span>Announcements</span>
    </a>
</li>
   <li class="nav-item">
        {{-- Use the new captain.sk.overview route --}}
        <a href="{{ route('captain.sk.overview') }}" class="nav-link {{ request()->routeIs('captain.sk.overview') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i>
            <span>SK Module</span>
        </a>
    </li>
   
@endsection


@section('content')
<style>
    /* --- HEADER STYLES --- */
    .profiling-header {
        background: linear-gradient(135deg, #2B5CE6 0%, #1E3A8A 100%);
        color: white; padding: 35px 40px; border-radius: 16px; margin-bottom: 30px;
        position: relative; box-shadow: 0 4px 20px rgba(30, 58, 138, 0.2);
    }
    .profiling-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 5px; }
    .profiling-subtitle { opacity: 0.9; font-size: 0.95rem; }

    /* --- LAYOUT & CARDS --- */
    .content-card {
        background: white; border-radius: 12px; padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #E5E7EB;
        height: 100%;
    }
    
    .sidebar-card {
        background: white; border-radius: 12px; padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid #E5E7EB;
        margin-bottom: 20px;
    }

    /* --- FORM ELEMENTS --- */
    .form-label { font-weight: 700; color: #374151; font-size: 0.9rem; margin-bottom: 8px; }
    .form-control, .form-select {
        padding: 12px 15px; border-radius: 8px; border: 1px solid #D1D5DB;
        font-size: 0.95rem; transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2B5CE6; box-shadow: 0 0 0 3px rgba(43, 92, 230, 0.1); outline: none;
    }
    
    /* --- UPLOAD AREA --- */
    .image-upload-box {
        border: 2px dashed #D1D5DB; border-radius: 10px; padding: 30px;
        text-align: center; cursor: pointer; transition: all 0.2s; background: #F9FAFB;
        position: relative;
    }
    .image-upload-box:hover { border-color: #2B5CE6; background: #EFF6FF; }
    .upload-icon { font-size: 2rem; color: #9CA3AF; margin-bottom: 10px; }
    .upload-text { font-size: 0.85rem; color: #6B7280; font-weight: 500; }
    
    /* --- BUTTONS --- */
    .btn-back {
        position: absolute; top: 35px; right: 40px;
        background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3);
        padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none;
        display: flex; align-items: center; gap: 8px; transition: all 0.2s;
    }
    .btn-back:hover { background: white; color: #2B5CE6; }

    .btn-publish {
        background: #2B5CE6; color: white; width: 100%; border: none; padding: 12px;
        border-radius: 8px; font-weight: 700; font-size: 1rem;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background 0.2s;
    }
    .btn-publish:hover { background: #1E3A8A; color: white; }

    .section-title {
        font-size: 1rem; font-weight: 700; color: #111827; 
        border-bottom: 1px solid #E5E7EB; padding-bottom: 15px; margin-bottom: 20px;
    }
</style>

{{-- Header --}}
<div class="profiling-header">
    <div>
        <div class="profiling-title">Create Announcement</div>
        <div class="profiling-subtitle">Compose updates for barangay residents and officials.</div>
    </div>
    <a href="{{ route('captain.announcements.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('captain.announcements.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        {{-- LEFT COLUMN: Main Content --}}
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="mb-4">
                    <label class="form-label">Announcement Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg fw-bold" placeholder="Enter a catchy headline..." value="{{ old('title') }}" required>
                    @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Content / Details <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" placeholder="Write the full announcement details here..." style="line-height: 1.6;" required>{{ old('content') }}</textarea>
                    @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Sidebar Settings --}}
        <div class="col-lg-4">
            
            {{-- Publish Card --}}
            <div class="sidebar-card">
                <div class="section-title">Publishing</div>
                
                <div class="form-check form-switch mb-3 ps-5">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked style="width: 3em; height: 1.5em; margin-left: -3.5em;">
                    <label class="form-check-label fw-bold ms-1 pt-1" for="is_published">Publish Immediately</label>
                </div>
                
                <p class="small text-muted mb-4">
                    If unchecked, this post will be saved as a <strong>Draft</strong> and hidden from the public.
                </p>

                <button type="submit" class="btn-publish">
                    <i class="fas fa-paper-plane"></i> Post Announcement
                </button>
            </div>

            {{-- Settings Card --}}
            <div class="sidebar-card">
                <div class="section-title">Settings & Media</div>

                {{-- Audience --}}
                <div class="mb-4">
                    <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                    <select name="audience" class="form-select">
                        <option value="All">All (Everyone)</option>
                        <option value="Residents">Residents Only</option>
                        <option value="Barangay Officials">Barangay Officials</option>
                        <option value="SK Officials">SK Officials Only</option>
                    </select>
                    <div class="form-text small">Who can see this post?</div>
                </div>

                {{-- Image Upload --}}
                <div class="mb-3">
                    <label class="form-label">Cover Image</label>
                    <div class="image-upload-box" onclick="document.getElementById('imageInput').click()">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Click to upload image</div>
                        <div class="small text-muted mt-1">(JPG, PNG max 5MB)</div>
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                    {{-- Preview Container --}}
                    <div id="imagePreview" class="mt-3 d-none">
                        <img src="" alt="Preview" class="img-fluid rounded border w-100">
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('imagePreview');
                var img = preview.querySelector('img');
                img.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection