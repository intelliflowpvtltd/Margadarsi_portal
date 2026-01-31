@extends('layouts.app')

@section('title', $project->name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item active">{{ $project->name }}</li>
@endsection

@section('content')
<!-- Hero Section -->
<div class="project-hero mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                @if($project->logo)
                <img src="{{ $project->logo }}" alt="{{ $project->name }}" class="project-hero-logo me-3">
                @else
                <div class="project-hero-logo-placeholder me-3">{{ substr($project->name, 0, 1) }}</div>
                @endif
                <div>
                    <h1 class="project-hero-title mb-2">{{ $project->name }}</h1>
                    <div class="project-hero-badges">
                        <a href="{{ route('companies.show', $project->company) }}" class="badge badge-company">
                            <i class="bi bi-building me-1"></i>{{ $project->company->name }}
                        </a>
                        <span class="badge badge-type-{{ $project->type }}">{{ $project->type_label }}</span>
                        <span class="badge badge-status-{{ $project->status }}">{{ $project->status_label }}</span>
                        @if($project->is_featured)
                        <span class="badge badge-featured"><i class="bi bi-star-fill me-1"></i>Featured</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary me-2">
                <i class="bi bi-pencil me-2"></i>Edit Project
            </a>
            <button class="btn btn-danger" onclick="deleteProject()">
                <i class="bi bi-trash me-2"></i>Delete
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="info-card mb-4">
            <div class="info-card-header">
                <i class="bi bi-info-circle me-2"></i>Basic Information
            </div>
            <div class="info-card-body">
                @if($project->description)
                <div class="mb-3">
                    <label class="info-label">Description</label>
                    <p class="info-value">{{ $project->description }}</p>
                </div>
                @endif

                @if($project->highlights && count($project->highlights) > 0)
                <div class="mb-3">
                    <label class="info-label">Highlights</label>
                    <ul class="highlights-list">
                        @foreach($project->highlights as $highlight)
                        <li>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="info-label">Project Type</label>
                        <p class="info-value"><span class="badge badge-type-{{ $project->type }}">{{ $project->type_label }}</span></p>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">Status</label>
                        <p class="info-value"><span class="badge badge-status-{{ $project->status }}">{{ $project->status_label }}</span></p>
                    </div>
                    @if($project->rera_number)
                    <div class="col-md-6">
                        <label class="info-label">RERA Number</label>
                        <p class="info-value">{{ $project->rera_number }}</p>
                    </div>
                    @endif
                    @if($project->rera_valid_until)
                    <div class="col-md-6">
                        <label class="info-label">RERA Valid Until</label>
                        <p class="info-value">{{ $project->rera_valid_until->format('d M, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Location Details -->
        <div class="info-card mb-4">
            <div class="info-card-header">
                <i class="bi bi-geo-alt me-2"></i>Location Details
            </div>
            <div class="info-card-body">
                @if($project->full_address)
                <div class="mb-3">
                    <label class="info-label">Address</label>
                    <p class="info-value">
                        {{ $project->full_address }}
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ addslashes($project->full_address) }}')">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </p>
                </div>
                @endif

                <div class="row g-3">
                    @if($project->landmark)
                    <div class="col-md-6">
                        <label class="info-label">Landmark</label>
                        <p class="info-value">{{ $project->landmark }}</p>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="info-label">City</label>
                        <p class="info-value">{{ $project->city }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="info-label">State</label>
                        <p class="info-value">{{ $project->state }}</p>
                    </div>
                    @if($project->pincode)
                    <div class="col-md-6">
                        <label class="info-label">PIN Code</label>
                        <p class="info-value">{{ $project->pincode }}</p>
                    </div>
                    @endif
                    @if($project->latitude && $project->longitude)
                    <div class="col-md-12">
                        <label class="info-label">Coordinates</label>
                        <p class="info-value">{{ $project->latitude }}, {{ $project->longitude }}</p>
                    </div>
                    @endif
                    @if($project->google_maps_url)
                    <div class="col-md-12">
                        <a href="{{ $project->google_maps_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-map me-2"></i>View on Google Maps
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Land & Timeline -->
        <div class="info-card mb-4">
            <div class="info-card-header">
                <i class="bi bi-calendar-event me-2"></i>Land & Timeline
            </div>
            <div class="info-card-body">
                <div class="row g-3">
                    @if($project->total_land_area)
                    <div class="col-md-6">
                        <label class="info-label">Total Land Area</label>
                        <p class="info-value">{{ $project->total_land_area }} {{ strtoupper($project->land_area_unit) }}</p>
                    </div>
                    @endif
                    @if($project->launch_date)
                    <div class="col-md-6">
                        <label class="info-label">Launch Date</label>
                        <p class="info-value">{{ $project->launch_date->format('d M, Y') }}</p>
                    </div>
                    @endif
                    @if($project->possession_date)
                    <div class="col-md-6">
                        <label class="info-label">Possession Date</label>
                        <p class="info-value">{{ $project->possession_date->format('d M, Y') }}</p>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <label class="info-label">Completion Progress</label>
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" data-percentage="{{ $project->completion_percentage }}" style="width: 0%">
                                    {{ $project->completion_percentage }}%
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const progressBar = document.querySelector('[data-percentage]');
                                if (progressBar) {
                                    const percentage = progressBar.getAttribute('data-percentage');
                                    setTimeout(() => progressBar.style.width = percentage + '%', 100);
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="stats-card mb-4">
            <div class="stats-card-header">
                <i class="bi bi-graph-up me-2"></i>Project Statistics
            </div>
            <div class="stats-card-body">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $project->towers_count ?? 0 }}</div>
                        <div class="stat-label">Towers/Blocks</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-house-door"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $project->units_count ?? 0 }}</div>
                        <div class="stat-label">Unit Configurations</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-flower2"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $project->amenities_count ?? 0 }}</div>
                        <div class="stat-label">Amenities</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $project->users_count ?? 0 }}</div>
                        <div class="stat-label">Assigned Users</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="info-card">
            <div class="info-card-header">
                <i class="bi bi-clock-history me-2"></i>Meta Information
            </div>
            <div class="info-card-body">
                <div class="mb-3">
                    <label class="info-label">Status</label>
                    <p class="info-value">
                        <span class="badge {{ $project->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $project->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="info-label">Featured</label>
                    <p class="info-value">
                        <span class="badge {{ $project->is_featured ? 'badge-featured' : 'badge-normal' }}">
                            {{ $project->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="info-label">Created</label>
                    <p class="info-value">{{ $project->created_at->format('d M, Y H:i A') }}</p>
                </div>
                <div>
                    <label class="info-label">Last Updated</label>
                    <p class="info-value">{{ $project->updated_at->format('d M, Y H:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $project->name }}</strong>?</p>
                <p class="text-muted mb-0">This action can be undone later by restoring the project.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Delete Project</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Hero Section */
    .project-hero {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 2rem;
    }

    .project-hero-logo {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid rgba(184, 149, 106, 0.3);
    }

    .project-hero-logo-placeholder {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
    }

    .project-hero-title {
        color: var(--color-dark-maroon);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .project-hero-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Info Cards */
    .info-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        overflow: hidden;
    }

    .info-card-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: var(--color-dark-maroon);
        border-bottom: 1px solid rgba(184, 149, 106, 0.2);
    }

    .info-card-body {
        padding: 1.5rem;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }

    .info-value {
        color: var(--color-text-primary);
        margin-bottom: 0;
    }

    .highlights-list {
        margin: 0;
        padding-left: 1.25rem;
    }

    .highlights-list li {
        color: var(--color-text-primary);
        margin-bottom: 0.5rem;
    }

    /* Stats Card */
    .stats-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        overflow: hidden;
    }

    .stats-card-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: var(--color-dark-maroon);
        border-bottom: 1px solid rgba(184, 149, 106, 0.2);
    }

    .stats-card-body {
        padding: 1rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: rgba(184, 149, 106, 0.03);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        background: rgba(184, 149, 106, 0.08);
        transform: translateX(4px);
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .stat-content {
        flex-grow: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }

    /* Progress Bar */
    .progress-container {
        margin-top: 0.5rem;
    }

    .progress {
        height: 24px;
        background: rgba(184, 149, 106, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        transition: width 0.6s ease;
    }

    /* Badges */
    .badge-company {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
        color: white;
        text-decoration: none;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-company:hover {
        opacity: 0.9;
        color: white;
    }

    .badge-type-residential {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-type-commercial {
        background: linear-gradient(135deg, #6f42c1, #5a32a3);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-type-villa {
        background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-type-open_plots {
        background: linear-gradient(135deg, #795548, #5d4037);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-status-upcoming {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-status-ongoing {
        background: linear-gradient(135deg, #fd7e14, #ca6510);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-status-completed {
        background: linear-gradient(135deg, #198754, #146c43);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-status-sold_out {
        background: linear-gradient(135deg, #dc3545, #b02a37);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-featured {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-active {
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-inactive {
        background: linear-gradient(135deg, #EF4444, #DC2626);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-normal {
        background: #6c757d;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Modal Premium */
    .modal-premium {
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
    }

    .modal-premium .modal-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        border-bottom: 1px solid rgba(184, 149, 106, 0.2);
    }

    .modal-premium .modal-title {
        color: var(--color-dark-maroon);
        font-family: var(--font-primary);
    }
</style>
@endpush

@push('scripts')
<script>
    // Delete project
    function deleteProject() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Confirm delete
    document.getElementById('btnConfirmDelete').addEventListener('click', async function() {
        try {
            const response = await fetch('/projects/{{ $project->id }}', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to delete project');
            }

            // Close modal and redirect
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            window.location.href = '{{ route("projects.index") }}';
        } catch (error) {
            console.error('Error deleting project:', error);
            alert('Failed to delete project. Please try again.');
        }
    });

    // Copy to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Address copied to clipboard!', 'success');
        });
    }

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