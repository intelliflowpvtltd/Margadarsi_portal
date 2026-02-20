<?php $__env->startSection('title', 'Roles & Permissions'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item active">Roles & Permissions</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-shield-lock me-2 text-gold"></i>
                Roles & Permissions
            </h1>
            <p class="text-muted mb-0">Manage roles, hierarchy, and access control</p>
        </div>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Add Custom Role
        </a>
    </div>
</div>

<!-- Filters & Search -->
<div class="filter-card mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="search-box-large">
                <i class="bi bi-search search-icon-large"></i>
                <input type="text" class="search-input-large" id="searchRoles" placeholder="Search by role name or description...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select form-select-premium" id="filterType">
                <option value="">All Types</option>
                <option value="system">System Roles</option>
                <option value="custom">Custom Roles</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" id="btnClearFilters">
                <i class="bi bi-x-circle me-2"></i>
                Clear Filters
            </button>
        </div>
    </div>
</div>

<!-- Roles Grid -->
<div class="premium-card">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3">Loading roles...</p>
    </div>

    <!-- Roles Grid Container -->
    <div id="gridContainer" style="display: none;">
        <!-- Stats Summary -->
        <div class="roles-stats mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon system">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="systemRolesCount">0</div>
                            <div class="stat-label">System Roles</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon custom">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="customRolesCount">0</div>
                            <div class="stat-label">Custom Roles</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="totalUsersCount">0</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon permissions">
                            <i class="bi bi-key"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value"><?php echo e($totalPermissions ?? 52); ?></div>
                            <div class="stat-label">Total Permissions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4" id="rolesGrid">
            <!-- Role cards will be inserted via JavaScript -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> roles
            </div>
            <nav>
                <ul class="pagination pagination-premium mb-0" id="pagination">
                    <!-- Pagination will be inserted via JavaScript -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <div class="empty-state-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h4 class="mt-3">No Roles Found</h4>
        <p class="text-muted">Create a custom role to get started</p>
        <a href="<?php echo e(route('roles.create')); ?>" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle me-2"></i>
            Add Custom Role
        </a>
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
                        <strong>Warning:</strong> <span id="usersCount">0</span> user(s) are assigned to this role.
                        They will need to be reassigned before deletion.
                    </div>
                </div>
                <p class="text-muted mb-0 mt-2">This action can be undone later by restoring the role.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Delete Role</button>
            </div>
        </div>
    </div>
</div>

<!-- System Role Warning Modal -->
<div class="modal fade" id="systemRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title">System Role Protected</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    System roles cannot be deleted as they are essential for the application's functionality.
                    You can only modify their permissions and description.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Understood</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Premium Card */
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 12px;
        padding: 1.25rem;
    }

    /* Search Box Large */
    .search-box-large {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon-large {
        position: absolute;
        left: 1rem;
        color: var(--color-text-muted);
        font-size: 1.1rem;
    }

    .search-input-large {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 10px;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
    }

    .search-input-large:focus {
        outline: none;
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    /* Premium Form Select */
    .form-select-premium {
        padding: 0.75rem 1rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .form-select-premium:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    /* Stats Cards */
    .roles-stats {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(250, 249, 246, 0.9));
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
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

    .stat-icon.system {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }

    .stat-icon.custom {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    .stat-icon.total {
        background: linear-gradient(135deg, #198754, #146c43);
    }

    .stat-icon.permissions {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    /* Role Card */
    .role-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(184, 149, 106, 0.2);
    }

    .role-card.system-role {
        border-left: 4px solid #0d6efd;
    }

    .role-card.custom-role {
        border-left: 4px solid var(--color-coffee-gold);
    }

    .role-card-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        padding: 1.25rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.1);
    }

    .role-icon-badge {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        color: white;
    }

    .role-card.system-role .role-icon-badge {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }

    .role-card.custom-role .role-icon-badge {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    .role-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .role-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
    }

    .badge-system {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-custom {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-hierarchy {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.1), rgba(184, 149, 106, 0.1));
        color: var(--color-dark-maroon);
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(184, 149, 106, 0.3);
    }

    .role-card-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .role-description {
        color: var(--color-text-secondary);
        font-size: 0.875rem;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .role-stats {
        display: flex;
        gap: 1.5rem;
        padding-top: 1rem;
        margin-top: auto;
        border-top: 1px solid rgba(184, 149, 106, 0.15);
    }

    .role-stat {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .role-stat i {
        color: var(--color-coffee-gold);
        font-size: 1.125rem;
    }

    .role-stat-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .role-stat-label {
        font-size: 0.875rem;
        color: var(--color-text-muted);
    }

    .role-actions {
        padding: 1rem 1.25rem;
        background: rgba(250, 249, 246, 0.5);
        border-top: 1px solid rgba(184, 149, 106, 0.1);
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        background: white;
        color: var(--color-coffee-gold-dark);
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
    }

    .btn-action:hover:not(:disabled) {
        background: var(--color-coffee-gold);
        color: white;
        border-color: var(--color-coffee-gold);
        transform: translateY(-1px);
    }

    .btn-action.btn-danger:hover:not(:disabled) {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Pagination Premium */
    .pagination-premium .page-link {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        color: var(--color-dark-maroon);
        margin: 0 0.25rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .pagination-premium .page-link:hover {
        background: var(--color-coffee-gold);
        color: white;
        border-color: var(--color-coffee-gold);
    }

    .pagination-premium .page-item.active .page-link {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
        border-color: var(--color-dark-maroon);
    }

    /* Empty State */
    .empty-state-icon {
        font-size: 4rem;
        color: var(--color-coffee-gold);
        opacity: 0.5;
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

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // API Base URL
    const API_BASE_URL = '/api/v1/roles';
    const COMPANY_ID = parseInt("<?php echo e(auth()->user()->company_id ?? 1); ?>");

    // Current state
    let currentPage = 1;
    let searchQuery = '';
    let typeFilter = '';
    let roleToDelete = null;

    // Load roles on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadRoles();

        // Event listeners
        document.getElementById('searchRoles').addEventListener('input', debounce(handleSearch, 500));
        document.getElementById('filterType').addEventListener('change', handleFilterChange);
        document.getElementById('btnClearFilters').addEventListener('click', clearFilters);
        document.getElementById('btnConfirmDelete').addEventListener('click', confirmDelete);
    });

    // Load roles from API
    async function loadRoles(page = 1) {
        currentPage = page;
        showLoading();

        try {
            const params = new URLSearchParams({
                company_id: COMPANY_ID,
                page: currentPage,
                per_page: 12,
                ...(searchQuery && {
                    search: searchQuery
                }),
                // Fix: API expects is_system (boolean), not type
                ...(typeFilter && {
                    is_system: typeFilter === 'system' ? '1' : '0'
                })
            });

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to load roles');
            }

            const data = await response.json();
            renderRoles(data.data);
            renderPagination(data.meta);
            updateStats(data.data);

            if (data.data.length === 0) {
                showEmptyState();
            } else {
                showGrid();
            }
        } catch (error) {
            console.error('Error loading roles:', error);
            showToast('Failed to load roles. Please try again.', 'danger');
            hideLoading();
        }
    }

    // Update stats
    function updateStats(roles) {
        const systemRoles = roles.filter(r => r.is_system);
        const customRoles = roles.filter(r => !r.is_system);
        const totalUsers = roles.reduce((sum, r) => sum + (r.users_count || 0), 0);

        document.getElementById('systemRolesCount').textContent = systemRoles.length;
        document.getElementById('customRolesCount').textContent = customRoles.length;
        document.getElementById('totalUsersCount').textContent = totalUsers;
    }

    // Render roles as cards
    function renderRoles(roles) {
        const grid = document.getElementById('rolesGrid');
        grid.innerHTML = '';

        roles.forEach(role => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';

            const roleType = role.is_system ? 'system' : 'custom';
            const roleTypeClass = role.is_system ? 'system-role' : 'custom-role';
            const badgeClass = role.is_system ? 'badge-system' : 'badge-custom';
            const badgeLabel = role.is_system ? 'System' : 'Custom';
            const icon = getRoleIcon(role.slug);
            const deleteDisabled = role.is_system ? 'disabled' : '';
            const deleteTitle = role.is_system ? 'System roles cannot be deleted' : 'Delete role';

            col.innerHTML = `
                <div class="role-card ${roleTypeClass}">
                    <div class="role-card-header">
                        <div class="role-icon-badge">
                            <i class="bi ${icon}"></i>
                        </div>
                        <h3 class="role-name">${escapeHtml(role.name)}</h3>
                        <div class="role-badges">
                            <span class="${badgeClass}">${badgeLabel}</span>
                            <span class="badge-hierarchy">Level ${role.hierarchy_level}</span>
                        </div>
                    </div>
                    <div class="role-card-body">
                        <p class="role-description">${escapeHtml(role.description || 'No description provided')}</p>
                        <div class="role-stats">
                            <div class="role-stat">
                                <i class="bi bi-shield-check"></i>
                                <div>
                                    <div class="role-stat-value">${role.permissions_count || 0}</div>
                                    <div class="role-stat-label">Permissions</div>
                                </div>
                            </div>
                            <div class="role-stat">
                                <i class="bi bi-people"></i>
                                <div>
                                    <div class="role-stat-value">${role.users_count || 0}</div>
                                    <div class="role-stat-label">Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="role-actions">
                        <button class="btn-action" onclick="viewRole(${role.id})">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn-action" onclick="editRole(${role.id})">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn-action btn-danger" onclick="deleteRole(${role.id}, ${role.is_system}, ${role.users_count || 0})" 
                                ${deleteDisabled} title="${deleteTitle}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `;
            grid.appendChild(col);
        });
    }

    // Get role icon based on slug
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

    // Render pagination
    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        // Update showing text
        document.getElementById('showingFrom').textContent = meta.from || 0;
        document.getElementById('showingTo').textContent = meta.to || 0;
        document.getElementById('totalRecords').textContent = meta.total || 0;

        if (meta.last_page <= 1) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${meta.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadRoles(${meta.current_page - 1}); return false;">Previous</a>`;
        pagination.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                const li = document.createElement('li');
                li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadRoles(${i}); return false;">${i}</a>`;
                pagination.appendChild(li);
            } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                li.innerHTML = '<a class="page-link">...</a>';
                pagination.appendChild(li);
            }
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadRoles(${meta.current_page + 1}); return false;">Next</a>`;
        pagination.appendChild(nextLi);
    }

    // View role
    function viewRole(id) {
        window.location.href = `/roles/${id}`;
    }

    // Edit role
    function editRole(id) {
        window.location.href = `/roles/${id}/edit`;
    }

    // Delete role
    function deleteRole(id, isSystem, usersCount) {
        if (isSystem) {
            const modal = new bootstrap.Modal(document.getElementById('systemRoleModal'));
            modal.show();
            return;
        }

        roleToDelete = id;

        // Show users warning if needed
        if (usersCount > 0) {
            document.getElementById('roleUsersWarning').style.display = 'block';
            document.getElementById('usersCount').textContent = usersCount;
        } else {
            document.getElementById('roleUsersWarning').style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!roleToDelete) return;

        try {
            const response = await fetch(`${API_BASE_URL}/${roleToDelete}`, {
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

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();

            // Reload roles
            loadRoles(currentPage);

            // Show success message
            showToast('Role deleted successfully', 'success');
        } catch (error) {
            console.error('Error deleting role:', error);
            showToast(error.message || 'Failed to delete role. Please try again.', 'danger');
        }
    }

    // Search handler
    function handleSearch(e) {
        searchQuery = e.target.value;
        loadRoles(1);
    }

    // Filter change handler
    function handleFilterChange(e) {
        typeFilter = e.target.value;
        loadRoles(1);
    }

    // Clear filters
    function clearFilters() {
        searchQuery = '';
        typeFilter = '';
        document.getElementById('searchRoles').value = '';
        document.getElementById('filterType').value = '';
        loadRoles(1);
    }

    // Show/hide states
    function showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('gridContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }

    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
    }

    function showGrid() {
        hideLoading();
        document.getElementById('gridContainer').style.display = 'block';
        document.getElementById('emptyState').style.display = 'none';
    }

    function showEmptyState() {
        hideLoading();
        document.getElementById('gridContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
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

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/roles/index.blade.php ENDPATH**/ ?>