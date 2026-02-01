@extends('layouts.app')

@section('title', 'Edit Department')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
<li class="breadcrumb-item active">Edit Department</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil-square me-2 text-gold"></i>
                Edit Department
            </h1>
            <p class="text-muted mb-0">
                <span class="badge-department me-2" id="departmentBadge">Loading...</span>
                Update department details
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('departments.show', request()->route('department')) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>
                View Details
            </a>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Departments
            </a>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-5">
    <div class="spinner-border text-gold" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-muted mt-3">Loading department data...</p>
</div>

<!-- Error State -->
<div id="errorState" class="alert alert-danger d-none">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <span id="errorMessage">Failed to load department data</span>
</div>

<!-- Edit Department Form -->
<div class="row d-none" id="formContainer">
    <div class="col-lg-8 mx-auto">
        <div class="premium-card">
            <form id="editDepartmentForm">
                @csrf
                @method('PUT')

                <!-- Project Info (Read-only) -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-building me-2 text-gold"></i>
                        Project Assignment
                    </h5>
                    <p class="text-muted mb-4">Department belongs to this project (cannot be changed)</p>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="project_display" class="form-label">
                                Project
                            </label>
                            <input type="text" class="form-control form-control-premium" id="project_display"
                                readonly disabled>
                            <input type="hidden" id="project_id" name="project_id">
                            <small class="text-muted">Project cannot be changed after creation</small>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2 text-gold"></i>
                        Department Details
                    </h5>
                    <p class="text-muted mb-4">Update the department details</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Department Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-premium" id="name"
                                name="name" required
                                placeholder="e.g., Sales Department">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">A descriptive name for the department</small>
                        </div>

                        <div class="col-md-6">
                            <label for="slug_display" class="form-label">
                                Department Type
                            </label>
                            <input type="text" class="form-control form-control-premium" id="slug_display"
                                readonly disabled>
                            <input type="hidden" id="slug" name="slug">
                            <small class="text-muted">Department type cannot be changed</small>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control form-control-premium" id="description"
                                name="description" rows="3"
                                placeholder="Describe the purpose and responsibilities of this department..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <div class="form-check premium-checkbox">
                                <input type="checkbox" class="form-check-input" id="is_active"
                                    name="is_active">
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Department</strong>
                                    <span class="text-muted d-block">Department is active and can have roles/users assigned</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Stats -->
                <div class="department-stats mb-4" id="departmentStats">
                    <h6 class="mb-3"><i class="bi bi-bar-chart text-gold me-2"></i>Department Statistics:</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-icon roles">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="rolesCount">0</span>
                                    <span class="stat-label">Roles</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-icon users">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="usersCount">0</span>
                                    <span class="stat-label">Users</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-icon created">
                                    <i class="bi bi-calendar-plus"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="createdAt">-</span>
                                    <span class="stat-label">Created</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-gold" id="btnSubmit">
                        <i class="bi bi-check-circle me-2"></i>
                        Update Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toastNotification" class="toast" role="alert">
        <div class="toast-header">
            <i class="bi bi-check-circle-fill text-success me-2" id="toastIcon"></i>
            <strong class="me-auto" id="toastTitle">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
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

    .form-section {
        margin-bottom: 2.5rem;
        padding-bottom: 2.5rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

    .form-control-premium {
        padding: 0.75rem 1rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .form-control-premium:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
        outline: none;
    }

    .form-control-premium:disabled {
        background-color: #f8f9fa;
        opacity: 0.8;
    }

    .form-control-premium.is-invalid {
        border-color: #dc3545;
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

    .badge-department {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .badge-department.management { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .badge-department.sales { background: linear-gradient(135deg, #198754, #146c43); }
    .badge-department.pre_sales { background: linear-gradient(135deg, #fd7e14, #dc6502); }

    .premium-checkbox {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 1rem;
    }

    .premium-checkbox .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.125rem;
    }

    .premium-checkbox .form-check-input:checked {
        background-color: var(--color-coffee-gold);
        border-color: var(--color-coffee-gold);
    }

    /* Department Stats */
    .department-stats {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 1.25rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: white;
        font-size: 1.25rem;
    }

    .stat-icon.roles { background: linear-gradient(135deg, #6f42c1, #59359a); }
    .stat-icon.users { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .stat-icon.created { background: linear-gradient(135deg, #198754, #146c43); }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.35);
        color: white;
    }

    .text-gold {
        color: var(--color-coffee-gold) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const DEPARTMENT_ID = {{ request()->route('department') }};
    const API_BASE_URL = '/api/v1/departments';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        // Load department data
        loadDepartment();

        // Form submission
        document.getElementById('editDepartmentForm').addEventListener('submit', handleSubmit);
    });

    // Load department data
    async function loadDepartment() {
        try {
            const response = await fetch(`${API_BASE_URL}/${DEPARTMENT_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to load department');
            }

            const result = await response.json();
            populateForm(result.data);

            // Show form, hide loading
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('formContainer').classList.remove('d-none');

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('errorState').classList.remove('d-none');
            document.getElementById('errorMessage').textContent = error.message;
        }
    }

    // Populate form with department data
    function populateForm(dept) {
        // Set department badge
        const badge = document.getElementById('departmentBadge');
        badge.textContent = getDepartmentTypeLabel(dept.slug);
        badge.className = `badge-department ${dept.slug}`;

        // Project (read-only)
        document.getElementById('project_display').value = dept.project ? dept.project.name : 'Unknown Project';
        document.getElementById('project_id').value = dept.project_id;

        // Department details
        document.getElementById('name').value = dept.name || '';
        document.getElementById('slug_display').value = getDepartmentTypeLabel(dept.slug);
        document.getElementById('slug').value = dept.slug || '';
        document.getElementById('description').value = dept.description || '';
        document.getElementById('is_active').checked = dept.is_active;

        // Stats
        document.getElementById('rolesCount').textContent = dept.roles_count || 0;
        document.getElementById('usersCount').textContent = dept.users_count || 0;
        document.getElementById('createdAt').textContent = formatDate(dept.created_at);
    }

    // Get department type label
    function getDepartmentTypeLabel(slug) {
        const labels = {
            'management': 'Management',
            'sales': 'Sales',
            'pre_sales': 'Pre-Sales'
        };
        return labels[slug] || slug;
    }

    // Format date
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Handle form submission
    async function handleSubmit(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Get form data
        const formData = new FormData(e.target);
        const data = {
            name: formData.get('name'),
            description: formData.get('description') || null,
            is_active: document.getElementById('is_active').checked
        };

        // Validate
        if (!data.name || data.name.trim() === '') {
            displayErrors({ name: ['Department name is required'] });
            showToast('Please enter department name', 'danger');
            return;
        }

        // Disable submit button
        const btnSubmit = document.getElementById('btnSubmit');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch(`${API_BASE_URL}/${DEPARTMENT_ID}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    displayErrors(result.errors);
                    showToast('Please check the form for errors', 'danger');
                } else {
                    throw new Error(result.message || 'Failed to update department');
                }
                return;
            }

            // Success
            showToast('Department updated successfully!', 'success');

            // Redirect to departments list
            setTimeout(() => {
                window.location.href = '{{ route("departments.index") }}';
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to update department', 'danger');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // Display validation errors
    function displayErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = messages[0];
                }
            }
        }
    }

    // Clear errors
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        toastTitle.textContent = type === 'success' ? 'Success' : 'Error';
        toastMessage.textContent = message;
        
        toastIcon.className = type === 'success' 
            ? 'bi bi-check-circle-fill text-success me-2'
            : 'bi bi-exclamation-circle-fill text-danger me-2';

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
</script>
@endpush
