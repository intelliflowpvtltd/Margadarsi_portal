

<?php $__env->startSection('title', 'Departments'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item active">Departments</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-diagram-3 me-2 text-gold"></i>
                Departments
            </h1>
            <p class="text-muted mb-0">Manage your organizational departments</p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.create')): ?>
        <a href="<?php echo e(route('departments.create')); ?>" class="btn btn-gold">
            <i class="bi bi-plus-circle me-2"></i>
            Add Department
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card-premium">
            <div class="stat-icon total">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Departments</div>
                <div class="stat-value" id="totalDepartments">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-premium">
            <div class="stat-icon active">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Active</div>
                <div class="stat-value" id="activeDepartments">0</div>
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
</div>

<!-- Filters -->
<div class="premium-card mb-4">
    <div class="filter-section">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Project</label>
                <select id="filterProject" class="form-control form-control-premium">
                    <option value="">All Projects</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select id="filterType" class="form-control form-control-premium">
                    <option value="">All Types</option>
                    <option value="management">Management (Company Level)</option>
                    <option value="sales">Sales</option>
                    <option value="pre_sales">Pre-Sales</option>
                    <option value="external">External</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select id="filterStatus" class="form-control form-control-premium">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control form-control-premium border-start-0" 
                        placeholder="Search departments...">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Departments Table -->
<div class="premium-card">
    <div class="table-responsive">
        <table class="table table-premium" id="departmentsTable">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Project</th>
                    <th>Type</th>
                    <th>Roles</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="departmentsTableBody">
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-gold" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
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
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    /* Stat Cards */
    .stat-card-premium {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
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
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .stat-icon.total { background: linear-gradient(135deg, #6f42c1, #59359a); }
    .stat-icon.active { background: linear-gradient(135deg, #198754, #146c43); }
    .stat-icon.roles { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .stat-icon.users { background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark)); }

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
        font-size: 1.5rem;
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

    .filter-section {
        padding: 1.25rem;
        border-bottom: 1px solid rgba(184, 149, 106, 0.15);
    }

    .form-control-premium {
        padding: 0.625rem 1rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control-premium:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
        outline: none;
    }

    .input-group .input-group-text {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px 0 0 8px;
    }

    .input-group .form-control-premium {
        border-radius: 0 8px 8px 0;
    }

    /* Table */
    .table-premium {
        margin: 0;
    }

    .table-premium thead th {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.03), rgba(184, 149, 106, 0.03));
        border-bottom: 2px solid rgba(184, 149, 106, 0.15);
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: var(--color-dark-maroon);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table-premium tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(184, 149, 106, 0.1);
    }

    .table-premium tbody tr:hover {
        background: rgba(184, 149, 106, 0.03);
    }

    .table-premium tbody tr:last-child td {
        border-bottom: none;
    }

    /* Department Row */
    .dept-name {
        font-weight: 600;
        color: var(--color-dark-maroon);
    }

    .dept-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Badges */
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
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
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

    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: scale(1.1);
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
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

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE_URL = '/api/v1/departments';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    let departments = [];
    let deleteTargetId = null;
    let searchTimeout = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Load initial data
        loadProjects();
        loadDepartments();

        // Filter handlers
        document.getElementById('filterProject').addEventListener('change', loadDepartments);
        document.getElementById('filterType').addEventListener('change', loadDepartments);
        document.getElementById('filterStatus').addEventListener('change', loadDepartments);
        document.getElementById('searchInput').addEventListener('input', debounceSearch);

        // Delete confirmation
        document.getElementById('confirmDeleteBtn').addEventListener('click', handleDelete);
    });

    // Load projects for filter
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
                const select = document.getElementById('filterProject');
                
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
        const tbody = document.getElementById('departmentsTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="spinner-border text-gold" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            const params = new URLSearchParams();
            
            const projectId = document.getElementById('filterProject').value;
            const typeFilter = document.getElementById('filterType').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const searchQuery = document.getElementById('searchInput').value;

            if (projectId) params.append('project_id', projectId);
            if (typeFilter) params.append('slug', typeFilter);
            if (statusFilter) params.append('is_active', statusFilter);
            if (searchQuery) params.append('search', searchQuery);

            const response = await fetch(`${API_BASE_URL}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to load departments');
            }

            const result = await response.json();
            departments = result.data || [];
            renderTable();
            updateStats();

        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Failed to load departments
                    </td>
                </tr>
            `;
            showToast('Failed to load departments', 'danger');
        }
    }

    // Render table
    function renderTable() {
        const tbody = document.getElementById('departmentsTableBody');

        if (departments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-diagram-3"></i>
                            <h5>No departments found</h5>
                            <p class="mb-0">Create your first department to get started</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = departments.map(dept => {
            const typeClass = dept.slug || 'management';
            const typeLabel = getTypeLabel(dept.slug);

            return `
                <tr>
                    <td>
                        <div class="dept-name">${escapeHtml(dept.name)}</div>
                        ${dept.description ? `<div class="dept-description">${escapeHtml(truncate(dept.description, 50))}</div>` : ''}
                    </td>
                    <td>${dept.project?.name || 'N/A'}</td>
                    <td><span class="badge-type ${typeClass}">${typeLabel}</span></td>
                    <td><strong>${dept.roles_count || 0}</strong></td>
                    <td><strong>${dept.users_count || 0}</strong></td>
                    <td>
                        <span class="badge-status ${dept.is_active ? 'active' : 'inactive'}">
                            ${dept.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/departments/${dept.id}" class="btn btn-action btn-outline-info" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.update')): ?>
                            <a href="/departments/${dept.id}/edit" class="btn btn-action btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('departments.delete')): ?>
                            <button class="btn btn-action btn-outline-danger" title="Delete" onclick="confirmDelete(${dept.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Update stats
    function updateStats() {
        const total = departments.length;
        const active = departments.filter(d => d.is_active).length;
        const roles = departments.reduce((sum, d) => sum + (d.roles_count || 0), 0);
        const users = departments.reduce((sum, d) => sum + (d.users_count || 0), 0);

        document.getElementById('totalDepartments').textContent = total;
        document.getElementById('activeDepartments').textContent = active;
        document.getElementById('totalRoles').textContent = roles;
        document.getElementById('totalUsers').textContent = users;
    }

    // Confirm delete
    function confirmDelete(id) {
        deleteTargetId = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Handle delete
    async function handleDelete() {
        if (!deleteTargetId) return;

        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';

        try {
            const response = await fetch(`${API_BASE_URL}/${deleteTargetId}`, {
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
            loadDepartments();

        } catch (error) {
            console.error('Error:', error);
            showToast(error.message || 'Failed to delete department', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
            deleteTargetId = null;
        }
    }

    // Helper functions
    function getTypeLabel(slug) {
        const labels = {
            'management': 'Management',
            'sales': 'Sales',
            'pre_sales': 'Pre-Sales'
        };
        return labels[slug] || slug || 'Unknown';
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function truncate(text, length) {
        if (!text) return '';
        return text.length > length ? text.substring(0, length) + '...' : text;
    }

    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadDepartments, 500);
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

    // Make confirmDelete available globally
    window.confirmDelete = confirmDelete;
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/departments/index.blade.php ENDPATH**/ ?>