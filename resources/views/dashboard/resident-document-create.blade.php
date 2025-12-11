{{-- resources/views/dashboards/resident-document-create.blade.php --}}

@extends('layouts.dashboard-layout')

@section('title', 'Request a Document')

@section('nav-items')
    <li class="nav-item">
        <a href="{{ route('resident.dashboard') }}" class="nav-link ">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('resident.document-services') }}" class="nav-link active">
            <i class="far fa-file-alt"></i>
            <span>Documents Services</span>
        </a>
    </li>
    <li class="nav-item">
    <a href="{{ route('resident.health-services') }}" class="nav-link">
        <i class="fas fa-heartbeat"></i>
        <span>Health Services</span>
    </a>
</li>

{{-- NEW LINK HERE --}}
<li class="nav-item">
    <a href="{{ route('resident.incidents.index') }}" class="nav-link {{ request()->routeIs('resident.incidents.*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Incident Reports</span>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('resident.announcements.index') }}" class="nav-link">
        <i class="fas fa-bullhorn"></i>
        <span>Announcements</span>
    </a>
</li>

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
    .file-input-info {
        font-size: 0.85rem;
        color: #6B7280;
    }
    
    /* Payment Specific Styles */
    .payment-option-card {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .payment-option-card:hover {
        border-color: #2B5CE6;
        background: #F9FAFB;
    }
    .form-check-input:checked + .form-check-label {
        color: #2B5CE6;
    }
    
    /* --- Styles matched from Captain View --- */
    .payment-header {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #2B5CE6; /* Blue matching the icon */
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F3F4F6;
    }
    .section-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6B7280; /* Gray-500 text-muted look */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }
    .amount-display {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1F2937;
        line-height: 1;
    }
    /* Dynamic Field Styling */
    .dynamic-field-group { 
        background: #F9FAFB; 
        padding: 20px; 
        border-radius: 12px; 
        border: 1px solid #E5E7EB; 
        margin-bottom: 25px; 
    }
    .dynamic-field-title { 
        font-size: 0.9rem; 
        font-weight: 700; 
        color: #2B5CE6; 
        margin-bottom: 15px; 
        text-transform: uppercase; 
        display: flex;
        align-items: center;
        gap: 8px;
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
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('resident.document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    
                    {{-- 1. Document Type --}}
                    <div class="col-12">
                        <label for="document_type_id" class="section-label text-dark" style="font-size: 0.8rem;">DOCUMENT TYPE <span class="text-danger">*</span></label>
                        <select class="form-select @error('document_type_id') is-invalid @enderror" 
                                id="document_select" 
                                name="document_type_id" 
                                required 
                                onchange="handleDocChange()">
                            {{-- Placeholder Option --}}
                            <option value="" data-price="0" data-fields="[]" disabled {{ $selectedType ? '' : 'selected' }}>Select a document...</option>
                            
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}" 
                                        data-price="{{ $type->price }}" 
                                        {{-- Encode fields to JSON for JS --}}
                                        data-fields="{{ json_encode($type->custom_fields ?? []) }}"
                                        {{-- Check if this option matches the URL parameter 'type_id' --}}
                                        {{ (string)$type->id === (string)old('document_type_id', $selectedType) ? 'selected' : '' }}>
                                    {{ $type->name }} {{ $type->price > 0 ? '(₱' . number_format($type->price, 0) . ')' : '(Free)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 2. Purpose --}}
                    <div class="col-12">
                        <label for="purpose" class="section-label text-dark" style="font-size: 0.8rem;">PURPOSE <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('purpose') is-invalid @enderror" id="purpose" name="purpose" rows="2" placeholder="e.g., For job application, For school requirement, etc." required>{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ================= DYNAMIC FIELDS SECTION ================= --}}
                    <div class="col-12" id="dynamic_fields_wrapper" style="display: none;">
                        <div class="dynamic-field-group">
                            <div class="dynamic-field-title">
                                <i class="fas fa-info-circle"></i> Additional Information Required
                            </div>
                            <div id="dynamic_fields_container" class="row g-3">
                                {{-- JS will inject inputs here (e.g., Business Name, Income) --}}
                            </div>
                        </div>
                    </div>
                    {{-- ========================================================== --}}

                    {{-- 3. Requirements Upload --}}
                    <div class="col-12">
                        <label for="requirements" class="section-label text-dark" style="font-size: 0.8rem;">UPLOAD REQUIREMENTS (OPTIONAL)</label>
                        <input type="file" class="form-control @error('requirements.*') is-invalid @enderror" id="requirements" name="requirements[]" multiple>
                        <p class="file-input-info mt-1">You can upload multiple files (PDF, JPG, PNG). Max 2MB each.</p>
                        
                        @error('requirements.*')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    {{-- ================= PAYMENT LOGIC SECTION ================= --}}
                    {{-- This entire div is hidden/shown via JS based on document price --}}
                    <div class="col-12" id="payment_section" style="display: none;">
                        
                        <div class="mt-4 pt-2">
                            <div class="payment-header">
                                <i class="fas fa-coins"></i> Payment Information
                            </div>
                            
                            <div class="row g-3 mb-4">
                                {{-- Amount Due Section --}}
                                <div class="col-12 mb-2">
                                    <label class="section-label">AMOUNT DUE</label>
                                    <div class="amount-display">₱<span id="display_price">0.00</span></div>
                                </div>

                                {{-- Payment Method Selection --}}
                                <div class="col-12">
                                    <label class="section-label mb-2">PAYMENT METHOD <span class="text-danger">*</span></label>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check p-3 border rounded bg-light" style="cursor: pointer; height: 100%;">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay_cash" value="Cash" checked onclick="togglePaymentFields()">
                                                <label class="form-check-label fw-bold w-100" for="pay_cash" style="cursor: pointer;">
                                                    <i class="fas fa-hand-holding-usd text-success me-2"></i> Cash on Pickup
                                                </label>
                                                <div class="small text-muted ps-4 mt-1">Pay at the barangay hall.</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-check p-3 border rounded bg-light" style="cursor: pointer; height: 100%;">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay_online" value="Online" onclick="togglePaymentFields()">
                                                <label class="form-check-label fw-bold w-100" for="pay_online" style="cursor: pointer;">
                                                    <i class="fas fa-mobile-alt text-primary me-2"></i> GCash / Maya
                                                </label>
                                                <div class="small text-muted ps-4 mt-1">Pay via e-wallet.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Online Payment Fields (Hidden by default) --}}
                            <div id="online_payment_fields" style="display: none; background: #F9FAFB; padding: 25px; border-radius: 12px; border: 1px dashed #E5E7EB;">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center mb-3 mb-md-0 border-end">
                                        {{-- Placeholder QR Code --}}
                                        <div class="bg-white p-3 d-inline-block rounded border mb-2">
                                            <i class="fas fa-qrcode fa-3x text-muted"></i>
                                        </div>
                                        <h6 class="fw-bold text-primary mb-0">GCash / Maya</h6>
                                        <p class="mb-0 fw-bold">0912-345-6789</p>
                                        <small class="text-muted">Barangay Treasurer</small>
                                    </div>
                                    <div class="col-md-8 ps-md-4">
                                        <h6 class="fw-bold mb-3 text-gray-700">Online Payment Details</h6>
                                        
                                        <div class="mb-3">
                                            <label for="payment_reference_number" class="section-label">REFERENCE NUMBER <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('payment_reference_number') is-invalid @enderror" 
                                                   id="payment_reference_number" name="payment_reference_number" 
                                                   placeholder="e.g. 10023456789">
                                            @error('payment_reference_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        
                                        <div class="mb-0">
                                            <label for="payment_proof" class="section-label">UPLOAD PROOF (SCREENSHOT) <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" 
                                                   id="payment_proof" name="payment_proof" accept="image/*">
                                            <div class="form-text">Please upload a clear screenshot of the transaction receipt.</div>
                                            @error('payment_proof') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ================= END PAYMENT LOGIC ================= --}}

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

{{-- JavaScript Logic --}}
<script>
    function handleDocChange() {
        const select = document.getElementById('document_select');
        const selectedOption = select.options[select.selectedIndex];
        
        // 1. Handle Price
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        document.getElementById('display_price').textContent = price.toFixed(2);
        
        const paymentSection = document.getElementById('payment_section');
        const payMethods = document.querySelectorAll('input[name="payment_method"]');
        const refInput = document.getElementById('payment_reference_number');
        const proofInput = document.getElementById('payment_proof');

        if (price > 0) {
            paymentSection.style.display = 'block';
            payMethods.forEach(el => el.disabled = false);
            togglePaymentFields(); // Ensure correct online/cash state
        } else {
            paymentSection.style.display = 'none';
            payMethods.forEach(el => el.disabled = true);
            refInput.disabled = true;
            proofInput.disabled = true;
        }

        // 2. Handle Dynamic Fields
        const rawFields = selectedOption.getAttribute('data-fields');
        let fields = [];
        try {
            fields = rawFields ? JSON.parse(rawFields) : [];
        } catch (e) {
            console.error("Error parsing fields:", e);
            fields = [];
        }

        const container = document.getElementById('dynamic_fields_container');
        const wrapper = document.getElementById('dynamic_fields_wrapper');

        container.innerHTML = ''; // Clear previous fields

        if (fields && fields.length > 0) {
            wrapper.style.display = 'block';
            
            fields.forEach(field => {
                // Determine input type
                let inputHtml = '';
                const fieldName = `custom_data[${field.name}]`; // Array syntax for easy controller parsing
                const requiredAttr = field.required ? 'required' : '';
                const asterisk = field.required ? '<span class="text-danger">*</span>' : '';

                if (field.type === 'textarea') {
                    inputHtml = `<textarea class="form-control" name="${fieldName}" rows="3" ${requiredAttr}></textarea>`;
                } else if (field.type === 'select') {
                    let optionsHtml = field.options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
                    inputHtml = `<select class="form-select" name="${fieldName}" ${requiredAttr}>${optionsHtml}</select>`;
                } else {
                    // text, number, date
                    inputHtml = `<input type="${field.type}" class="form-control" name="${fieldName}" ${requiredAttr}>`;
                }

                // Append to container
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-6'; // 2 columns layout
                colDiv.innerHTML = `
                    <label class="section-label text-dark" style="font-size: 0.8rem;">${field.label} ${asterisk}</label>
                    ${inputHtml}
                `;
                container.appendChild(colDiv);
            });
        } else {
            wrapper.style.display = 'none';
        }
    }

    function togglePaymentFields() {
        const isOnline = document.getElementById('pay_online').checked;
        const onlineFields = document.getElementById('online_payment_fields');
        const refInput = document.getElementById('payment_reference_number');
        const proofInput = document.getElementById('payment_proof');
        
        if (isOnline) {
            onlineFields.style.display = 'block';
            refInput.disabled = false;
            proofInput.disabled = false;
            refInput.required = true;
            proofInput.required = true;
        } else {
            onlineFields.style.display = 'none';
            refInput.disabled = true;
            proofInput.disabled = true;
            refInput.required = false;
            proofInput.required = false;
        }
    }

    // Run immediately when page loads to catch pre-selected values
    document.addEventListener("DOMContentLoaded", function() {
        handleDocChange();
    });
</script>

@endsection