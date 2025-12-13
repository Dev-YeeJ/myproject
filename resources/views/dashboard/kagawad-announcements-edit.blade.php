@extends('layouts.dashboard-layout')

@section('title', 'Edit Announcement')

@section('nav-items')
    {{-- COMPLETE KAGAWAD NAVIGATION --}}
    <li class="nav-item">
        <a href="{{ route('kagawad.dashboard') }}" class="nav-link ">
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
        <a href="{{ route('kagawad.projects') }}" class="nav-link">
            <i class="fas fa-tasks"></i>
            <span>Project Monitoring</span>
        </a>
    </li>
    <li class="nav-item active">
        <a href="{{ route('kagawad.incidents') }}" class="nav-link">
            <i class="fas fa-gavel"></i>
            <span>Incident & Blotter</span>
        </a>
    </li>
    <li class="nav-item">
        <a href={{ route('kagawad.announcements.index') }} class="nav-link active">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
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
        border: 2px dashed #D1D5DB; border-radius: 10px; padding: 20px;
        text-align: center; cursor: pointer; transition: all 0.2s; background: #F9FAFB;
        position: relative;
    }
    .image-upload-box:hover { border-color: #2B5CE6; background: #EFF6FF; }
    .upload-icon { font-size: 1.5rem; color: #9CA3AF; margin-bottom: 5px; }
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
        <div class="profiling-title">Edit Announcement</div>
        <div class="profiling-subtitle">Update your post details.</div>
    </div>
    <a href="{{ route('kagawad.announcements.index') }}" class="btn-back">
        <i class="fas fa-arrow-left"></i> Cancel
    </a>
</div>

<form action="{{ route('kagawad.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- LEFT COLUMN: Main Content --}}
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="mb-4">
                    <label class="form-label">Announcement Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg fw-bold" value="{{ old('title', $announcement->title) }}" required>
                    @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Content / Details <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="12" style="line-height: 1.6;" required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Sidebar Settings --}}
        <div class="col-lg-4">
            
            {{-- Publish Card --}}
            <div class="sidebar-card">
                <div class="section-title">Status & Action</div>
                
                <div class="form-check form-switch mb-3 ps-5">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }} style="width: 3em; height: 1.5em; margin-left: -3.5em;">
                    <label class="form-check-label fw-bold ms-1 pt-1" for="is_published">Published</label>
                </div>
                
                <p class="small text-muted mb-4">
                    Uncheck to revert this post to a <strong>Draft</strong>.
                </p>

                <button type="submit" class="btn-publish">
                    <i class="fas fa-save"></i> Update Changes
                </button>
            </div>

            {{-- Settings Card --}}
            <div class="sidebar-card">
                <div class="section-title">Settings & Media</div>

                {{-- Audience (LOCKED FOR KAGAWAD) --}}
                <div class="mb-4">
                    <label class="form-label">Target Audience</label>
                    <div class="p-3 bg-light border rounded d-flex align-items-center">
                        <i class="fas fa-lock text-muted me-2"></i>
                        <span class="fw-bold text-dark">Residents (Fixed)</span>
                    </div>
                </div>

                {{-- Current Image Display --}}
                @if($announcement->image_path)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Current Image:</label>
                        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Current" class="img-fluid rounded border w-100 mb-2">
                    </div>
                @endif

                {{-- Image Upload --}}
                <div class="mb-3">
                    <label class="form-label">Update Image</label>
                    <div class="image-upload-box" onclick="document.getElementById('imageInput').click()">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Click to replace image</div>
                        <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                    {{-- Preview Container --}}
                    <div id="imagePreview" class="mt-3 d-none">
                        <p class="small text-success fw-bold mb-1">New Selection:</p>
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