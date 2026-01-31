

<?php $__env->startSection('title', 'Role Details'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('roles.index')); ?>">Roles</a></li>
<li class="breadcrumb-item active" id="roleName">Role Details</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!--Loading State -->
<div id="loadingState" class="text-center py-5">
    <div class="spinner-border text-gold" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="text-muted mt-3">Loading role details...</p>
</div>

<!-- Role Details Container -->
<div id="detailsContainer" style="display: none;">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center gap-3">
                <div class="role-icon-large" id="roleIcon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h1 class="page-title mb-0" id="roleNameHeader">Role Name</h1>
                        <span class="badge-type" id="roleTypeBadge">System</span>
                        <span class="badge-hierarchy" id="hierarchyBadge">Level 1</span>
                    </div>
                    <p class="text-muted mb-0" id="roleDescription">Role description</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back
                </a>
                <a href="#" id="editRoleBtn" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
                </a>
                <button type="button" id="deleteRoleBtn" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card-detailed">
                <div class="stat-icon-detailed permissions">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value-detailed" id="permissionsCount">0</div>
                    <div class="stat-label-detailed">Permissions Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-detailed">
                <div class="stat-icon-detailed users">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value-detailed" id="usersCount">0</div>
                    <div class="stat-label-detailed">Users Assigned</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-detailed">
                <div class="stat-icon-detailed hierarchy">
                    <i class="bi bi-bar-chart-steps"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value-detailed" id="hierarchyLevel">0</div>
                    <div class="stat-label-detailed">Hierarchy Level</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-detailed">
                <div class="stat-icon-detailed created">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value-detailed" id="createdDate">-</div>
                    <div class="stat-label-detailed">Created On</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Permissions Section -->
        <div class="col-lg-6">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2 text-gold"></i>
                        Assigned Permissions
                    </h5>
                    <a href="#" id="managePermissionsBtn" class="btn btn-sm btn-primary">
                        <i class="bi bi-gear me-1"></i>
                        Manage
                    </a>
                </div>

                <div id="permissionsContainer" class="permissions-list">
                    <!-- Permissions will be loaded here -->
                </div>

                <div id="noPermissions" class="empty-state-small" style="display: none;">
                    <i class="bi bi-shield-x text-muted"></i>
                    <p class="text-muted mb-0">No permissions assigned yet</p>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="col-lg-6">
            <div class="premium-card">
                <div class="card-header-premium">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2 text-gold"></i>
                        Users with this Role
                    </h5>
                    <span class="badge bg-secondary" id="usersBadge">0 users</span>
                </div>

                <div id="usersContainer" class="users-list">
                    <!-- Users will be loaded here -->
                </div>

                <div id="noUsers" class="empty-state-small" style="display: none;">
                    <i class="bi bi-people text-muted"></i>
                    <p class="text-muted mb-0">No users assigned to this role yet</p>
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
                <p>Are you sure you want to delete this role?</p>
                <div id="roleUsersWarning" style="display: none;">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> <span id="warningUsersCount">0</span> user(s) are assigned to this role.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Delete Role</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .card-header-premium {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .card-header-premium h5 {
        color: var(--color-dark-maroon);
        font-weight: 700;
    }

    /* Role Icon Large */
    .role-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    /* Badges */
    .badge-type {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-type.system {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
    }

    .badge-type.custom {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        color: white;
    }

    .badge-hierarchy {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.1), rgba(184, 149, 106, 0.1));
        color: var(--color-dark-maroon);
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(184, 149, 106, 0.3);
    }

    /* Stats Card Detailed */
    .stat-card-detailed {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card-detailed:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
    }

    .stat-icon-detailed {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
    }

    .stat-icon-detailed.permissions {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
    }

    .stat-icon-detailed.users {
        background: linear-gradient(135deg, #198754, #146c43);
    }

    .stat-icon-detailed.hierarchy {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    .stat-icon-detailed.created {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }

    .stat-value-detailed {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        line-height: 1;
    }

    .stat-label-detailed {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    /* Permissions List */
    .permissions-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .permission-module {
        margin-bottom: 1rem;
    }

    .permission-module-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 600;
        color: var(--color-dark-maroon);
    }

    .permission-module-header i {
        color: var(--color-coffee-gold);
        margin-right: 0.5rem;
    }

    .permission-count-badge {
        background: var(--color-coffee-gold);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .permission-items {
        padding-left: 2rem;
    }

    .permission-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(184, 149, 106, 0.1);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .permission-item:last-child {
        border-bottom: none;
    }

    .permission-item i {
        color: #198754;
        font-size: 0.875rem;
    }

    .permission-name {
        font-size: 0.875rem;
        color: var(--color-text-secondary);
    }

    /* Users List */
    .users-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .user-item {
        padding: 1rem;
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }

    .user-item:hover {
        background: rgba(184, 149, 106, 0.05);
        border-color: var(--color-coffee-gold);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 1.125rem;
    }

    .user-info {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.125rem;
    }

    .user-email {
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }

    .user-status {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.875rem;
    }

    .user-status.active {
        color: #198754;
    }

    .user-status.inactive {
        color: #dc3545;
    }

    /* Empty State Small */
    .empty-state-small {
        text-align: center;
        padding: 2rem 1rem;
    }

    .empty-state-small i {
        font-size: 2.5rem;
        opacity: 0.3;
        margin-bottom: 0.5rem;
    }

    /* Modal */
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
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE_URL = '/api/v1/roles';
    const ROLE_ID = window.location.pathname.split('/')[2];
    let currentRole = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadRoleDetails();

        // Delete button handler
        document.getElementById('deleteRoleBtn').addEventListener('click', showDeleteModal);
        document.getElementById('btnConfirmDelete').addEventListener('click', confirmDelete);
    });

    // Load role details
    async function loadRoleDetails() {
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

            displayRoleDetails(currentRole);
            hideLoading();

        } catch (error) {
            console.error('Error loading role:', error);
            showToast('Failed to load role details', 'danger');
            setTimeout(() => window.location.href = '/roles', 2000);
        }
    }

    // Display role details
    function displayRoleDetails(role) {
        // Header
        document.getElementById('roleName').textContent = role.name;
        document.getElementById('roleNameHeader').textContent = role.name;
        document.getElementById('roleDescription').textContent = role.description || 'No description provided';

        // Type badge
        const typeBadge = document.getElementById('roleTypeBadge');
        typeBadge.textContent = role.is_system ? 'System' : 'Custom';
        typeBadge.classList.add(role.is_system ? 'system' : 'custom');

        // Hierarchy badge
        document.getElementById('hierarchyBadge').textContent = `Level ${role.hierarchy_level}`;

        // Icon
        const icon = getRoleIcon(role.slug);
        document.getElementById('roleIcon').innerHTML = `<i class="bi ${icon}"></i>`;

        // Stats
        document.getElementById('permissionsCount').textContent = role.permissions_count || 0;
        document.getElementById('usersCount').textContent = role.users_count || 0;
        document.getElementById('hierarchyLevel').textContent = `Level ${role.hierarchy_level}`;
        document.getElementById('createdDate').textContent = formatDate(role.created_at);

        // Update edit button link
        document.getElementById('editRoleBtn').href = `/roles/${role.id}/edit`;
        document.getElementById('managePermissionsBtn').href = `/roles/${role.id}/permissions`;

        // Update users badge
        document.getElementById('usersBadge').textContent = `${role.users_count || 0} users`;

        // Hide delete button if system role
        if (role.is_system) {
            document.getElementById('deleteRoleBtn').style.display = 'none';
        }

        // Load permissions and users
        displayPermissions(role.permissions || []);
        displayUsers(role.users || []);
    }

    // Display permissions grouped by module
    function displayPermissions(permissions) {
        const container = document.getElementById('permissionsContainer');

        if (!permissions || permissions.length === 0) {
            document.getElementById('noPermissions').style.display = 'block';
            return;
        }

        // Group by module
        const grouped = groupPermissionsByModule(permissions);

        container.innerHTML = '';
        Object.keys(grouped).forEach(module => {
            const perms = grouped[module];
            const moduleDiv = document.createElement('div');
            moduleDiv.className = 'permission-module';

            const icon = getModuleIcon(module);

            moduleDiv.innerHTML = `
                <div class="permission-module-header">
                    <div>
                        <i class="bi ${icon}"></i>
                        ${capitalizeFirst(module)} Module
                    </div>
                    <span class="permission-count-badge">${perms.length}</span>
                </div>
                <div class="permission-items">
                    ${perms.map(p => `
                        <div class="permission-item">
                            <i class="bi bi-check-circle-fill"></i>
                            <span class="permission-name">${formatPermissionName(p.name)}</span>
                        </div>
                    `).join('')}
                </div>
            `;

            container.appendChild(moduleDiv);
        });
    }

    // Display users
    function displayUsers(users) {
        const container = document.getElementById('usersContainer');

        if (!users || users.length === 0) {
            document.getElementById('noUsers').style.display = 'block';
            return;
        }

        container.innerHTML = '';
        users.forEach(user => {
            const userDiv = document.createElement('div');
            userDiv.className = 'user-item';

            const initials = getInitials(user.first_name, user.last_name);
            const statusClass = user.is_active ? 'active' : 'inactive';
            const statusIcon = user.is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
            const statusText = user.is_active ? 'Active' : 'Inactive';

            userDiv.innerHTML = `
                <div class="user-avatar">${initials}</div>
                <div class="user-info">
                    <div class="user-name">${user.first_name} ${user.last_name}</div>
                    <div class="user-email">${user.email}</div>
                </div>
                <div class="user-status ${statusClass}">
                    <i class="bi ${statusIcon}"></i>
                    ${statusText}
                </div>
            `;

            container.appendChild(userDiv);
        });
    }

    // Group permissions by module
    function groupPermissionsByModule(permissions) {
        const grouped = {};
        permissions.forEach(p => {
            const module = p.name.split('.')[0];
            if (!grouped[module]) {
                grouped[module] = [];
            }
            grouped[module].push(p);
        });
        return grouped;
    }

    // Get module icon
    function getModuleIcon(module) {
        const icons = {
            'companies': 'bi-building',
            'projects': 'bi-building',
            'roles': 'bi-shield-lock',
            'users': 'bi-people'
        };
        return icons[module] || 'bi-gear';
    }

    // Format permission name
    function formatPermissionName(name) {
        const action = name.split('.')[1];
        return action.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    // Get role icon
    function getRoleIcon(slug) {
        const icons = {
            'super-admin': 'bi-shield-fill-check',
            'admin': 'bi-shield-check',
            'sales-manager': 'bi-person-badge',
            'senior-sales-executive': 'bi-person-check',
            'sales-executive': 'bi-person',
            'team-leader': 'bi-people',
            'tele-caller': 'bi-headset'
        };
        return icons[slug] || 'bi-person-circle';
    }

    // Get initials
    function getInitials(firstName, lastName) {
        return (firstName?.charAt(0) || '') + (lastName?.charAt(0) || '');
    }

    // Show delete modal
    function showDeleteModal() {
        if (currentRole.users_count > 0) {
            document.getElementById('roleUsersWarning').style.display = 'block';
            document.getElementById('warningUsersCount').textContent = currentRole.users_count;
        }

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        try {
            const response = await fetch(`${API_BASE_URL}/${ROLE_ID}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to delete role');
            }

            showToast('Role deleted successfully', 'success');
            setTimeout(() => window.location.href = '/roles', 1000);

        } catch (error) {
            console.error('Error deleting role:', error);
            showToast(error.message, 'danger');
        }
    }

    // Hide loading
    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('detailsContainer').style.display = 'block';
    }

    // Format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    // Capitalize first letter
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Margadarsi Portal\Backend\resources\views/roles/show.blade.php ENDPATH**/ ?>