@extends('layouts.dashboard-layout')

@section('title', 'Create Announcement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gray-800 fw-bold">Create Announcement</h2>
        <a href="{{ route('captain.announcements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <form action="{{ route('captain.announcements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Enter announcement title" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Audience Selection --}}
                <div class="mb-3">
                    <label for="audience" class="form-label fw-bold">Target Audience</label>
                    <select class="form-select @error('audience') is-invalid @enderror" id="audience" name="audience" required>
                        <option value="All" {{ old('audience') == 'All' ? 'selected' : '' }}>All (Everyone)</option>
                        <option value="Residents" {{ old('audience') == 'Residents' ? 'selected' : '' }}>Residents Only</option>
                        <option value="Barangay Officials" {{ old('audience') == 'Barangay Officials' ? 'selected' : '' }}>Barangay Officials (Sec, Treas, Kagawad)</option>
                        <option value="SK Officials" {{ old('audience') == 'SK Officials' ? 'selected' : '' }}>SK Officials Only</option>
                    </select>
                    <div class="form-text">Select who can see this announcement on their dashboard.</div>
                    @error('audience')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" placeholder="Enter full details..." required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Cover Image (Optional)</label>
                    <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                    <div class="form-text">Max size: 5MB. Formats: JPG, PNG.</div>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked>
                    <label class="form-check-label fw-bold" for="is_published">Publish Immediately</label>
                    <div class="form-text">Uncheck to save as Draft (hidden from everyone).</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="fas fa-paper-plane me-2"></i> Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection