@extends('layouts.app')

@section('title', 'Manage Permissions')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Manage Permissions</li>
@endsection

@section('content')
<!-- Loading State -->
<div id="loadingState" class="text-center py-5">
    <div class="spinner-border text-gold" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-muted mt-3">Loading permissions...</p>
</div>

<!-- Main Content -->
<div id="mainContent" style="display: none;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="role-icon" id="roleIcon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h1 class="page-title mb-1" id="roleName">Role Permissions</h1>
                    <p class="text-muted mb-0" id="roleDescription">Manage what this role can access</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back
                </a>
                <button type="button" class="btn btn-primary" id="btnSavePermissions">
                    <i class="bi bi-save me-2"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="bi bi-key"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">27</div>
                    <div class="stat-label">Total Permissions</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon assigned">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="assignedCount">0</div>
                    <div class="stat-label">Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon unassigned">
                    <i class="bi bi-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value" id="unassignedCount">27</div>
                    <div class="stat-label">Unassigned</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Grid -->
    <div class="premium-card">
        <div class="card-header-premium mb-4">
            <h5 class="mb-0">
                <i class="bi bi-shield-check me-2 text-gold"></i>
                Permission Modules
            </h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" id="btnSelectAll">
                    <i class="bi bi-check-all me-1"></i>
                    Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAll">
                    <i class="bi bi-x-lg me-1"></i>
                    Deselect All
                </button>
            </div>
        </div>

        <div class="row g-4" id="permissionsGrid">
            <!-- Permissions will be loaded here -->
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
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .card-header-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .card-header-premium h5 {
        color: var(--color-dark-maroon);
        font-weight: 700;
    }

    /* Role Icon */
    .role-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    /* Stats Card */
    .stat-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.total {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
    }

    .stat-icon.assigned {
        background: linear-gradient(135deg, #198754, #146c43);
    }

    .stat-icon.unassigned {
        background: linear-gradient(135deg, #6c757d, #5a6268);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Permission Module Card */
    .permission-module {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .permission-module:hover {
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
    }

    .module-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .module-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .module-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .module-icon.companies { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .module-icon.projects { background: linear-gradient(135deg, #198754, #146c43); }
    .module-icon.roles { background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light)); }
    .module-icon.users { background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark)); }

    .module-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .module-count {
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }

    .module-body {
        padding: 1rem 1.25rem;
    }

    /* Permission Item */
    .permission-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .permission-item:hover {
        background: rgba(184, 149, 106, 0.05);
        border-color: var(--color-coffee-gold);
    }

    .permission-item.selected {
        background: rgba(25, 135, 84, 0.05);
        border-color: #198754;
    }

    .permission-checkbox {
        width: 20px;
        height: 20px;
        margin-right: 1rem;
        accent-color: #198754;
    }

    .permission-info {
        flex: 1;
    }

    .permission-name {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.125rem;
    }

    .permission-key {
        font-size: 0.75rem;
        color: var(--color-text-muted);
        font-family: monospace;
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
@endpush

@push('scripts')
<script>
    const API_BASE_URL = '/api/v1/roles';
    const ROLE_ID = window.location.pathname.split('/')[2];
    let currentRole = null;
    let allPermissions = [];
    let assignedPermissionIds = [];

    // Permission modules configuration
    const PERMISSION_MODULES = {
        companies: {
            icon: 'bi-building',
            label: 'Companies',
            permissions: [
                { key: 'companies.view', name: 'View Companies' },
                { key: 'companies.create', name: 'Create Companies' },
                { key: 'companies.update', name: 'Update Companies' },
                { key: 'companies.delete', name: 'Delete Companies' },
                { key: 'companies.restore', name: 'Restore Companies' },
                { key: 'companies.force-delete', name: 'Permanently Delete Companies' }
            ]
        },
        projects: {
            icon: 'bi-building',
            label: 'Projects',
            permissions: [
                { key: 'projects.view', name: 'View Projects' },
                { key: 'projects.create', name: 'Create Projects' },
                { key: 'projects.update', name: 'Update Projects' },
                { key: 'projects.delete', name: 'Delete Projects' },
                { key: 'projects.restore', name: 'Restore Projects' },
                { key: 'projects.force-delete', name: 'Permanently Delete Projects' },
                { key: 'projects.manage-specifications', name: 'Manage Specifications' }
            ]
        },
        roles: {
            icon: 'bi-shield-lock',
            label: 'Roles',
            permissions: [
                { key: 'roles.view', name: 'View Roles' },
                { key: 'roles.create', name: 'Create Roles' },
                { key: 'roles.update', name: 'Update Roles' },
                { key: 'roles.delete', name: 'Delete Roles' },
                { key: 'roles.restore', name: 'Restore Roles' },
                { key: 'roles.assign-permissions', name: 'Assign Permissions' },
                { key: 'roles.seed', name: 'Seed System Roles' }
            ]
        },
        users: {
            icon: 'bi-people',
            label: 'Users',
            permissions: [
                { key: 'users.view', name: 'View Users' },
                { key: 'users.create', name: 'Create Users' },
                { key: 'users.update', name: 'Update Users' },
                { key: 'users.delete', name: 'Delete Users' },
                { key: 'users.restore', name: 'Restore Users' },
                { key: 'users.force-delete', name: 'Permanently Delete Users' },
                { key: 'users.assign-projects', name: 'Assign Projects' }
            ]
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadRoleAndPermissions();

        document.getElementById('btnSavePermissions').addEventListener('click', savePermissions);
        document.getElementById('btnSelectAll').addEventListener('click', selectAll);
        document.getElementById('btnDeselectAll').addEventListener('click', deselectAll);
    });

    async function loadRoleAndPermissions() {
        try {
            const response = await fetch(`${API_BASE_URL}/${ROLE_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load role');

            const result = await response.json();
            currentRole = result.data;

            // Get assigned permission IDs
            assignedPermissionIds = (currentRole.permissions || []).map(p => p.name);

            displayRoleInfo(currentRole);
            renderPermissionsGrid();
            updateCounts();
            hideLoading();

        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to load role', 'danger');
            setTimeout(() => window.location.href = '/roles', 2000);
        }
    }

    function displayRoleInfo(role) {
        document.getElementById('roleName').textContent = `${role.name} - Permissions`;
        document.getElementById('roleDescription').textContent = role.description || 'Manage permissions for this role';
    }

    function renderPermissionsGrid() {
        const grid = document.getElementById('permissionsGrid');
        grid.innerHTML = '';

        Object.keys(PERMISSION_MODULES).forEach(moduleKey => {
            const module = PERMISSION_MODULES[moduleKey];
            const col = document.createElement('div');
            col.className = 'col-md-6';

            const assignedInModule = module.permissions.filter(p => 
                assignedPermissionIds.includes(p.key)
            ).length;

            col.innerHTML = `
                <div class="permission-module">
                    <div class="module-header">
                        <div class="module-title">
                            <div class="module-icon ${moduleKey}">
                                <i class="bi ${module.icon}"></i>
                            </div>
                            <span>${module.label}</span>
                        </div>
                        <div class="module-toggle">
                            <span class="module-count">${assignedInModule}/${module.permissions.length}</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                    id="toggle-${moduleKey}" 
                                    onchange="toggleModule('${moduleKey}')"
                                    ${assignedInModule === module.permissions.length ? 'checked' : ''}>
                            </div>
                        </div>
                    </div>
                    <div class="module-body">
                        ${module.permissions.map(p => `
                            <div class="permission-item ${assignedPermissionIds.includes(p.key) ? 'selected' : ''}"
                                 onclick="togglePermission('${p.key}', this)">
                                <input type="checkbox" class="permission-checkbox" 
                                    data-permission="${p.key}"
                                    ${assignedPermissionIds.includes(p.key) ? 'checked' : ''}>
                                <div class="permission-info">
                                    <div class="permission-name">${p.name}</div>
                                    <div class="permission-key">${p.key}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            grid.appendChild(col);
        });
    }

    function togglePermission(key, element) {
        const checkbox = element.querySelector('.permission-checkbox');
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            if (!assignedPermissionIds.includes(key)) {
                assignedPermissionIds.push(key);
            }
            element.classList.add('selected');
        } else {
            assignedPermissionIds = assignedPermissionIds.filter(p => p !== key);
            element.classList.remove('selected');
        }

        updateCounts();
        updateModuleToggles();
    }

    function toggleModule(moduleKey) {
        const module = PERMISSION_MODULES[moduleKey];
        const toggle = document.getElementById(`toggle-${moduleKey}`);
        const isChecked = toggle.checked;

        module.permissions.forEach(p => {
            const checkbox = document.querySelector(`[data-permission="${p.key}"]`);
            const item = checkbox.closest('.permission-item');

            if (isChecked) {
                if (!assignedPermissionIds.includes(p.key)) {
                    assignedPermissionIds.push(p.key);
                }
                checkbox.checked = true;
                item.classList.add('selected');
            } else {
                assignedPermissionIds = assignedPermissionIds.filter(id => id !== p.key);
                checkbox.checked = false;
                item.classList.remove('selected');
            }
        });

        updateCounts();
    }

    function selectAll() {
        Object.keys(PERMISSION_MODULES).forEach(moduleKey => {
            document.getElementById(`toggle-${moduleKey}`).checked = true;
            toggleModule(moduleKey);
        });
    }

    function deselectAll() {
        Object.keys(PERMISSION_MODULES).forEach(moduleKey => {
            document.getElementById(`toggle-${moduleKey}`).checked = false;
            toggleModule(moduleKey);
        });
    }

    function updateCounts() {
        const total = Object.values(PERMISSION_MODULES).reduce((sum, m) => sum + m.permissions.length, 0);
        const assigned = assignedPermissionIds.length;

        document.getElementById('assignedCount').textContent = assigned;
        document.getElementById('unassignedCount').textContent = total - assigned;
    }

    function updateModuleToggles() {
        Object.keys(PERMISSION_MODULES).forEach(moduleKey => {
            const module = PERMISSION_MODULES[moduleKey];
            const assignedInModule = module.permissions.filter(p => 
                assignedPermissionIds.includes(p.key)
            ).length;

            const toggle = document.getElementById(`toggle-${moduleKey}`);
            toggle.checked = assignedInModule === module.permissions.length;
        });
    }

    async function savePermissions() {
        const btn = document.getElementById('btnSavePermissions');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        try {
            const response = await fetch(`/roles/${ROLE_ID}/permissions`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ permissions: assignedPermissionIds })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to save permissions');
            }

            showToast('Permissions saved successfully!', 'success');

        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to save permissions', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('mainContent').style.display = 'block';
    }

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
