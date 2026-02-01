@extends('layouts.app')

@section('title', 'Create Department')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
<li class="breadcrumb-item active">Create Department</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-plus-circle me-2 text-gold"></i>
                Create Department
            </h1>
            <p class="text-muted mb-0">Create a new department for a project</p>
        </div>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Departments
        </a>
    </div>
</div>

<!-- Create Department Form -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="premium-card">
            <form id="createDepartmentForm">
                @csrf

                <!-- Project Selection -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-building me-2 text-gold"></i>
                        Project Assignment
                    </h5>
                    <p class="text-muted mb-4">Select the project this department belongs to</p>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="project_id" class="form-label">
                                Project <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-premium" id="project_id"
                                name="project_id" required>
                                <option value="">Select a project...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Department will be scoped to this project</small>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2 text-gold"></i>
                        Department Details
                    </h5>
                    <p class="text-muted mb-4">Provide the basic details for this department</p>

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
                            <label for="slug" class="form-label">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-premium" id="slug" name="slug" required>
                                <option value="">Select department type...</option>
                                <option value="management">Management</option>
                                <option value="sales">Sales</option>
                                <option value="pre_sales">Pre-Sales</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Standard department type identifier</small>
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
                                    name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Department</strong>
                                    <span class="text-muted d-block">Department is active and can have roles/users assigned</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Types Reference -->
                <div class="department-types-reference mb-4">
                    <h6 class="mb-3"><i class="bi bi-lightbulb text-gold me-2"></i>Department Types:</h6>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="type-card management">
                                <i class="bi bi-briefcase"></i>
                                <div>
                                    <strong>Management</strong>
                                    <small>Admin, strategic decisions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="type-card sales">
                                <i class="bi bi-graph-up-arrow"></i>
                                <div>
                                    <strong>Sales</strong>
                                    <small>Lead conversion, closures</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="type-card presales">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <strong>Pre-Sales</strong>
                                    <small>Telecalling, lead generation</small>
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
                        Create Department
                    </button>
                </div>

                <!-- Form Note -->
                <div class="alert alert-info mt-4 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    After creating the department, you can assign roles and users to it.
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

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
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

    .form-control-premium.is-invalid {
        border-color: #dc3545;
        background-image: none;
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

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

    /* Department Types Reference */
    .department-types-reference {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 1.25rem;
    }

    .type-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
    }

    .type-card i {
        font-size: 1.5rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: white;
    }

    .type-card.management i { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .type-card.sales i { background: linear-gradient(135deg, #198754, #146c43); }
    .type-card.presales i { background: linear-gradient(135deg, #fd7e14, #dc6502); }

    .type-card strong {
        display: block;
        font-size: 0.875rem;
        color: var(--color-dark-maroon);
    }

    .type-card small {
        color: #6c757d;
        font-size: 0.75rem;
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
</style>
@endpush

@push('scripts')
<script>
    const API_BASE_URL = '/api/v1/departments';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        // Load projects
        loadProjects();

        // Form submission
        document.getElementById('createDepartmentForm').addEventListener('submit', handleSubmit);
    });

    // Load projects for dropdown
    async function loadProjects() {
        try {
            const response = await fetch('/api/v1/projects', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const result = await response.json();
                const select = document.getElementById('project_id');
                
                result.data.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading projects:', error);
            showToast('Failed to load projects', 'danger');
        }
    }

    // Handle form submission
    async function handleSubmit(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Get form data
        const formData = new FormData(e.target);
        const data = {
            project_id: parseInt(formData.get('project_id')),
            name: formData.get('name'),
            slug: formData.get('slug'),
            description: formData.get('description') || null,
            is_active: document.getElementById('is_active').checked
        };

        // Validate
        if (!validateForm(data)) {
            return;
        }

        // Disable submit button
        const btnSubmit = document.getElementById('btnSubmit');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        try {
            const response = await fetch(API_BASE_URL, {
                method: 'POST',
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
                    throw new Error(result.message || 'Failed to create department');
                }
                return;
            }

            // Success
            showToast('Department created successfully!', 'success');

            // Redirect to departments list
            setTimeout(() => {
                window.location.href = '{{ route("departments.index") }}';
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to create department', 'danger');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // Validate form
    function validateForm(data) {
        let isValid = true;
        const errors = {};

        if (!data.project_id) {
            errors.project_id = ['Please select a project'];
            isValid = false;
        }

        if (!data.name || data.name.trim() === '') {
            errors.name = ['Department name is required'];
            isValid = false;
        }

        if (!data.slug) {
            errors.slug = ['Please select a department type'];
            isValid = false;
        }

        if (!isValid) {
            displayErrors(errors);
        }

        return isValid;
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
