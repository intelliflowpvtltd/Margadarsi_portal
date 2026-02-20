

<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item active">Users</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-people me-2 text-gold"></i>
                User Management
            </h1>
            <p class="text-muted mb-0">Manage users, roles, and project assignments</p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.create')): ?>
        <button class="btn btn-primary" id="btnAddUser">
            <i class="bi bi-person-plus me-2"></i>
            Add User
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters & Search -->
<div class="filter-card mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="search-box-large">
                <i class="bi bi-search search-icon-large"></i>
                <input type="text" class="search-input-large" id="searchUsers" placeholder="Search by name, email, or phone...">
            </div>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterRole">
                <option value="">All Roles</option>
                <!-- Populated via JS -->
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterProject">
                <option value="">All Projects</option>
                <!-- Populated via JS -->
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterDepartment">
                <option value="">All Departments</option>
                <!-- Populated via JS with numeric IDs -->
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterStatus">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" id="btnClearFilters">
                <i class="bi bi-x-circle me-2"></i>
                Clear Filters
            </button>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="premium-card">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3">Loading users...</p>
    </div>

    <!-- Table -->
    <div id="tableContainer" style="display: none;">
        <div class="table-responsive">
            <table class="table user-table">
                <thead>
                    <tr>
                        <th width="60">Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Project</th>
                        <th width="100">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Rows will be inserted via JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> users
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
            <i class="bi bi-people"></i>
        </div>
        <h4 class="mt-3">No Users Found</h4>
        <p class="text-muted">Start by adding your first user</p>
        <button class="btn btn-primary mt-3" onclick="document.getElementById('btnAddUser').click()">
            <i class="bi bi-person-plus me-2"></i>
            Add User
        </button>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">

                    <div class="row g-3">
                        <!-- Section: Basic Info -->
                        <div class="col-12">
                            <h6 class="text-muted border-bottom pb-2 mb-0"><i class="bi bi-person me-2"></i>Basic Information</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee Code</label>
                            <input type="text" class="form-control" name="employee_code">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" class="form-control" name="designation">
                        </div>
                        
                        <!-- Section: Organization (Cascade Flow) -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted border-bottom pb-2 mb-0"><i class="bi bi-diagram-3 me-2"></i>Organization Assignment</h6>
                            <small class="text-muted">Select in order: Project → Department → Role</small>
                        </div>
                        
                        <!-- Step 1: Project (Optional for company-level) -->
                        <div class="col-md-6" id="projectContainer">
                            <label class="form-label">
                                <span class="badge bg-secondary me-1">1</span>
                                Project <span class="text-danger project-required" style="display: none;">*</span>
                            </label>
                            <select class="form-select" name="project_id" id="projectSelect">
                                <option value="">-- None (Company Level) --</option>
                                <!-- Populated via JS -->
                            </select>
                            <small class="text-muted" id="projectHelpText">Leave empty for company-level roles (Super Admin, Company Admin)</small>
                        </div>
                        
                        <!-- Step 2: Department (Filtered by Project) -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <span class="badge bg-secondary me-1">2</span>
                                Department <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="department_id" id="departmentSelect" required>
                                <option value="">Select Department</option>
                                <!-- Populated via JS based on Project -->
                            </select>
                            <small class="text-muted" id="departmentHelpText">Filtered by selected project</small>
                        </div>
                        
                        <!-- Step 3: Role (Filtered by Department) -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <span class="badge bg-secondary me-1">3</span>
                                Role <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="role_id" id="roleSelect" required disabled>
                                <option value="">Select Department First</option>
                                <!-- Populated via JS based on Department -->
                            </select>
                            <small class="text-muted" id="roleHelpText">Filtered by selected department</small>
                        </div>
                        
                        <!-- Step 4: Reports To (Filtered by Role Hierarchy) -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <span class="badge bg-secondary me-1">4</span>
                                Reports To
                            </label>
                            <select class="form-select" name="reports_to" id="managerSelect">
                                <option value="">No Manager (Top Level)</option>
                                <!-- Populated via JS based on Role -->
                            </select>
                            <small class="text-muted">Select user's direct manager</small>
                        </div>
                        
                        <!-- Section: Account Settings -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted border-bottom pb-2 mb-0"><i class="bi bi-gear me-2"></i>Account Settings</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="passwordField">
                            <label class="form-label">Password <span class="text-danger password-required">*</span></label>
                            <input type="password" class="form-control" name="password" id="passwordInput">
                            <small class="text-muted">Min 8 chars, uppercase, lowercase, number, symbol</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Avatar</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveUser">
                    <i class="bi bi-check-circle me-2"></i>
                    <span id="btnSaveText">Save User</span>
                </button>
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
    }

    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 12px;
        padding: 1.25rem;
    }

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

    .form-select-premium {
        padding: 0.75rem 1rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .user-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .user-table thead th {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        color: var(--color-dark-maroon);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-table thead th:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .user-table thead th:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .user-table tbody tr {
        background: white;
        transition: all 0.2s ease;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .user-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
    }

    .user-table tbody td {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    .user-table tbody td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .user-table tbody td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(184, 149, 106, 0.3);
    }

    .user-avatar-placeholder {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }

    .badge-active {
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .badge-inactive {
        background: linear-gradient(135deg, #EF4444, #DC2626);
        color: white;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .role-badge {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.1), rgba(184, 149, 106, 0.1));
        color: var(--color-dark-maroon);
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .dept-badge {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(139, 92, 246, 0.1));
        color: #4f46e5;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 500;
    }

    .action-btn {
        background: rgba(184, 149, 106, 0.1);
        border: 1px solid rgba(184, 149, 106, 0.3);
        color: var(--color-coffee-gold-dark);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--color-coffee-gold);
        color: white;
        border-color: var(--color-coffee-gold);
    }

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

    .empty-state-icon {
        font-size: 4rem;
        color: var(--color-coffee-gold);
        opacity: 0.5;
    }

    .modal-premium {
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
    }

    .modal-premium .modal-header {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        border-bottom: 1px solid rgba(184, 149, 106, 0.2);
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE_URL = '/users';
    let currentPage = 1;
    let searchQuery = '';
    let roleFilter = '';
    let projectFilter = '';
    let departmentFilter = '';
    let statusFilter = '';
    let rolesData = [];
    let usersData = [];
    let projectsData = [];
    let departmentsData = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Load initial data
        loadRoles();
        loadProjectsFilter();
        loadDepartmentsFilter();
        loadUsers();

        // Safe event listener attachment (handles @can directives hiding elements)
        const btnAddUser = document.getElementById('btnAddUser');
        if (btnAddUser) {
            btnAddUser.addEventListener('click', openCreateModal);
        }
        
        document.getElementById('btnSaveUser')?.addEventListener('click', saveUser);
        document.getElementById('searchUsers')?.addEventListener('input', debounce(handleSearch, 500));
        document.getElementById('filterRole')?.addEventListener('change', handleFilterChange);
        document.getElementById('filterProject')?.addEventListener('change', handleFilterChange);
        document.getElementById('filterDepartment')?.addEventListener('change', handleFilterChange);
        document.getElementById('filterStatus')?.addEventListener('change', handleFilterChange);
        document.getElementById('btnClearFilters')?.addEventListener('click', clearFilters);

        // Setup cascade event listeners ONCE (not on every modal open)
        setupCascadeListeners();
    });

    // Setup cascade listeners only once - PROPER FLOW: Project -> Department -> Role -> Manager
    function setupCascadeListeners() {
        const projectSelect = document.getElementById('projectSelect');
        const departmentSelect = document.getElementById('departmentSelect');
        const roleSelect = document.getElementById('roleSelect');

        // Step 1: Project change -> Load departments for that project
        if (projectSelect) {
            projectSelect.addEventListener('change', async function() {
                await updateDepartmentsByProject(this.value);
                // Reset downstream dropdowns
                resetRoleDropdown();
                resetManagerDropdown();
            });
        }

        // Step 2: Department change -> Load roles for that department
        if (departmentSelect) {
            departmentSelect.addEventListener('change', async function() {
                await updateRolesByDepartment(this.value);
                // Reset manager dropdown
                resetManagerDropdown();
            });
        }

        // Step 3: Role change -> Update managers (filter by hierarchy)
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                updateManagersByRole(this.value);
            });
        }
    }

    // Reset role dropdown to initial state
    function resetRoleDropdown() {
        const roleSelect = document.getElementById('roleSelect');
        if (roleSelect) {
            roleSelect.innerHTML = '<option value="">Select Department First</option>';
            roleSelect.disabled = true;
        }
    }

    // Reset manager dropdown
    function resetManagerDropdown() {
        const managerSelect = document.getElementById('managerSelect');
        if (managerSelect) {
            managerSelect.innerHTML = '<option value="">No Manager (Top Level)</option>';
        }
    }

    // Load departments based on selected project
    async function updateDepartmentsByProject(projectId) {
        const departmentSelect = document.getElementById('departmentSelect');
        if (!departmentSelect) return;

        // Reset department dropdown
        departmentSelect.innerHTML = '<option value="">Loading...</option>';

        try {
            let url = '/api/v1/departments?per_page=100&is_active=1';
            
            if (projectId) {
                // Project selected - load project-level departments (Sales, Pre-Sales, External)
                url += `&project_id=${projectId}&scope=project`;
            } else {
                // No project - load company-level departments only (Management)
                url += `&scope=company`;
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load departments');

            const data = await response.json();
            departmentsData = data.data || [];

            // Populate department dropdown
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            departmentsData.forEach(dept => {
                const label = dept.is_company_level ? `${dept.name} (Company Level)` : dept.name;
                departmentSelect.innerHTML += `<option value="${dept.id}" data-type="${dept.slug}" data-scope="${dept.scope || 'project'}">${label}</option>`;
            });

            // Update help text
            const helpText = document.getElementById('departmentHelpText');
            if (helpText) {
                helpText.textContent = projectId 
                    ? `Showing departments for selected project` 
                    : 'Showing company-level departments (no project selected)';
            }

        } catch (error) {
            console.error('Error loading departments:', error);
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
        }
    }

    // Load roles based on selected department type
    async function updateRolesByDepartment(departmentId) {
        const roleSelect = document.getElementById('roleSelect');
        const departmentSelect = document.getElementById('departmentSelect');
        if (!roleSelect || !departmentId) {
            resetRoleDropdown();
            return;
        }

        // Get department type from selected option
        const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
        const deptType = selectedOption?.dataset?.type;

        roleSelect.innerHTML = '<option value="">Loading...</option>';
        roleSelect.disabled = true;

        try {
            // Department type to slug mapping for roles
            const deptRoleMapping = {
                'management': ['super_admin', 'company_admin'],
                'sales': ['project_manager', 'senior_sales_executive', 'sales_executive'],
                'pre_sales': ['team_leader', 'telecaller'],
                'pre-sales': ['team_leader', 'telecaller'],
                'external': ['channel_partner']
            };

            const allowedSlugs = deptRoleMapping[deptType] || [];
            
            // Filter roles based on department type
            const filteredRoles = rolesData.filter(role => allowedSlugs.includes(role.slug));

            roleSelect.innerHTML = '<option value="">Select Role</option>';
            filteredRoles.forEach(role => {
                const scopeLabel = role.requires_project_assignment ? '' : ' (Company Wide)';
                roleSelect.innerHTML += `<option value="${role.id}" data-hierarchy="${role.hierarchy_level}" data-scope="${role.scope}">${role.name}${scopeLabel}</option>`;
            });

            roleSelect.disabled = false;

            // Update help text
            const helpText = document.getElementById('roleHelpText');
            if (helpText) {
                helpText.textContent = `Showing roles for ${selectedOption?.textContent || 'selected department'}`;
            }

        } catch (error) {
            console.error('Error filtering roles:', error);
            roleSelect.innerHTML = '<option value="">Error loading roles</option>';
        }
    }

    // Update managers dropdown based on selected role hierarchy
    function updateManagersByRole(roleId) {
        const managerSelect = document.getElementById('managerSelect');
        const roleSelect = document.getElementById('roleSelect');
        if (!managerSelect) return;

        // Get hierarchy level of selected role
        const selectedOption = roleSelect?.options[roleSelect.selectedIndex];
        const hierarchyLevel = parseInt(selectedOption?.dataset?.hierarchy) || 99;

        // Filter users to show only those with higher authority (lower hierarchy number)
        const potentialManagers = usersData.filter(user => {
            const userHierarchy = user.role?.hierarchy_level || 99;
            return userHierarchy < hierarchyLevel && user.is_active;
        });

        managerSelect.innerHTML = '<option value="">No Manager (Top Level)</option>';
        potentialManagers.forEach(user => {
            const roleLabel = user.role?.name || '';
            managerSelect.innerHTML += `<option value="${user.id}">${user.first_name} ${user.last_name} (${roleLabel})</option>`;
        });
    }

    // Load projects for filter dropdown
    async function loadProjectsFilter() {
        try {
            const response = await fetch('/api/v1/projects?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            projectsData = data.data || [];
            
            const filterProject = document.getElementById('filterProject');
            if (filterProject) {
                projectsData.forEach(project => {
                    filterProject.innerHTML += `<option value="${project.id}">${project.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    // Load departments for filter dropdown
    async function loadDepartmentsFilter() {
        try {
            const response = await fetch('/api/v1/departments?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            const departments = data.data || [];
            
            const filterDepartment = document.getElementById('filterDepartment');
            if (filterDepartment) {
                departments.forEach(dept => {
                    filterDepartment.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading departments:', error);
        }
    }

    async function loadRoles() {
        try {
            const response = await fetch('/api/v1/roles?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            rolesData = data.data || [];
            populateRoleDropdowns();
        } catch (error) {
            console.error('Error loading roles:', error);
        }
    }

    async function loadDepartments() {
        try {
            const response = await fetch('/api/v1/departments?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            return data.data || [];
        } catch (error) {
            console.error('Error loading departments:', error);
            return [];
        }
    }

    function populateRoleDropdowns() {
        const filterRole = document.getElementById('filterRole');
        const roleSelect = document.getElementById('roleSelect');
        
        rolesData.forEach(role => {
            filterRole.innerHTML += `<option value="${role.id}">${role.name}</option>`;
            roleSelect.innerHTML += `<option value="${role.id}">${role.name}</option>`;
        });
    }

    async function loadUsers(page = 1) {
        currentPage = page;
        showLoading();

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 15,
                ...(searchQuery && { search: searchQuery }),
                ...(roleFilter && { role_id: roleFilter }),
                ...(projectFilter && { project_id: projectFilter }),
                ...(departmentFilter && { department_id: departmentFilter }),
                ...(statusFilter !== '' && { is_active: statusFilter })
            });

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load users');

            const data = await response.json();
            usersData = data.data;
            renderUsers(data.data);
            renderPagination(data.meta);

            if (data.data.length === 0) {
                showEmptyState();
            } else {
                showTable();
            }
        } catch (error) {
            console.error('Error loading users:', error);
            alert('Failed to load users. Please try again.');
            hideLoading();
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        users.forEach(user => {
            // Get first project if available
            const firstProject = user.projects && user.projects.length > 0 ? user.projects[0] : null;
            const projectName = firstProject ? firstProject.name : '-';
            const projectCount = user.projects?.length || 0;
            const projectLabel = projectCount > 1 ? `${projectName} <span class="badge bg-secondary ms-1">+${projectCount - 1}</span>` : projectName;
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    ${user.avatar 
                        ? `<img src="${user.avatar}" alt="${user.first_name}" class="user-avatar">`
                        : `<div class="user-avatar-placeholder">${user.first_name?.charAt(0) || ''}${user.last_name?.charAt(0) || ''}</div>`
                    }
                </td>
                <td><strong>${user.first_name} ${user.last_name}</strong></td>
                <td>${user.email}</td>
                <td><span class="dept-badge">${user.department?.name || '-'}</span></td>
                <td><span class="role-badge">${user.role?.name || 'N/A'}</span></td>
                <td>${projectLabel}</td>
                <td>
                    <span class="badge-${user.is_active ? 'active' : 'inactive'}">
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm action-btn" onclick="viewUser(${user.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm action-btn" onclick="editUser(${user.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm action-btn text-danger" onclick="deleteUser(${user.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        document.getElementById('showingFrom').textContent = meta.from || 0;
        document.getElementById('showingTo').textContent = meta.to || 0;
        document.getElementById('totalRecords').textContent = meta.total || 0;

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${meta.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadUsers(${meta.current_page - 1}); return false;">Previous</a>`;
        pagination.appendChild(prevLi);

        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                const li = document.createElement('li');
                li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadUsers(${i}); return false;">${i}</a>`;
                pagination.appendChild(li);
            } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                li.innerHTML = '<a class="page-link">...</a>';
                pagination.appendChild(li);
            }
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadUsers(${meta.current_page + 1}); return false;">Next</a>`;
        pagination.appendChild(nextLi);
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordInput').required = true;
        document.querySelector('.password-required').style.display = 'inline';
        
        // Initialize cascade: Load projects first, then trigger department load
        populateProjectsInForm();
        new bootstrap.Modal(document.getElementById('userModal')).show();
    }

    async function populateProjectsInForm() {
        const select = document.getElementById('projectSelect');
        if (!select) return;
        
        select.innerHTML = '<option value="">-- None (Company Level) --</option>';
        
        try {
            const response = await fetch('/api/v1/projects?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) return;
            
            const data = await response.json();
            data.data.forEach(project => {
                select.innerHTML += `<option value="${project.id}">${project.name}</option>`;
            });
            
            // Initialize cascade: Load company-level departments (no project selected)
            await updateDepartmentsByProject('');
            resetRoleDropdown();
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    // Old duplicate functions removed - using new cascade functions defined above

    async function populateManagerDropdown() {
        const select = document.getElementById('managerSelect');
        select.innerHTML = '<option value="">No Manager</option>';
        
        try {
            const response = await fetch('/users?per_page=100&is_active=1', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            const currentUserId = document.getElementById('userId').value;
            
            data.data.forEach(user => {
                if (user.id != currentUserId) {
                    select.innerHTML += `<option value="${user.id}">${user.first_name} ${user.last_name}</option>`;
                }
            });
        } catch (error) {
            console.error('Error loading managers:', error);
        }
    }

    async function editUser(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            const user = data.data;

            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userId').value = user.id;
            document.getElementById('passwordInput').required = false;
            document.querySelector('.password-required').style.display = 'none';

            const form = document.getElementById('userForm');
            form.querySelector('[name="first_name"]').value = user.first_name || '';
            form.querySelector('[name="last_name"]').value = user.last_name || '';
            form.querySelector('[name="email"]').value = user.email || '';
            form.querySelector('[name="phone"]').value = user.phone || '';
            form.querySelector('[name="role_id"]').value = user.role_id || '';
            form.querySelector('[name="employee_code"]').value = user.employee_code || '';
            form.querySelector('[name="designation"]').value = user.designation || '';
            form.querySelector('[name="is_active"]').value = user.is_active ? '1' : '0';

            await populateProjectsInForm();
            if (user.department?.project_id) {
                form.querySelector('[name="project_id"]').value = user.department.project_id;
                await updateDepartmentsByProject(user.department.project_id);
            }
            form.querySelector('[name="department_id"]').value = user.department_id || '';

            await populateManagerDropdown();
            form.querySelector('[name="reports_to"]').value = user.reports_to || '';

            new bootstrap.Modal(document.getElementById('userModal')).show();
        } catch (error) {
            console.error('Error loading user:', error);
            alert('Failed to load user details.');
        }
    }

    async function saveUser() {
        const form = document.getElementById('userForm');
        const formData = new FormData(form);
        const userId = document.getElementById('userId').value;
        const isEdit = !!userId;

        // Convert to JSON
        const jsonData = {};
        formData.forEach((value, key) => {
            if (key !== 'avatar' && value !== '') {
                jsonData[key] = value;
            }
        });

        // Add company_id (from current user's company)
        if (!isEdit) {
            jsonData.company_id = <?php echo e(auth()->user()->company_id); ?>;
        }

        // Convert project_id to project_ids array for backend
        if (jsonData.project_id) {
            jsonData.project_ids = [jsonData.project_id];
            delete jsonData.project_id;
        }

        try {
            const url = isEdit ? `${API_BASE_URL}/${userId}` : API_BASE_URL;
            const method = isEdit ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(jsonData),
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to save user');
            }

            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            loadUsers(currentPage);
            alert(data.message || 'User saved successfully!');
        } catch (error) {
            console.error('Error saving user:', error);
            alert(error.message || 'Failed to save user.');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete user');
            }

            loadUsers(currentPage);
            alert(data.message || 'User deleted successfully!');
        } catch (error) {
            console.error('Error deleting user:', error);
            alert(error.message || 'Failed to delete user.');
        }
    }

    function viewUser(id) {
        window.location.href = `${API_BASE_URL}/${id}`;
    }

    function handleSearch(e) {
        searchQuery = e.target.value;
        loadUsers(1);
    }

    function handleFilterChange() {
        roleFilter = document.getElementById('filterRole').value;
        projectFilter = document.getElementById('filterProject').value;
        departmentFilter = document.getElementById('filterDepartment').value;
        statusFilter = document.getElementById('filterStatus').value;
        loadUsers(1);
    }

    function clearFilters() {
        document.getElementById('searchUsers').value = '';
        document.getElementById('filterRole').value = '';
        document.getElementById('filterProject').value = '';
        document.getElementById('filterDepartment').value = '';
        document.getElementById('filterStatus').value = '';
        searchQuery = '';
        roleFilter = '';
        projectFilter = '';
        departmentFilter = '';
        statusFilter = '';
        loadUsers(1);
    }

    async function loadProjects() {
        try {
            const response = await fetch('/projects?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            const filterProject = document.getElementById('filterProject');
            data.data.forEach(project => {
                filterProject.innerHTML += `<option value="${project.id}">${project.name}</option>`;
            });
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    function showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('tableContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }

    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
    }

    function showTable() {
        hideLoading();
        document.getElementById('tableContainer').style.display = 'block';
        document.getElementById('emptyState').style.display = 'none';
    }

    function showEmptyState() {
        hideLoading();
        document.getElementById('tableContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
    }

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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/users/index.blade.php ENDPATH**/ ?>