{{-- resources/views/dashboards/resident-document-create.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Request a Document')

@section('nav-items')
    {{-- This is the RESIDENT navigation --}}
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        {{-- This is the active page's parent --}}
        <a href="{{ route('resident.document-services') }}" class="nav-link active">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    {{-- ... other nav items ... --}}
@endsection

@section('content')
<style>
    /* Styles for the form container */
    .form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.07);
        padding: 30px;
    }
    .form-header {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .form-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    .form-header p {
        font-size: 0.95rem;
        color: #6B7280;
        margin-top: 8px;
        margin-bottom: 0;
    }
    /* --- NEW --- */
    .file-input-info {
        font-size: 0.85rem;
        color: #6B7280;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="form-container">
            <div class="form-header">
                <h3>New Document Request</h3>
                <p>Fill out the details below to submit your request.</p>
            </div>

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- !!! UPDATED FORM TAG !!! --}}
            <form action="{{ route('resident.document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    
                    {{-- Document Type --}}
                    <div class="col-12">
                        <label for="document_type_id" class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('document_type_id') is-invalid @enderror" id="document_type_id" name="document_type_id" required>
                            <option value="" disabled {{ !$selectedType ? 'selected' : '' }}>Select a document...</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('document_type_id', $selectedType) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }} {{ $type->price > 0 ? '(₱' . number_format($type->price, 0) . ')' : '(Free)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Purpose --}}
                    <div class="col-12">
                        <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" rows="3" placeholder="e.g., For job application, For school requirement, etc." required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- !!! NEW FILE UPLOAD FIELD !!! --}}
                    <div class="col-12">
                        <label for="requirements" class="form-label">Upload Requirements (Optional)</label>
                        <input type="file" class="form-control @error('requirements.*') is-invalid @enderror" id="requirements" name="requirements[]" multiple>
                        <p class="file-input-info mt-1">You can upload multiple files (PDF, JPG, PNG). Max 2MB each.</p>
                        
                        @error('requirements.*')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    {{-- Form Actions --}}
                    <div class="col-12 text-end mt-4">
                        <a href="{{ route('resident.document-services') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>
@endsection