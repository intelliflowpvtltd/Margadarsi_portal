<?php $__env->startSection('title', 'Leads'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item active">Leads</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="leads-page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-1">
                <i class="bi bi-telephone me-2 text-gold"></i>
                Lead Management
            </h1>
            <p class="text-muted mb-0" style="font-size:0.875rem;">Track and manage your sales leads through the pipeline</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('leads.create')); ?>" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i>
                Full Form
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                <i class="bi bi-plus-circle me-1"></i>
                Quick Add
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-card stat-new">
            <div class="stat-value" id="statNew">0</div>
            <div class="stat-label">New</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-contacted">
            <div class="stat-value" id="statContacted">0</div>
            <div class="stat-label">Contacted</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-qualified">
            <div class="stat-value" id="statQualified">0</div>
            <div class="stat-label">Qualified</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-unreachable">
            <div class="stat-value" id="statUnreachable">0</div>
            <div class="stat-label">Unreachable</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-handedover">
            <div class="stat-value" id="statHandedOver">0</div>
            <div class="stat-label">Handed Over</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card stat-closed">
            <div class="stat-value" id="statClosed">0</div>
            <div class="stat-label">Closed</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card mb-4">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="search-box-large">
                <i class="bi bi-search search-icon-large"></i>
                <input type="text" class="search-input-large" id="searchLeads" placeholder="Search by name, mobile...">
            </div>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterProject">
                <option value="">All Projects</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterAssignee">
                <option value="">All Assignees</option>
                <option value="me">My Leads</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select form-select-premium" id="filterView">
                <option value="kanban">Kanban View</option>
                <option value="list">List View</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="btn-group w-100" role="group">
                <button class="btn btn-outline-secondary" id="btnClearFilters">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </button>
                <button class="btn btn-outline-primary" id="btnRefresh">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Kanban Board -->
<div id="kanbanView" class="kanban-board">
    <div class="kanban-column" data-status="new">
        <div class="kanban-column-header kanban-header-new">
            <h5 class="kanban-column-title">
                <i class="bi bi-circle-fill me-2"></i>
                New
                <span class="badge bg-light text-dark ms-2" id="countNew">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnNew">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="contacted">
        <div class="kanban-column-header kanban-header-contacted">
            <h5 class="kanban-column-title">
                <i class="bi bi-telephone-fill me-2"></i>
                Contacted
                <span class="badge bg-light text-dark ms-2" id="countContacted">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnContacted">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="unreachable">
        <div class="kanban-column-header kanban-header-unreachable">
            <h5 class="kanban-column-title">
                <i class="bi bi-telephone-x-fill me-2"></i>
                Unreachable
                <span class="badge bg-light text-dark ms-2" id="countUnreachable">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnUnreachable">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="qualified">
        <div class="kanban-column-header kanban-header-qualified">
            <h5 class="kanban-column-title">
                <i class="bi bi-check-circle-fill me-2"></i>
                Qualified
                <span class="badge bg-light text-dark ms-2" id="countQualified">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnQualified">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="handed_over">
        <div class="kanban-column-header kanban-header-handedover">
            <h5 class="kanban-column-title">
                <i class="bi bi-box-arrow-right me-2"></i>
                Handed Over
                <span class="badge bg-light text-dark ms-2" id="countHandedOver">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnHandedOver">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="not_qualified">
        <div class="kanban-column-header kanban-header-closed">
            <h5 class="kanban-column-title">
                <i class="bi bi-x-circle-fill me-2"></i>
                Not Qualified
                <span class="badge bg-light text-dark ms-2" id="countNotQualified">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnNotQualified">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>

    <div class="kanban-column" data-status="lost">
        <div class="kanban-column-header kanban-header-lost">
            <h5 class="kanban-column-title">
                <i class="bi bi-heartbreak-fill me-2"></i>
                Lost
                <span class="badge bg-light text-dark ms-2" id="countLost">0</span>
            </h5>
        </div>
        <div class="kanban-column-body" id="columnLost">
            <div class="kanban-loading">
                <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                <span class="ms-2">Loading...</span>
            </div>
        </div>
    </div>
</div>

<!-- Lead Detail Modal -->
<div class="modal fade" id="leadDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="leadDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-gold" role="status"></div>
                    <p class="mt-3">Loading lead details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title">Add New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addLeadForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile *</label>
                            <input type="tel" class="form-control" name="mobile" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project *</label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Select Project</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lead Source</label>
                            <select class="form-select" name="lead_source_id" id="leadSourceSelect">
                                <option value="">Select Source</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source Campaign</label>
                            <input type="text" class="form-control" name="source_campaign" placeholder="e.g. Facebook Ads March">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" value="Telangana">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Requirements</label>
                            <textarea class="form-control" name="requirements_notes" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveLead">
                    <i class="bi bi-check-circle me-2"></i>Save Lead
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Page Header */
    .leads-page-header {
        margin-bottom: 1rem;
    }
    .leads-page-header .page-title {
        font-size: 1.5rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 0.75rem 0.5rem;
        text-align: center;
        border: 2px solid;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        font-family: var(--font-primary);
        line-height: 1.2;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--color-text-secondary);
        margin-top: 0.125rem;
    }

    .stat-new { border-color: #3B82F6; }
    .stat-new .stat-value { color: #3B82F6; }
    .stat-contacted { border-color: #8B5CF6; }
    .stat-contacted .stat-value { color: #8B5CF6; }
    .stat-qualified { border-color: #10B981; }
    .stat-qualified .stat-value { color: #10B981; }
    .stat-unreachable { border-color: #F59E0B; }
    .stat-unreachable .stat-value { color: #F59E0B; }
    .stat-handedover { border-color: #22C55E; }
    .stat-handedover .stat-value { color: #22C55E; }
    .stat-closed { border-color: #6B7280; }
    .stat-closed .stat-value { color: #6B7280; }

    /* Filter Card */
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.15);
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    .search-box-large {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon-large {
        position: absolute;
        left: 0.75rem;
        color: var(--color-text-muted);
        font-size: 1rem;
    }

    .search-input-large {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .search-input-large:focus {
        outline: none;
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .form-select-premium {
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .form-select-premium:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    /* Kanban Board */
    .kanban-board {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        /* Fill available height in content area */
        height: calc(100vh - 340px);
        min-height: 350px;
    }

    .kanban-column {
        flex: 0 0 260px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .kanban-column-header {
        padding: 0.75rem 1rem;
        border-radius: 10px 10px 0 0;
        border-bottom: 2px solid rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .kanban-column-title {
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        color: white;
    }

    .kanban-header-new { background: linear-gradient(135deg, #3B82F6, #2563EB); }
    .kanban-header-contacted { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .kanban-header-unreachable { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .kanban-header-qualified { background: linear-gradient(135deg, #10B981, #059669); }
    .kanban-header-handedover { background: linear-gradient(135deg, #22C55E, #16A34A); }
    .kanban-header-closed { background: linear-gradient(135deg, #6B7280, #4B5563); }

    .kanban-column-body {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        background: white;
        border-radius: 0 0 10px 10px;
    }

    .kanban-column-body::-webkit-scrollbar {
        width: 6px;
    }

    .kanban-column-body::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
    }

    .kanban-column-body::-webkit-scrollbar-thumb {
        background: rgba(184, 149, 106, 0.3);
        border-radius: 3px;
    }

    .kanban-loading {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--color-text-muted);
        font-size: 0.875rem;
    }

    .kanban-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--color-text-muted);
        font-size: 0.875rem;
    }

    .kanban-empty i {
        font-size: 2rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 0.5rem;
    }

    /* Lead Card */
    .lead-card {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .lead-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.2);
        border-color: var(--color-coffee-gold);
    }

    .lead-card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.5rem;
    }

    .lead-name {
        font-weight: 600;
        color: var(--color-dark-maroon);
        font-size: 0.9rem;
        margin-bottom: 0.125rem;
    }

    .lead-mobile {
        font-size: 0.8rem;
        color: var(--color-text-secondary);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .lead-project {
        font-size: 0.7rem;
        color: var(--color-coffee-gold-dark);
        margin-bottom: 0.25rem;
    }

    .lead-meta {
        display: flex;
        gap: 0.5rem;
        font-size: 0.7rem;
        color: var(--color-text-muted);
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .lead-meta-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .sla-badge {
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
    }

    .sla-breach { background: #FEE2E2; color: #DC2626; }
    .sla-ok { background: #D1FAE5; color: #059669; }

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

    /* Lost column */
    .kanban-header-lost { background: linear-gradient(135deg, #FEE2E2, #FECACA); }
    .kanban-header-lost .kanban-column-title { color: #991B1B; }

    /* Temperature badge */
    .temp-badge {
        display: inline-block;
        font-size: 0.6rem;
        font-weight: 600;
        color: #fff;
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        line-height: 1.4;
    }

    /* Responsive adjustments */
    @media (max-width: 1400px) {
        .kanban-column { flex: 0 0 230px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const API_BASE_URL = '/api/v1/leads';
    let currentFilters = {
        search: '',
        project_id: '',
        assignee_id: '',
    };

    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        loadLeads();
        loadProjects();
        loadLeadSources();

        // Event listeners
        document.getElementById('searchLeads').addEventListener('input', debounce(handleSearch, 500));
        document.getElementById('filterProject').addEventListener('change', handleFilterChange);
        document.getElementById('filterAssignee').addEventListener('change', handleFilterChange);
        document.getElementById('btnClearFilters').addEventListener('click', clearFilters);
        document.getElementById('btnRefresh').addEventListener('click', () => {
            loadStatistics();
            loadLeads();
        });
        document.getElementById('btnSaveLead').addEventListener('click', saveLead);
    });

    async function loadStatistics() {
        try {
            const response = await fetch(`${API_BASE_URL}/statistics`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                document.getElementById('statNew').textContent = data.data.by_status.new || 0;
                document.getElementById('statContacted').textContent = data.data.by_status.contacted || 0;
                document.getElementById('statQualified').textContent = data.data.by_status.qualified || 0;
                document.getElementById('statUnreachable').textContent = data.data.by_status.unreachable || 0;
                document.getElementById('statHandedOver').textContent = data.data.by_status.handed_over || 0;
                document.getElementById('statClosed').textContent = data.data.by_status.not_qualified || 0;
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    async function loadLeads() {
        const statuses = ['new', 'contacted', 'unreachable', 'qualified', 'handed_over', 'not_qualified', 'lost'];

        // Load all columns in parallel for speed
        await Promise.all(statuses.map(status => loadLeadsByStatus(status)));
    }

    const COLUMN_ID_MAP = {
        'new': 'columnNew',
        'contacted': 'columnContacted',
        'unreachable': 'columnUnreachable',
        'qualified': 'columnQualified',
        'handed_over': 'columnHandedOver',
        'not_qualified': 'columnNotQualified',
        'lost': 'columnLost',
    };

    const COUNT_ID_MAP = {
        'new': 'countNew',
        'contacted': 'countContacted',
        'unreachable': 'countUnreachable',
        'qualified': 'countQualified',
        'handed_over': 'countHandedOver',
        'not_qualified': 'countNotQualified',
        'lost': 'countLost',
    };

    async function loadLeadsByStatus(status) {
        const column = document.getElementById(COLUMN_ID_MAP[status]);
        if (!column) return;

        try {
            const params = new URLSearchParams({
                status: status,
                per_page: 50,
                ...currentFilters
            });

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            
            if (data.success) {
                renderLeadsInColumn(column, data.data, status);
                updateColumnCount(status, data.data.length);
            }
        } catch (error) {
            console.error(`Error loading ${status} leads:`, error);
            column.innerHTML = '<div class="kanban-empty"><i class="bi bi-exclamation-circle"></i>Error loading leads</div>';
        }
    }

    function renderLeadsInColumn(column, leads, status) {
        if (leads.length === 0) {
            column.innerHTML = '<div class="kanban-empty"><i class="bi bi-inbox"></i>No leads</div>';
            return;
        }

        const TEMP_COLORS = { hot: '#EF4444', warm: '#F59E0B', cold: '#3B82F6' };

        column.innerHTML = leads.map(lead => {
            const tempTag = lead.temperature_tag;
            const tempBadge = tempTag 
                ? `<span class="temp-badge" style="background:${TEMP_COLORS[tempTag.slug] || '#6B7280'}">${tempTag.name}</span>` 
                : '';
            const slaBadge = lead.sla_breached ? '<span class="sla-badge sla-breach">SLA!</span>' : '';
            const source = lead.lead_source?.name || '';
            const age = timeAgo(lead.created_at);

            return `
            <div class="lead-card" onclick="window.location.href='/leads/${lead.id}'">
                <div class="lead-card-header">
                    <div style="min-width:0">
                        <div class="lead-name">${lead.name}</div>
                        <div class="lead-mobile">
                            <i class="bi bi-telephone"></i>
                            ${lead.mobile}
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        ${tempBadge}${slaBadge}
                    </div>
                </div>
                <div class="lead-project">
                    <i class="bi bi-building me-1"></i>${lead.project?.name || 'N/A'}
                    ${source ? `<span class="text-muted ms-2" style="font-size:0.7rem"><i class="bi bi-funnel me-1"></i>${source}</span>` : ''}
                </div>
                <div class="lead-meta">
                    <div class="lead-meta-item">
                        <i class="bi bi-telephone"></i>
                        ${lead.call_attempts || 0} calls
                    </div>
                    <div class="lead-meta-item">
                        <i class="bi bi-person"></i>
                        ${lead.current_assignee?.first_name || 'Unassigned'}
                    </div>
                    <div class="lead-meta-item ms-auto">
                        <i class="bi bi-clock"></i>
                        ${age}
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    function updateColumnCount(status, count) {
        const countEl = document.getElementById(COUNT_ID_MAP[status]);
        if (countEl) countEl.textContent = count;
    }

    function timeAgo(dateStr) {
        if (!dateStr) return '';
        const seconds = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
        if (seconds < 60) return 'now';
        const mins = Math.floor(seconds / 60);
        if (mins < 60) return `${mins}m`;
        const hrs = Math.floor(mins / 60);
        if (hrs < 24) return `${hrs}h`;
        const days = Math.floor(hrs / 24);
        return `${days}d`;
    }

    async function viewLead(id) {
        const modal = new bootstrap.Modal(document.getElementById('leadDetailModal'));
        modal.show();

        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                renderLeadDetail(data.data, data.workflow);
            }
        } catch (error) {
            console.error('Error loading lead:', error);
        }
    }

    function renderLeadDetail(lead, workflow) {
        document.getElementById('leadDetailContent').innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <h4>${lead.name}</h4>
                    <p class="text-muted">${lead.mobile} ${lead.email ? '| ' + lead.email : ''}</p>
                    <p><strong>Project:</strong> ${lead.project?.name}</p>
                    <p><strong>Status:</strong> ${workflow.status_label}</p>
                    <p><strong>Stage:</strong> ${workflow.stage_label}</p>
                    <p><strong>Assigned to:</strong> ${lead.current_assignee?.first_name || 'Unassigned'}</p>
                </div>
                <div class="col-md-4">
                    <h6>Quick Actions</h6>
                    <button class="btn btn-sm btn-primary w-100 mb-2">
                        <i class="bi bi-telephone"></i> Log Call
                    </button>
                    <button class="btn btn-sm btn-success w-100 mb-2">
                        <i class="bi bi-check-circle"></i> Mark Qualified
                    </button>
                </div>
            </div>
        `;
    }

    async function loadProjects() {
        try {
            const response = await fetch('/api/v1/projects?per_page=100', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (data.success) {
                const projectSelects = document.querySelectorAll('select[name="project_id"], #filterProject');
                projectSelects.forEach(select => {
                    if (select.id !== 'filterProject') {
                        select.innerHTML = '<option value="">Select Project</option>' + 
                            data.data.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
                    } else {
                        select.innerHTML = '<option value="">All Projects</option>' + 
                            data.data.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
                    }
                });
            }
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    async function loadLeadSources() {
        try {
            const response = await fetch('/api/v1/lead-sources', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.success || data.data) {
                const sources = data.data || data;
                const select = document.getElementById('leadSourceSelect');
                if (select) {
                    select.innerHTML = '<option value="">Select Source</option>' +
                        (Array.isArray(sources) ? sources : []).map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                }
            }
        } catch (error) {
            console.error('Error loading lead sources:', error);
        }
    }

    async function saveLead() {
        const form = document.getElementById('addLeadForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch(API_BASE_URL, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    ...data,
                    company_id: <?php echo e(auth()->user()->company_id); ?>

                })
            });

            const result = await response.json();
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('addLeadModal')).hide();
                form.reset();
                loadStatistics();
                loadLeads();
                showToast('Lead created successfully', 'success');
            } else {
                showToast(result.message || 'Error creating lead', 'danger');
            }
        } catch (error) {
            console.error('Error saving lead:', error);
            showToast('Error creating lead', 'danger');
        }
    }

    function handleSearch(e) {
        currentFilters.search = e.target.value;
        loadLeads();
    }

    function handleFilterChange(e) {
        if (e.target.id === 'filterProject') {
            currentFilters.project_id = e.target.value;
        } else if (e.target.id === 'filterAssignee') {
            currentFilters.assignee_id = e.target.value === 'me' ? <?php echo e(auth()->id()); ?> : e.target.value;
        }
        loadLeads();
    }

    function clearFilters() {
        currentFilters = { search: '', project_id: '', assignee_id: '' };
        document.getElementById('searchLeads').value = '';
        document.getElementById('filterProject').value = '';
        document.getElementById('filterAssignee').value = '';
        loadLeads();
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/leads/index.blade.php ENDPATH**/ ?>