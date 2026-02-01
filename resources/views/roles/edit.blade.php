@extends('layouts.app')

@section('title', 'Edit Role')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Edit Role</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil me-2 text-gold"></i>
                Edit Role
            </h1>
            <p class="text-muted mb-0">Modify role settings and configuration</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Roles
        </a>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-5">
    <div class="spinner-border text-gold" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-muted mt-3">Loading role...</p>
</div>

<!-- Edit Role Form -->
<div class="row" id="formContainer" style="display: none;">
    <div class="col-lg-8 mx-auto">
        <div class="premium-card">
            <!-- System Role Warning -->
            <div id="systemRoleWarning" class="alert alert-info mb-4" style="display: none;">
                <i class="bi bi-info-circle me-2"></i>
                <strong>System Role:</strong> This is a protected system role. You can only modify its description and color.
                Name, slug, and hierarchy level cannot be changed.
            </div>

            <form id="editRoleForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-info-circle me-2 text-gold"></i>
                        Basic Information
                    </h5>
                    <p class="text-muted mb-4">Modify the basic details for this role</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-premium" id="name"
                                name="name" required
                                placeholder="e.g., Marketing Manager">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="slug" class="form-label">
                                Slug <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-premium" id="slug"
                                name="slug" required readonly
                                placeholder="e.g., marketing-manager">
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Slug cannot be changed after creation</small>
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
                                <option value="company">Company-wide</option>
                                <option value="project">Project-specific</option>
                                <option value="department">Department-specific</option>
                            </select>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Scope determines where this role applies</small>
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
                        </div>
                    </div>
                </div>

                <!-- Role Metadata -->
                <div class="role-metadata">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="metadata-item">
                                <i class="bi bi-shield-check text-gold"></i>
                                <div>
                                    <div class="metadata-value" id="permissionsCount">0</div>
                                    <div class="metadata-label">Permissions Assigned</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metadata-item">
                                <i class="bi bi-people text-gold"></i>
                                <div>
                                    <div class="metadata-value" id="usersCount">0</div>
                                    <div class="metadata-label">Users Assigned</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metadata-item">
                                <i class="bi bi-calendar-check text-gold"></i>
                                <div>
                                    <div class="metadata-value" id="createdAt">-</div>
                                    <div class="metadata-label">Created On</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </a>
                    <a href="#" id="managePermissionsBtn" class="btn btn-outline-primary">
                        <i class="bi bi-shield-lock me-2"></i>
                        Manage Permissions
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="bi bi-save me-2"></i>
                        Update Role
                    </button>
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
    }

    .form-control-premium[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
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

    /* Role Metadata */
    .role-metadata {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .metadata-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .metadata-item i {
        font-size: 1.5rem;
    }

    .metadata-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .metadata-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
@endpush

@push('scripts')
<script>
    const API_BASE_URL = '/api/v1/roles';
    const ROLE_ID = window.location.pathname.split('/')[2]; // Extract ID from URL
    const COMPANY_ID = parseInt("{{ auth()->user()->company_id ?? 1 }}");
    let currentRole = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadRole();

        // Scope change handler
        document.getElementById('scope').addEventListener('change', handleScopeChange);

        // Load projects and departments
        loadProjects();
        loadDepartments();

        // Form submission
        document.getElementById('editRoleForm').addEventListener('submit', handleSubmit);
    });

    // Handle scope change
    function handleScopeChange() {
        const scope = document.getElementById('scope').value;
        const projectSelector = document.getElementById('projectSelector');
        const departmentSelector = document.getElementById('departmentSelector');
        const projectField = document.getElementById('project_id');
        const deptField = document.getElementById('department_id');
        
        // Hide all first
        projectSelector.style.display = 'none';
        departmentSelector.style.display = 'none';
        projectField.removeAttribute('required');
        deptField.removeAttribute('required');
        
        // Show based on scope
        if (scope === 'project') {
            projectSelector.style.display = 'block';
            projectField.setAttribute('required', 'required');
        } else if (scope === 'department') {
            departmentSelector.style.display = 'block';
            deptField.setAttribute('required', 'required');
        }
    }

    // Load projects
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

    // Load departments
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

    // Load role data
    async function loadRole() {
        try {
            const response = await fetch(`${API_BASE_URL}/${ROLE_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to load role');
            }

            const result = await response.json();
            currentRole = result.data;

            populateForm(currentRole);
            hideLoading();

        } catch (error) {
            console.error('Error loading role:', error);
            showToast('Failed to load role. Redirecting...', 'danger');
            setTimeout(() => window.location.href = '/roles', 2000);
        }
    }

    // Populate form with role data
    function populateForm(role) {
        document.getElementById('name').value = role.name || '';
        document.getElementById('slug').value = role.slug || '';
        document.getElementById('description').value = role.description || '';
        document.getElementById('hierarchy_level').value = role.hierarchy_level || 5;
        
        // Set scope and trigger display logic
        document.getElementById('scope').value = role.scope || 'company';
        setTimeout(() => {
            handleScopeChange();
            
            // Set project/department after selectors are populated
            if (role.project_id) {
                document.getElementById('project_id').value = role.project_id;
            }
            if (role.department_id) {
                document.getElementById('department_id').value = role.department_id;
            }
        }, 500);

        // Update metadata
        document.getElementById('permissionsCount').textContent = role.permissions_count || 0;
        document.getElementById('usersCount').textContent = role.users_count || 0;
        document.getElementById('createdAt').textContent = formatDate(role.created_at);

        // Update manage permissions link
        document.getElementById('managePermissionsBtn').href = `/roles/${role.id}/permissions`;

        // Show system role warning and disable fields if system role
        if (role.is_system) {
            document.getElementById('systemRoleWarning').style.display = 'block';
            document.getElementById('name').setAttribute('readonly', true);
            document.getElementById('hierarchy_level').setAttribute('readonly', true);
            document.getElementById('scope').setAttribute('disabled', true);
        }
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
            description: formData.get('description'),
            hierarchy_level: parseInt(formData.get('hierarchy_level')),
            scope: scope,
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
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch(`${API_BASE_URL}/${ROLE_ID}`, {
                method: 'PUT',
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
                    displayErrors(result.errors);
                    showToast('Please check the form for errors', 'danger');
                } else {
                    throw new Error(result.message || 'Failed to update role');
                }
                return;
            }

            // Success
            showToast('Role updated successfully!', 'success');

            // Reload role data
            setTimeout(() => loadRole(), 1000);

        } catch (error) {
            console.error('Error updating role:', error);
            showToast(error.message || 'Failed to update role. Please try again.', 'danger');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    }

    // Validate form
    function validateForm(data) {
        let isValid = true;

        if (!data.name || data.name.trim().length < 3) {
            showFieldError('name', 'Role name must be at least 3 characters');
            isValid = false;
        }

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

    // Hide loading, show form
    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
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