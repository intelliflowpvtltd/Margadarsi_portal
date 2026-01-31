@extends('layouts.app')

@section('title', 'Projects')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Projects</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-building me-2 text-gold"></i>
                Projects
            </h1>
            <p class="text-muted mb-0">Manage your real estate projects and developments</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Add Project
        </a>
    </div>
</div>

<!-- Filters & Search -->
<div class="filter-card mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="search-box-large">
                <i class="bi bi-search search-icon-large"></i>
                <input type="text" class="search-input-large" id="searchProjects" placeholder="Search by name, city, or RERA number...">
            </div>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterCompany">
                <option value="">All Companies</option>
                @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterType">
                <option value="">All Types</option>
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
                <option value="villa">Villa</option>
                <option value="open_plots">Open Plots</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterStatus">
                <option value="">All Status</option>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
                <option value="sold_out">Sold Out</option>
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

<!-- Projects Grid -->
<div class="premium-card">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3">Loading projects...</p>
    </div>

    <!-- Projects Grid Container -->
    <div id="gridContainer" style="display: none;">
        <div class="row g-4" id="projectsGrid">
            <!-- Project cards will be inserted via JavaScript -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> projects
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
            <i class="bi bi-building"></i>
        </div>
        <h4 class="mt-3">No Projects Found</h4>
        <p class="text-muted">Start by adding your first project</p>
        <a href="{{ route('projects.create') }}" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle me-2"></i>
            Add Project
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
                <p>Are you sure you want to delete this project?</p>
                <p class="text-muted mb-0">This action can be undone later by restoring the project.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Delete Project</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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

    /* Project Card */
    .project-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(184, 149, 106, 0.2);
    }

    .project-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
    }

    .project-card-placeholder {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 700;
    }

    .project-card-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .project-card-title {
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

    .project-card-company {
        font-size: 0.875rem;
        color: var(--color-coffee-gold-dark);
        margin-bottom: 0.75rem;
        text-decoration: none;
        display: inline-block;
    }

    .project-card-company:hover {
        color: var(--color-dark-maroon);
    }

    .project-badges {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    /* Type Badges */
    .badge-type-residential {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-type-commercial {
        background: linear-gradient(135deg, #6f42c1, #5a32a3);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-type-villa {
        background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-type-open_plots {
        background: linear-gradient(135deg, #795548, #5d4037);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Status Badges */
    .badge-status-upcoming {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status-ongoing {
        background: linear-gradient(135deg, #fd7e14, #ca6510);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status-completed {
        background: linear-gradient(135deg, #198754, #146c43);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status-sold_out {
        background: linear-gradient(135deg, #dc3545, #b02a37);
        color: white;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .project-location {
        color: var(--color-text-secondary);
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }

    .project-location i {
        color: var(--color-coffee-gold);
    }

    .project-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        margin-top: auto;
        padding-top: 1rem;
        border-top: 1px solid rgba(184, 149, 106, 0.15);
    }

    .project-stat {
        text-align: center;
    }

    .project-stat-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-dark-maroon);
    }

    .project-stat-label {
        font-size: 0.75rem;
        color: var(--color-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .project-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
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
    }

    .btn-action:hover {
        background: var(--color-coffee-gold);
        color: white;
        border-color: var(--color-coffee-gold);
    }

    .btn-action.btn-danger:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
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
@endpush

@push('scripts')
<script>
    // API Base URL
    const API_BASE_URL = '/projects';

    // Current page and filters
    let currentPage = 1;
    let searchQuery = '';
    let companyFilter = '';
    let typeFilter = '';
    let statusFilter = '';
    let projectToDelete = null;

    // Load projects on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadProjects();

        // Event listeners
        document.getElementById('searchProjects').addEventListener('input', debounce(handleSearch, 500));
        document.getElementById('filterCompany').addEventListener('change', handleFilterChange);
        document.getElementById('filterType').addEventListener('change', handleFilterChange);
        document.getElementById('filterStatus').addEventListener('change', handleFilterChange);
        document.getElementById('btnClearFilters').addEventListener('click', clearFilters);
        document.getElementById('btnConfirmDelete').addEventListener('click', confirmDelete);
    });

    // Load projects from API
    async function loadProjects(page = 1) {
        currentPage = page;
        showLoading();

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 12,
                ...(searchQuery && {
                    search: searchQuery
                }),
                ...(companyFilter && {
                    company_id: companyFilter
                }),
                ...(typeFilter && {
                    type: typeFilter
                }),
                ...(statusFilter && {
                    status: statusFilter
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
                throw new Error('Failed to load projects');
            }

            const data = await response.json();
            renderProjects(data.data);
            renderPagination(data.meta);

            if (data.data.length === 0) {
                showEmptyState();
            } else {
                showGrid();
            }
        } catch (error) {
            console.error('Error loading projects:', error);
            alert('Failed to load projects. Please try again.');
            hideLoading();
        }
    }

    // Render projects as cards
    function renderProjects(projects) {
        const grid = document.getElementById('projectsGrid');
        grid.innerHTML = '';

        projects.forEach(project => {
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4 col-xl-3';

            const typeBadgeClass = `badge-type-${project.type}`;
            const statusBadgeClass = `badge-status-${project.status}`;
            const statusLabel = {
                'upcoming': 'Upcoming',
                'ongoing': 'Ongoing',
                'completed': 'Completed',
                'sold_out': 'Sold Out'
            } [project.status] || project.status;

            const typeLabel = {
                'residential': 'Residential',
                'commercial': 'Commercial',
                'villa': 'Villa',
                'open_plots': 'Open Plots'
            } [project.type] || project.type;

            col.innerHTML = `
                <div class="project-card">
                    ${project.logo 
                        ? `<img src="${project.logo}" alt="${project.name}" class="project-card-image">`
                        : `<div class="project-card-placeholder">${project.name.charAt(0)}</div>`
                    }
                    <div class="project-card-body">
                        <h3 class="project-card-title">${project.name}</h3>
                        <a href="/companies/${project.company?.id}" class="project-card-company">
                            <i class="bi bi-building me-1"></i>${project.company?.name || 'N/A'}
                        </a>
                        <div class="project-badges">
                            <span class="${typeBadgeClass}">${typeLabel}</span>
                            <span class="${statusBadgeClass}">${statusLabel}</span>
                        </div>
                        <div class="project-location">
                            <i class="bi bi-geo-alt me-1"></i>${project.city}, ${project.state}
                        </div>
                        <div class="project-stats">
                            <div class="project-stat">
                                <div class="project-stat-value">${project.towers_count || 0}</div>
                                <div class="project-stat-label">Towers</div>
                            </div>
                            <div class="project-stat">
                                <div class="project-stat-value">${project.units_count || 0}</div>
                                <div class="project-stat-label">Units</div>
                            </div>
                            <div class="project-stat">
                                <div class="project-stat-value">${project.completion_percentage || 0}%</div>
                                <div class="project-stat-label">Complete</div>
                            </div>
                        </div>
                        <div class="project-actions">
                            <button class="btn-action" onclick="viewProject(${project.id})">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <button class="btn-action" onclick="editProject(${project.id})">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn-action btn-danger" onclick="deleteProject(${project.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            grid.appendChild(col);
        });
    }

    // Render pagination
    function renderPagination(meta) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';

        // Update showing text
        document.getElementById('showingFrom').textContent = meta.from || 0;
        document.getElementById('showingTo').textContent = meta.to || 0;
        document.getElementById('totalRecords').textContent = meta.total || 0;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${meta.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadProjects(${meta.current_page - 1}); return false;">Previous</a>`;
        pagination.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                const li = document.createElement('li');
                li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadProjects(${i}); return false;">${i}</a>`;
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
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadProjects(${meta.current_page + 1}); return false;">Next</a>`;
        pagination.appendChild(nextLi);
    }

    // View project
    function viewProject(id) {
        window.location.href = `/projects/${id}`;
    }

    // Edit project
    function editProject(id) {
        window.location.href = `/projects/${id}/edit`;
    }

    // Delete project
    function deleteProject(id) {
        projectToDelete = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Confirm delete
    async function confirmDelete() {
        if (!projectToDelete) return;

        try {
            const response = await fetch(`${API_BASE_URL}/${projectToDelete}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to delete project');
            }

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();

            // Reload projects
            loadProjects(currentPage);

            // Show success message
            showToast('Project deleted successfully', 'success');
        } catch (error) {
            console.error('Error deleting project:', error);
            alert('Failed to delete project. Please try again.');
        }
    }

    // Search handler
    function handleSearch(e) {
        searchQuery = e.target.value;
        loadProjects(1);
    }

    // Filter change handler
    function handleFilterChange(e) {
        if (e.target.id === 'filterCompany') {
            companyFilter = e.target.value;
        } else if (e.target.id === 'filterType') {
            typeFilter = e.target.value;
        } else if (e.target.id === 'filterStatus') {
            statusFilter = e.target.value;
        }
        loadProjects(1);
    }

    // Clear filters
    function clearFilters() {
        searchQuery = '';
        companyFilter = '';
        typeFilter = '';
        statusFilter = '';
        document.getElementById('searchProjects').value = '';
        document.getElementById('filterCompany').value = '';
        document.getElementById('filterType').value = '';
        document.getElementById('filterStatus').value = '';
        loadProjects(1);
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
        toast.textContent = message;
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
</script>
@endpush