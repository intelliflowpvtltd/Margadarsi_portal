@extends('layouts.app')

@section('title', 'Department Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
<li class="breadcrumb-item active">Department Details</li>
@endsection

@section('content')
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

<!-- Content Container -->
<div id="contentContainer" class="d-none">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h1 class="page-title mb-0" id="departmentName">Department Name</h1>
                    <span class="badge-type" id="typeBadge">Sales</span>
                    <span class="badge-status" id="statusBadge">Active</span>
                </div>
                <p class="text-muted mb-0" id="departmentDescription">Department description goes here</p>
            </div>
            <div class="d-flex gap-2">
                @can('departments.update')
                <a href="#" id="editBtn" class="btn btn-gold">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Department
                </a>
                @endcan
                @can('departments.delete')
                <button class="btn btn-outline-danger" id="deleteBtn">
                    <i class="bi bi-trash me-2"></i>
                    Delete
                </button>
                @endcan
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-premium">
                <div class="stat-icon project">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Project</div>
                    <div class="stat-value" id="projectName">-</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium">
                <div class="stat-icon roles">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Roles</div>
                    <div class="stat-value" id="totalRoles">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium">
                <div class="stat-icon users">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value" id="totalUsers">0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-premium">
                <div class="stat-icon created">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Created</div>
                    <div class="stat-value" id="createdAt">-</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row g-4">
        <!-- Roles Section -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header-premium">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2 text-gold"></i>
                        Assigned Roles
                    </h5>
                    <span class="badge badge-count" id="rolesCount">0</span>
                </div>
                <div class="card-body-premium">
                    <div id="rolesList">
                        <div class="empty-state">
                            <i class="bi bi-shield"></i>
                            <p>No roles assigned to this department</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <div class="card-header-premium">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2 text-gold"></i>
                        Team Members
                    </h5>
                    <span class="badge badge-count" id="usersCount">0</span>
                </div>
                <div class="card-body-premium">
                    <div id="usersList">
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <p>No users assigned to this department</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-gold"></i>
                        Department Information
                    </h5>
                </div>
                <div class="card-body-premium">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Department Name</label>
                                <span id="infoName">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Department Type</label>
                                <span id="infoType">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Project</label>
                                <span id="infoProject">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Status</label>
                                <span id="infoStatus">-</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-item">
                                <label>Description</label>
                                <span id="infoDescription">No description provided</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Created At</label>
                                <span id="infoCreatedAt">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label>Last Updated</label>
                                <span id="infoUpdatedAt">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this department?</p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <span id="deleteWarning">This action cannot be undone.</span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-2"></i>
                    Delete Department
                </button>
            </div>
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
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .badge-type {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .badge-type.management { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .badge-type.sales { background: linear-gradient(135deg, #198754, #146c43); }
    .badge-type.pre_sales { background: linear-gradient(135deg, #fd7e14, #dc6502); }

    .badge-status {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status.active {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-status.inactive {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }

    /* Stat Cards */
    .stat-card-premium {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
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

    .stat-icon.project { background: linear-gradient(135deg, #6f42c1, #59359a); }
    .stat-icon.roles { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .stat-icon.users { background: linear-gradient(135deg, #198754, #146c43); }
    .stat-icon.created { background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark)); }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-content .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    /* Premium Card */
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-header-premium {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-premium h5 {
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .badge-count {
        background: var(--color-coffee-gold);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .card-body-premium {
        padding: 1.5rem;
        max-height: 400px;
        overflow-y: auto;
    }

    /* Role/User Items */
    .list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(184, 149, 106, 0.15);
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .list-item:hover {
        background: rgba(184, 149, 106, 0.05);
    }

    .list-item:last-child {
        margin-bottom: 0;
    }

    .list-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: white;
    }

    .list-item-icon.role { background: linear-gradient(135deg, #6f42c1, #59359a); }
    .list-item-icon.user { background: linear-gradient(135deg, #0d6efd, #0a58ca); }

    .list-item-content {
        flex: 1;
    }

    .list-item-title {
        font-weight: 600;
        color: var(--color-dark-maroon);
        font-size: 0.9rem;
    }

    .list-item-subtitle {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .list-item-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    /* Info Items */
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-item label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-item span {
        font-size: 0.95rem;
        color: var(--color-dark-maroon);
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
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

    let departmentData = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadDepartment();

        // Delete button handler
        document.getElementById('deleteBtn')?.addEventListener('click', () => {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });

        // Confirm delete handler
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', handleDelete);
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
            departmentData = result.data;
            renderDepartment(departmentData);

            // Show content, hide loading
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('contentContainer').classList.remove('d-none');

        } catch (error) {
            console.error('Error:', error);
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('errorState').classList.remove('d-none');
            document.getElementById('errorMessage').textContent = error.message;
        }
    }

    // Render department data
    function renderDepartment(dept) {
        // Header
        document.getElementById('departmentName').textContent = dept.name;
        document.getElementById('departmentDescription').textContent = dept.description || 'No description provided';

        // Type badge
        const typeBadge = document.getElementById('typeBadge');
        typeBadge.textContent = getDepartmentTypeLabel(dept.slug);
        typeBadge.className = `badge-type ${dept.slug}`;

        // Status badge
        const statusBadge = document.getElementById('statusBadge');
        statusBadge.textContent = dept.is_active ? 'Active' : 'Inactive';
        statusBadge.className = `badge-status ${dept.is_active ? 'active' : 'inactive'}`;

        // Edit button URL
        const editBtn = document.getElementById('editBtn');
        if (editBtn) {
            editBtn.href = `/departments/${dept.id}/edit`;
        }

        // Stats
        document.getElementById('projectName').textContent = dept.project?.name || 'Unknown';
        document.getElementById('totalRoles').textContent = dept.roles_count || 0;
        document.getElementById('totalUsers').textContent = dept.users_count || 0;
        document.getElementById('createdAt').textContent = formatDate(dept.created_at);

        // Counts
        document.getElementById('rolesCount').textContent = dept.roles?.length || 0;
        document.getElementById('usersCount').textContent = dept.users?.length || 0;

        // Roles list
        renderRoles(dept.roles || []);

        // Users list
        renderUsers(dept.users || []);

        // Info section
        document.getElementById('infoName').textContent = dept.name;
        document.getElementById('infoType').textContent = getDepartmentTypeLabel(dept.slug);
        document.getElementById('infoProject').textContent = dept.project?.name || 'Unknown';
        document.getElementById('infoStatus').textContent = dept.is_active ? 'Active' : 'Inactive';
        document.getElementById('infoDescription').textContent = dept.description || 'No description provided';
        document.getElementById('infoCreatedAt').textContent = formatDateTime(dept.created_at);
        document.getElementById('infoUpdatedAt').textContent = formatDateTime(dept.updated_at);

        // Delete warning
        if (dept.roles_count > 0 || dept.users_count > 0) {
            document.getElementById('deleteWarning').textContent = 
                `This department has ${dept.roles_count} role(s) and ${dept.users_count} user(s). Please reassign them before deleting.`;
            document.getElementById('confirmDeleteBtn').disabled = true;
        }
    }

    // Render roles list
    function renderRoles(roles) {
        const container = document.getElementById('rolesList');
        
        if (!roles || roles.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-shield"></i>
                    <p>No roles assigned to this department</p>
                </div>
            `;
            return;
        }

        container.innerHTML = roles.map(role => `
            <div class="list-item">
                <div class="list-item-icon role">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="list-item-content">
                    <div class="list-item-title">${escapeHtml(role.name)}</div>
                    <div class="list-item-subtitle">
                        Level ${role.hierarchy_level} • ${role.users_count || 0} users
                    </div>
                </div>
                <span class="list-item-badge ${role.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}">
                    ${role.is_active ? 'Active' : 'Inactive'}
                </span>
            </div>
        `).join('');
    }

    // Render users list
    function renderUsers(users) {
        const container = document.getElementById('usersList');
        
        if (!users || users.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <p>No users assigned to this department</p>
                </div>
            `;
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="list-item">
                <div class="list-item-icon user">
                    <i class="bi bi-person"></i>
                </div>
                <div class="list-item-content">
                    <div class="list-item-title">${escapeHtml(user.name)}</div>
                    <div class="list-item-subtitle">
                        ${user.role?.name || 'No role'} • ${user.email}
                    </div>
                </div>
                <span class="list-item-badge ${user.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'}">
                    ${user.is_active ? 'Active' : 'Inactive'}
                </span>
            </div>
        `).join('');
    }

    // Handle delete
    async function handleDelete() {
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        try {
            const response = await fetch(`${API_BASE_URL}/${DEPARTMENT_ID}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete department');
            }

            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            showToast('Department deleted successfully!', 'success');

            setTimeout(() => {
                window.location.href = '{{ route("departments.index") }}';
            }, 1500);

        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to delete department', 'danger');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // Helper functions
    function getDepartmentTypeLabel(slug) {
        const labels = {
            'management': 'Management',
            'sales': 'Sales',
            'pre_sales': 'Pre-Sales'
        };
        return labels[slug] || slug;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            month: 'short', day: 'numeric', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

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
