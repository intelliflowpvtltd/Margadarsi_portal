@extends('layouts.app')

@section('title', 'Edit Company - ' . $company->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li>
<li class="breadcrumb-item"><a href="{{ route('companies.show', $company) }}">{{ $company->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-pencil me-2 text-gold"></i>
        Edit Company
    </h1>
    <p class="text-muted mb-0">Update company information and settings</p>
</div>

<!-- Edit Form Card -->
<div class="premium-card">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-tabs-premium mb-4" id="companyTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basicInfo">
                <i class="bi bi-info-circle me-2"></i>Basic Info
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#registration">
                <i class="bi bi-file-text me-2"></i>Registration
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact">
                <i class="bi bi-telephone me-2"></i>Contact
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#address">
                <i class="bi bi-geo-alt me-2"></i>Addresses
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#social">
                <i class="bi bi-share me-2"></i>Social Media
            </button>
        </li>
    </ul>

    <form id="companyForm">
        @csrf
        @method('PUT')

        <div class="tab-content">
            <!-- Basic Info Tab -->
            <div class="tab-pane fade show active" id="basicInfo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $company->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Legal Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="legal_name" value="{{ $company->legal_name }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tagline</label>
                        <input type="text" class="form-control" name="tagline" value="{{ $company->tagline }}" placeholder="Your company's catchy tagline">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Brief description about your company">{{ $company->description }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company Logo</label>
                        @if($company->logo)
                        <div class="mb-2">
                            <img src="{{ $company->logo }}" alt="Current Logo" style="max-height: 100px;">
                        </div>
                        @endif
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Favicon</label>
                        @if($company->favicon)
                        <div class="mb-2">
                            <img src="{{ $company->favicon }}" alt="Current Favicon" style="max-height: 32px;">
                        </div>
                        @endif
                        <input type="file" class="form-control" name="favicon" accept="image/*">
                        <small class="text-muted">Recommended: 32x32px, ICO or PNG</small>
                    </div>
                </div>
            </div>

            <!-- Registration Tab -->
            <div class="tab-pane fade" id="registration">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">PAN Number</label>
                        <input type="text" class="form-control text-uppercase" name="pan_number" value="{{ $company->pan_number }}" maxlength="10" placeholder="ABCDE1234F">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">GSTIN</label>
                        <input type="text" class="form-control text-uppercase" name="gstin" value="{{ $company->gstin }}" maxlength="15" placeholder="22AAAAA0000A1Z5">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CIN (Corporate Identification Number)</label>
                        <input type="text" class="form-control text-uppercase" name="cin" value="{{ $company->cin }}" maxlength="21">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Number</label>
                        <input type="text" class="form-control" name="rera_number" value="{{ $company->rera_number }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Incorporation Date</label>
                        <input type="date" class="form-control" name="incorporation_date" value="{{ $company->incorporation_date ? $company->incorporation_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="is_active">
                            <option value="1" {{ $company->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$company->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact Tab -->
            <div class="tab-pane fade" id="contact">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" value="{{ $company->email }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="phone" value="{{ $company->phone }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alternate Phone</label>
                        <input type="tel" class="form-control" name="alternate_phone" value="{{ $company->alternate_phone }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WhatsApp</label>
                        <input type="tel" class="form-control" name="whatsapp" value="{{ $company->whatsapp }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" name="website" value="{{ $company->website }}" placeholder="https://www.example.com">
                    </div>
                </div>
            </div>

            <!-- Address Tab -->
            <div class="tab-pane fade" id="address">
                <h6 class="mb-3 text-maroon">Registered Office Address</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="registered_address_line1" value="{{ $company->registered_address_line1 }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="registered_address_line2" value="{{ $company->registered_address_line2 }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="registered_city" value="{{ $company->registered_city }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="registered_state" value="{{ $company->registered_state }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pincode</label>
                        <input type="text" class="form-control" name="registered_pincode" value="{{ $company->registered_pincode }}" maxlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" name="registered_country" value="{{ $company->registered_country ?? 'India' }}">
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3 text-maroon">Corporate Office Address</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="corporate_address_line1" value="{{ $company->corporate_address_line1 }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="corporate_address_line2" value="{{ $company->corporate_address_line2 }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="corporate_city" value="{{ $company->corporate_city }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="corporate_state" value="{{ $company->corporate_state }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pincode</label>
                        <input type="text" class="form-control" name="corporate_pincode" value="{{ $company->corporate_pincode }}" maxlength="6">
                    </div>
                </div>
            </div>

            <!-- Social Media Tab -->
            <div class="tab-pane fade" id="social">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-facebook me-2"></i>Facebook URL
                        </label>
                        <input type="url" class="form-control" name="facebook_url" value="{{ $company->facebook_url }}" placeholder="https://facebook.com/yourcompany">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-instagram me-2"></i>Instagram URL
                        </label>
                        <input type="url" class="form-control" name="instagram_url" value="{{ $company->instagram_url }}" placeholder="https://instagram.com/yourcompany">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-linkedin me-2"></i>LinkedIn URL
                        </label>
                        <input type="url" class="form-control" name="linkedin_url" value="{{ $company->linkedin_url }}" placeholder="https://linkedin.com/company/yourcompany">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-twitter me-2"></i>Twitter URL
                        </label>
                        <input type="url" class="form-control" name="twitter_url" value="{{ $company->twitter_url }}" placeholder="https://twitter.com/yourcompany">
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <i class="bi bi-youtube me-2"></i>YouTube URL
                        </label>
                        <input type="url" class="form-control" name="youtube_url" value="{{ $company->youtube_url }}" placeholder="https://youtube.com/@yourcompany">
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="btnSave">
                <i class="bi bi-check-circle me-2"></i>
                Update Company
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    /* Nav Tabs Premium */
    .nav-tabs-premium {
        border-bottom: 2px solid rgba(184, 149, 106, 0.2);
    }

    .nav-tabs-premium .nav-link {
        border: none;
        color: var(--color-text-secondary);
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-tabs-premium .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--color-coffee-gold);
        transform: scaleX(0);
        transition: transform 0.2s ease;
    }

    .nav-tabs-premium .nav-link:hover {
        color: var(--color-coffee-gold);
    }

    .nav-tabs-premium .nav-link.active {
        color: var(--color-dark-maroon);
        font-weight: 600;
    }

    .nav-tabs-premium .nav-link.active::after {
        transform: scaleX(1);
    }

    /* Form Enhancements */
    .form-control {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .form-select {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .text-maroon {
        color: var(--color-dark-maroon);
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('companyForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        console.log('📤 Uploading company data with files...');
        console.log('📍 Update URL:', '{{ route("companies.update", $company) }}');

        const btnSave = document.getElementById('btnSave');
        const originalText = btnSave.innerHTML;
        btnSave.disabled = true;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            // Use POST with _method=PUT for file uploads (Laravel method spoofing)
            const response = await fetch('{{ route("companies.update", $company) }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: formData // Send FormData directly for file uploads
            });

            console.log('📥 Response status:', response.status);

            if (!response.ok) {
                const error = await response.json();
                console.error('❌ Update failed:', error);
                throw new Error(error.message || 'Failed to update company');
            }

            const result = await response.json();
            console.log('✅ Update successful:', result);

            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
            alert.style.zIndex = '9999';
            alert.textContent = result.message;
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.remove();
                console.log('🔄 Redirecting to show page...');
                window.location.href = '{{ route("companies.show", $company) }}';
            }, 1500);

        } catch (error) {
            console.error('💥 Error:', error);
            alert('Failed to update company: ' + error.message);
            btnSave.disabled = false;
            btnSave.innerHTML = originalText;
        }
    });
</script>
@endpush