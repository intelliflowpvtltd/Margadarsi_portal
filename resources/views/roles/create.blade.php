@extends('layouts.app')

@section('title', 'Create Role')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Create Role</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-plus-circle me-2 text-gold"></i>
                Create Custom Role
            </h1>
            <p class="text-muted mb-0">Define a new role with custom permissions and hierarchy</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Roles
        </a>
    </div>
</div>

<!-- Create Role Form -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="premium-card">
            <form id="createRoleForm">
                @csrf

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2 text-gold"></i>
                        Basic Information
                    </h5>
                    <p class="text-muted mb-4">Provide the basic details for this role</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-premium" id="name"
                                name="name" required
                                placeholder="e.g., Marketing Manager">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">A descriptive name for the role</small>
                        </div>

                        <div class="col-md-6">
                            <label for="slug" class="form-label">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-premium" id="slug"
                                name="slug" required
                                placeholder="e.g., marketing-manager">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Auto-generated URL-friendly identifier</small>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control form-control-premium" id="description"
                                name="description" rows="3"
                                placeholder="Describe the responsibilities and scope of this role..."></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <!-- Hierarchy & Settings -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-bar-chart-steps me-2 text-gold"></i>
                        Hierarchy & Settings
                    </h5>
                    <p class="text-muted mb-4">Configure the role's position in the organizational hierarchy</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="hierarchy_level" class="form-label">
                                Hierarchy Level <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-premium"
                                id="hierarchy_level" name="hierarchy_level"
                                min="1" max="10" value="5" required>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Lower numbers = higher authority (1-10)</small>
                        </div>

                        <div class="col-md-6">
                            <label for="scope" class="form-label">
                                Role Scope <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-premium" id="scope" 
                                    name="scope" required>
                                <option value="company" selected>Company-wide</option>
                                <option value="project">Project-specific</option>
                                <option value="department">Department-specific</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Determines where this role applies</small>
                        </div>

                        <!-- Project Selector (conditional) -->
                        <div class="col-md-6" id="projectSelector" style="display: none;">
                            <label for="project_id" class="form-label">
                                Select Project <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-premium" id="project_id" name="project_id">
                                <option value="">Choose a project...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Role limited to this project only</small>
                        </div>

                        <!-- Department Selector (conditional) -->
                        <div class="col-md-6" id="departmentSelector" style="display: none;">
                            <label for="department_id" class="form-label">
                                Select Department <span class="text-danger">*</span>
                            </label>
                            <select class="form-control form-control-premium" id="department_id" name="department_id">
                                <option value="">Choose a department...</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Role limited to this department only</small>
                        </div>
                    </div>

                    <!-- Hierarchy Reference -->
                    <div class="hierarchy-reference mt-4">
                        <h6 class="mb-3">Hierarchy Reference:</h6>
                        <div class="hierarchy-levels">
                            <div class="hierarchy-item">
                                <span class="level-badge level-1">1</span>
                                <span class="level-label">Super Admin - Full system access</span>
                            </div>
                            <div class="hierarchy-item">
                                <span class="level-badge level-2-3">2-3</span>
                                <span class="level-label">Admin/Management - Strategic decisions</span>
                            </div>
                            <div class="hierarchy-item">
                                <span class="level-badge level-4-6">4-6</span>
                                <span class="level-label">Team Leaders/Supervisors - Operational control</span>
                            </div>
                            <div class="hierarchy-item">
                                <span class="level-badge level-7-10">7-10</span>
                                <span class="level-label">Staff/Support - Task execution</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Assignment -->
                <input type="hidden" name="company_id" value="{{ auth()->user()->company_id ?? 1 }}">

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="bi bi-save me-2"></i>
                        Create Role
                    </button>
                </div>

                <!-- Next Step Info -->
                <div class="next-step-info">
                    <i class="bi bi-info-circle me-2"></i>
                    After creating the role, you can assign permissions on the next page.
                </div>
            </form>
        </div>
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

    .form-control-premium.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

    .text-muted {
        font-size: 0.875rem;
    }

    /* Color Picker */
    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .form-control-color {
        width: 80px;
        height: 45px;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 10px;
        padding: 0.25rem;
        cursor: pointer;
    }

    .color-preview {
        flex: 1;
        height: 45px;
        border-radius: 10px;
        border: 1px solid rgba(184, 149, 106, 0.3);
        background: var(--color-coffee-gold);
    }

    /* Hierarchy Reference */
    .hierarchy-reference {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 1.25rem;
    }

    .hierarchy-reference h6 {
        color: var(--color-dark-maroon);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .hierarchy-levels {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .hierarchy-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .level-badge {
        min-width: 50px;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
        color: white;
    }

    .level-badge.level-1 {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }

    .level-badge.level-2-3 {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
    }

    .level-badge.level-4-6 {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    .level-badge.level-7-10 {
        background: linear-gradient(135deg, #6c757d, #5a6268);
    }

    .level-label {
        font-size: 0.875rem;
        color: var(--color-text-secondary);
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(184, 149, 106, 0.15);
    }

    .next-step-info {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(10, 88, 202, 0.05));
        border: 1px solid rgba(13, 110, 253, 0.2);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1.5rem;
        color: #0d6efd;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    /* Loading State */
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
@endpush

@push('scripts')
<script>
    const API_BASE_URL = '/api/v1/roles';
    const COMPANY_ID = parseInt("{{ auth()->user()->company_id ?? 1 }}");

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from name
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        nameInput.addEventListener('input', function() {
            if (slugInput.dataset.manuallyEdited !== 'true') {
                slugInput.value = generateSlug(this.value);
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.manuallyEdited = 'true';
        });

        // Scope change handler
        document.getElementById('scope').addEventListener('change', handleScopeChange);

        // Load projects and departments
        loadProjects();
        loadDepartments();

        // Form submission
        document.getElementById('createRoleForm').addEventListener('submit', handleSubmit);
    });

    // Handle scope change
    function handleScopeChange() {
        const scope = document.getElementById('scope').value;
        const projectSelector = document.getElementById('projectSelector');
        const departmentSelector = document.getElementById('departmentSelector');
        const projectField = document.getElementById('project_id');
        const deptField = document.getElementById('department_id');
        
        // Hide all first and remove required
        projectSelector.style.display = 'none';
        departmentSelector.style.display = 'none';
        projectField.removeAttribute('required');
        deptField.removeAttribute('required');
        
        // Show and set required based on scope
        if (scope === 'project') {
            projectSelector.style.display = 'block';
            projectField.setAttribute('required', 'required');
        } else if (scope === 'department') {
            departmentSelector.style.display = 'block';
            deptField.setAttribute('required', 'required');
        }
    }

    // Load projects via AJAX
    async function loadProjects() {
        try {
            const response = await fetch(`/api/v1/projects?company_id=${COMPANY_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const result = await response.json();
                const select = document.getElementById('project_id');
                // Clear existing options except first
                select.innerHTML = '<option value="">Choose a project...</option>';
                result.data.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    // Load departments via AJAX
    async function loadDepartments() {
        try {
            const response = await fetch(`/api/v1/departments?company_id=${COMPANY_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const result = await response.json();
                const select = document.getElementById('department_id');
                // Clear existing options except first
                select.innerHTML = '<option value="">Choose a department...</option>';
                result.data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading departments:', error);
        }
    }

    // Generate slug from text
    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Handle form submission
    async function handleSubmit(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Get form data
        const formData = new FormData(e.target);
        const scope = formData.get('scope');
        const data = {
            name: formData.get('name'),
            slug: formData.get('slug'),
            description: formData.get('description'),
            hierarchy_level: parseInt(formData.get('hierarchy_level')),
            scope: scope,
            company_id: formData.get('company_id'),
            project_id: scope === 'project' ? parseInt(formData.get('project_id')) || null : null,
            department_id: scope === 'department' ? parseInt(formData.get('department_id')) || null : null
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    // Validation errors
                    displayErrors(result.errors);
                    showToast('Please check the form for errors', 'danger');
                } else {
                    throw new Error(result.message || 'Failed to create role');
                }
                return;
            }

            // Success
            showToast('Role created successfully!', 'success');

            // Redirect to permissions page or role details
            setTimeout(() => {
                window.location.href = `/roles/${result.data.id}/permissions`;
            }, 1000);

        } catch (error) {
            console.error('Error creating role:', error);
            showToast(error.message || 'Failed to create role. Please try again.', 'danger');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // Validate form
    function validateForm(data) {
        let isValid = true;

        // Name validation
        if (!data.name || data.name.trim().length < 3) {
            showFieldError('name', 'Role name must be at least 3 characters');
            isValid = false;
        }

        // Slug validation
        if (!data.slug || !data.slug.match(/^[a-z0-9-]+$/)) {
            showFieldError('slug', 'Slug must contain only lowercase letters, numbers, and hyphens');
            isValid = false;
        }

        // Hierarchy level validation
        if (!data.hierarchy_level || data.hierarchy_level < 1 || data.hierarchy_level > 10) {
            showFieldError('hierarchy_level', 'Hierarchy level must be between 1 and 10');
            isValid = false;
        }

        return isValid;
    }

    // Display validation errors
    function displayErrors(errors) {
        Object.keys(errors).forEach(field => {
            showFieldError(field, errors[field][0]);
        });
    }

    // Show field error
    function showFieldError(field, message) {
        const input = document.getElementById(field);
        const feedback = input.nextElementSibling;

        input.classList.add('is-invalid');
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
            feedback.style.display = 'block';
        }
    }

    // Clear errors
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.style.display = 'none';
        });
    }

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
        document.body.appendChild(toast);

        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush