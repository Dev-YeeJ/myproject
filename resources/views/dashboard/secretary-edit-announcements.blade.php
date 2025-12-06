@extends('layouts.dashboard-layout')

@section('title', 'Edit Announcement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-gray-800 fw-bold">Edit Announcement</h2>
        <a href="{{ route('secretary.announcements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <form action="{{ route('secretary.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="audience" class="form-label fw-bold">Target Audience</label>
                    <select class="form-select @error('audience') is-invalid @enderror" id="audience" name="audience" required>
                        <option value="All" {{ old('audience', $announcement->audience) == 'All' ? 'selected' : '' }}>All (Everyone)</option>
                        <option value="Residents" {{ old('audience', $announcement->audience) == 'Residents' ? 'selected' : '' }}>Residents Only</option>
                        <option value="Barangay Officials" {{ old('audience', $announcement->audience) == 'Barangay Officials' ? 'selected' : '' }}>Barangay Officials</option>
                        <option value="SK Officials" {{ old('audience', $announcement->audience) == 'SK Officials' ? 'selected' : '' }}>SK Officials Only</option>
                    </select>
                    @error('audience')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Update Image</label>
                    <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    @if($announcement->image_path)
                        <div class="mt-3 p-2 border rounded bg-light" style="width: fit-content;">
                            <p class="small text-muted mb-1">Current Image:</p>
                            <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Current" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold" for="is_published">Published</label>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="fas fa-save me-2"></i> Update Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection