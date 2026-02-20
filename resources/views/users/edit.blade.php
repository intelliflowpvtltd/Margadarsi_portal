@extends('layouts.app')

@section('title', 'Edit User - ' . $user->first_name . ' ' . $user->last_name)

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item"><a href="{{ route('users.show', $user->id) }}">{{ $user->first_name }} {{ $user->last_name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil-square me-2 text-gold"></i>
                Edit User
            </h1>
            <p class="text-muted mb-0">
                Update details for <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-2"></i>View Profile
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>
</div>

<!-- User Form -->
<div class="form-container">
    <form id="userForm" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <!-- Section: Basic Information -->
        <div class="form-section mb-4">
            <h5 class="form-section-title">
                <i class="bi bi-person me-2"></i>Basic Information
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" required
                           value="{{ $user->first_name }}" placeholder="Enter first name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" required
                           value="{{ $user->last_name }}" placeholder="Enter last name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required
                           value="{{ $user->email }}" placeholder="user@company.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone"
                           value="{{ $user->phone }}" placeholder="+91 9876543210">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Employee Code</label>
                    <input type="text" class="form-control" name="employee_code"
                           value="{{ $user->employee_code }}" placeholder="e.g. EMP-001">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control" name="designation"
                           value="{{ $user->designation }}" placeholder="e.g. Senior Sales Executive">
                </div>
            </div>
        </div>

        <!-- Section: Organization Assignment -->
        <div class="form-section mb-4">
            <h5 class="form-section-title">
                <i class="bi bi-diagram-3 me-2"></i>Organization Assignment
            </h5>
            <p class="text-muted small mb-3">Modify organization structure for this user</p>

            <div class="row g-3">
                <!-- Company (read-only for non-super-admins) -->
                <div class="col-md-6">
                    <label class="form-label">
                        <span class="badge bg-secondary me-1">1</span>
                        Company <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="company_id" id="companySelect" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $user->company_id == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project -->
                <div class="col-md-6">
                    <label class="form-label">
                        <span class="badge bg-secondary me-1">2</span>
                        Projects <span class="text-danger project-required" style="display: none;">*</span>
                    </label>
                    <select class="form-select" name="project_ids[]" id="projectSelect" multiple>
                        @php
                            $userProjectIds = $user->projects->pluck('id')->toArray();
                        @endphp
                        <!-- Will be populated via JS; pre-selected values tracked -->
                    </select>
                    <small class="text-muted" id="projectHelpText">
                        Currently assigned to {{ count($userProjectIds) }} project(s)
                    </small>
                </div>

                <!-- Department -->
                <div class="col-md-6">
                    <label class="form-label">
                        <span class="badge bg-secondary me-1">3</span>
                        Department
                    </label>
                    <select class="form-select" name="department_id" id="departmentSelect">
                        <option value="">Select Department</option>
                        <!-- Populated via JS based on Project -->
                    </select>
                    <small class="text-muted" id="departmentHelpText">Filtered by selected project</small>
                </div>

                <!-- Role -->
                <div class="col-md-6">
                    <label class="form-label">
                        <span class="badge bg-secondary me-1">4</span>
                        Role <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="role_id" id="roleSelect" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                    data-hierarchy="{{ $role->hierarchy_level }}"
                                    data-scope="{{ $role->scope ?? '' }}"
                                    {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}{{ ($role->scope ?? '') === 'company' ? ' (Company Wide)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted" id="roleHelpText">Roles available based on your authority level</small>
                </div>

                <!-- Reports To -->
                <div class="col-md-6">
                    <label class="form-label">
                        <span class="badge bg-secondary me-1">5</span>
                        Reports To
                    </label>
                    <select class="form-select" name="reports_to" id="managerSelect">
                        <option value="">No Manager (Top Level)</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ $user->reports_to == $manager->id ? 'selected' : '' }}>
                                {{ $manager->first_name }} {{ $manager->last_name }}
                                ({{ $manager->role->name ?? 'No Role' }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select user's direct manager</small>
                </div>
            </div>
        </div>

        <!-- Section: Account Settings -->
        <div class="form-section mb-4">
            <h5 class="form-section-title">
                <i class="bi bi-gear me-2"></i>Account Settings
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="passwordInput"
                               placeholder="Leave blank to keep current password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Leave blank to keep existing password. Min 8 chars with mixed case, number, symbol.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Avatar</label>
                    <input type="file" class="form-control" name="avatar" accept="image/*" id="avatarInput">
                    <small class="text-muted">Upload new avatar to replace current one (Max 2MB)</small>
                </div>
                <div class="col-md-6">
                    <div id="avatarPreview" class="mt-2">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Current Avatar" class="avatar-preview-img">
                            <small class="d-block text-muted mt-1">Current avatar</small>
                        @else
                            <div class="avatar-placeholder-lg">
                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-maroon" id="btnSubmit">
                <i class="bi bi-check-circle me-2"></i>Update User
            </button>
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>

<!-- Alert Container -->
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>
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

    .form-section {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .form-section-title {
        color: var(--color-dark-maroon);
        font-weight: 700;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        color: var(--color-coffee-gold);
    }

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

    .form-actions {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(184, 149, 106, 0.2);
    }

    .btn-maroon {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
        color: white;
        border: none;
        padding: 0.625rem 1.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(128, 0, 32, 0.2);
    }

    .btn-maroon:hover {
        background: linear-gradient(135deg, #6B001B, var(--color-dark-maroon));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(128, 0, 32, 0.3);
        color: white;
    }

    .btn-maroon:active {
        transform: translateY(0);
    }

    .btn-maroon:disabled {
        opacity: 0.7;
        transform: none;
        cursor: not-allowed;
    }

    .avatar-preview-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(184, 149, 106, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .avatar-placeholder-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark, #a07936));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(184, 149, 106, 0.3);
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }

    .input-group .btn-outline-secondary {
        border-color: rgba(184, 149, 106, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const avatarInput = document.getElementById('avatarInput');

    // Pre-existing data from controller
    const currentDepartmentId = {{ $user->department_id ?? 'null' }};
    const currentProjectIds = @json($user->projects->pluck('id'));

    // Toggle password visibility
    togglePassword?.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Avatar preview
    avatarInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Avatar file must be less than 2MB.', 'warning');
                e.target.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('avatarPreview');
                preview.innerHTML = `<img src="${ev.target.result}" alt="New Avatar" class="avatar-preview-img">
                                     <small class="d-block text-muted mt-1">New avatar (unsaved)</small>`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Load projects for the current company on page load
    async function loadProjects() {
        const companyId = document.getElementById('companySelect')?.value;
        const projectSelect = document.getElementById('projectSelect');
        if (!companyId || !projectSelect) return;

        try {
            const response = await fetch(`/api/v1/projects?company_id=${companyId}&per_page=100`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            if (!response.ok) return;
            const data = await response.json();
            const projects = data.data || [];

            projectSelect.innerHTML = '';
            projects.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = project.name;
                if (currentProjectIds.includes(project.id)) {
                    option.selected = true;
                }
                projectSelect.appendChild(option);
            });

            // After loading projects, load departments
            await loadDepartments();
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    // Load departments based on project selection
    async function loadDepartments() {
        const departmentSelect = document.getElementById('departmentSelect');
        if (!departmentSelect) return;

        const projectSelect = document.getElementById('projectSelect');
        const selectedOptions = projectSelect ? Array.from(projectSelect.selectedOptions) : [];
        const projectId = selectedOptions.length > 0 ? selectedOptions[0].value : '';

        try {
            let url = '/api/v1/departments?per_page=100&is_active=1';
            if (projectId) {
                url += `&project_id=${projectId}&scope=project`;
            } else {
                url += '&scope=company';
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;
            const data = await response.json();
            const departments = data.data || [];

            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            departments.forEach(dept => {
                const label = dept.is_company_level ? `${dept.name} (Company Level)` : dept.name;
                const selected = dept.id === currentDepartmentId ? 'selected' : '';
                departmentSelect.innerHTML += `<option value="${dept.id}" ${selected}>${label}</option>`;
            });
        } catch (error) {
            console.error('Error loading departments:', error);
        }
    }

    // Load projects when company changes
    document.getElementById('companySelect')?.addEventListener('change', loadProjects);

    // Reload departments when project changes
    document.getElementById('projectSelect')?.addEventListener('change', loadDepartments);

    // Initialize – load projects on page load
    loadProjects();

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const formData = new FormData(this);

            // Laravel needs _method for PUT via FormData
            formData.set('_method', 'PUT');

            const response = await fetch('{{ route("users.update", $user->id) }}', {
                method: 'POST',  // POST with _method=PUT for multipart
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
                if (data.errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => errorMessage += '• ' + error + '\n');
                    });
                    showAlert(errorMessage, 'danger');
                } else {
                    showAlert(data.message || 'Failed to update user.', 'danger');
                }
                return;
            }

            showAlert('User updated successfully!', 'success');
            setTimeout(() => {
                window.location.href = '{{ route("users.show", $user->id) }}';
            }, 1500);

        } catch (error) {
            console.error('Error updating user:', error);
            showAlert('An unexpected error occurred. Please try again.', 'danger');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
        }
    });

    function showAlert(message, type = 'info') {
        const container = document.getElementById('alertContainer');
        const id = 'alert-' + Date.now();
        const alertDiv = document.createElement('div');
        alertDiv.id = id;
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.cssText = 'min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 10px;';
        alertDiv.innerHTML = `
            <div style="white-space: pre-line;">${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        container.appendChild(alertDiv);
        setTimeout(() => {
            const el = document.getElementById(id);
            if (el) el.remove();
        }, 6000);
    }
});
</script>
@endpush
