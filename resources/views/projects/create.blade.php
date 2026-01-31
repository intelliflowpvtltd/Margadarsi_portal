@extends('layouts.app')

@section('title', 'Create Project')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item active">Create Project</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-plus-circle me-2 text-gold"></i>
                Create New Project
            </h1>
            <p class="text-muted mb-0">Add a new real estate project to your portfolio</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Projects
        </a>
    </div>
</div>

<!-- Project Form -->
<div class="form-container">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-tabs-premium mb-4" id="projectTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basicInfo">
                <i class="bi bi-info-circle me-2"></i>Basic Info
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reraLocation">
                <i class="bi bi-geo-alt me-2"></i>RERA & Location
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#landTimeline">
                <i class="bi bi-calendar-event me-2"></i>Land & Timeline
            </button>
        </li>
    </ul>

    <form id="projectForm">
        @csrf
        <div class="tab-content">
            <!-- Tab 1: Basic Info -->
            <div class="tab-pane fade show active" id="basicInfo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="villa">Villa</option>
                            <option value="open_plots">Open Plots</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing" selected>Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="sold_out">Sold Out</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Brief description about the project"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Highlights (One per line)</label>
                        <textarea class="form-control" id="highlightsInput" rows="4" placeholder="Enter project highlights, one per line"></textarea>
                        <small class="text-muted">Each line will be converted to a highlight point</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/*" id="logoInput">
                        <small class="text-muted">Recommended: 400x400px, PNG or JPG (Max 2MB)</small>
                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <img src="" alt="Logo Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1">
                            <label class="form-check-label" for="isFeatured">
                                <i class="bi bi-star-fill text-warning"></i> Featured Project
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label" for="isActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: RERA & Location -->
            <div class="tab-pane fade" id="reraLocation">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">RERA Number</label>
                        <input type="text" class="form-control" name="rera_number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Valid Until</label>
                        <input type="date" class="form-control" name="rera_valid_until">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address_line1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Landmark</label>
                        <input type="text" class="form-control" name="landmark">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="state" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PIN Code</label>
                        <input type="text" class="form-control" name="pincode" maxlength="6" pattern="[0-9]{6}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" placeholder="e.g., 17.385044">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" placeholder="e.g., 78.486671">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" class="form-control" name="google_maps_url" placeholder="https://goo.gl/maps/...">
                    </div>
                </div>
            </div>

            <!-- Tab 3: Land & Timeline -->
            <div class="tab-pane fade" id="landTimeline">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Land Area</label>
                        <input type="number" step="0.01" class="form-control" name="total_land_area" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Land Area Unit</label>
                        <select class="form-select" name="land_area_unit">
                            <option value="sqft">Square Feet (sqft)</option>
                            <option value="sqm">Square Meters (sqm)</option>
                            <option value="acre">Acres</option>
                            <option value="hectare">Hectares</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Launch Date</label>
                        <input type="date" class="form-control" name="launch_date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Possession Date</label>
                        <input type="date" class="form-control" name="possession_date">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Completion Percentage: <span id="completionValue">0</span>%</label>
                        <input type="range" class="form-range" name="completion_percentage" min="0" max="100" value="0" id="completionRange">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                            <span>75%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Create Project
            </button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-container {
        background: white;
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
    .form-control,
    .form-select {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

    .form-check-input:checked {
        background-color: var(--color-coffee-gold);
        border-color: var(--color-coffee-gold);
    }

    .form-range::-webkit-slider-thumb {
        background: var(--color-coffee-gold);
    }

    .form-range::-moz-range-thumb {
        background: var(--color-coffee-gold);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(184, 149, 106, 0.2);
    }
</style>
@endpush

@push('scripts')
<script>
    // Logo preview
    document.getElementById('logoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Completion percentage slider
    document.getElementById('completionRange')?.addEventListener('input', function(e) {
        document.getElementById('completionValue').textContent = e.target.value;
    });

    // Form submission
    document.getElementById('projectForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Handle highlights (convert textarea to JSON array)
        const highlightsText = document.getElementById('highlightsInput').value;
        if (highlightsText.trim()) {
            const highlightsArray = highlightsText.split('\n').filter(line => line.trim());
            formData.delete('highlights'); // Remove if exists
            formData.append('highlights', JSON.stringify(highlightsArray));
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        try {
            const response = await fetch('{{ route("projects.store") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!response.ok) {
                // Handle validation errors
                if (data.errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => errorMessage += '• ' + error + '\n');
                    });
                    alert(errorMessage);
                } else {
                    throw new Error(data.message || 'Failed to create project');
                }
            } else {
                // Success - redirect to project show page
                showToast('Project created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = `/projects/${data.data.id}`;
                }, 1000);
            }
        } catch (error) {
            console.error('Error creating project:', error);
            alert('Failed to create project. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush