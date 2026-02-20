<?php $__env->startSection('title', 'Companies'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item active">Companies</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-building me-2 text-gold"></i>
                Companies
            </h1>
            <p class="text-muted mb-0">Manage your company information and settings</p>
        </div>
        <button class="btn btn-maroon" id="btnAddCompany">
            <i class="bi bi-plus-circle me-2"></i>
            Add Company
        </button>
    </div>
</div>

<!-- Filters & Search -->
<div class="filter-card mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="search-box-large">
                <i class="bi bi-search search-icon-large"></i>
                <input type="text" class="search-input-large" id="searchCompanies" placeholder="Search by name, email, or legal name...">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select form-select-premium" id="filterStatus">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
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

<!-- Companies Table -->
<div class="premium-card">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-gold" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-3">Loading companies...</p>
    </div>

    <!-- Table -->
    <div id="tableContainer" style="display: none;">
        <div class="table-responsive">
            <table class="table company-table">
                <thead>
                    <tr>
                        <th width="80">Logo</th>
                        <th>Company Name</th>
                        <th>Legal Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th width="100">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody id="companiesTableBody">
                    <!-- Rows will be inserted via JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> companies
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
        <h4 class="mt-3">No Companies Found</h4>
        <p class="text-muted">Start by adding your first company</p>
        <button class="btn btn-primary mt-3" onclick="document.getElementById('btnAddCompany').click()">
            <i class="bi bi-plus-circle me-2"></i>
            Add Company
        </button>
    </div>
</div>

<!-- Create/Edit Company Modal -->
<div class="modal fade" id="companyModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs nav-tabs-premium mb-4" id="companyTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basicInfo">
                            <i class="bi bi-info-circle me-2"></i>Basic Info
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#registration">
                            <i class="bi bi-file-text me-2"></i>Registration
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contact">
                            <i class="bi bi-telephone me-2"></i>Contact
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#address">
                            <i class="bi bi-geo-alt me-2"></i>Addresses
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#social">
                            <i class="bi bi-share me-2"></i>Social Media
                        </button>
                    </li>
                </ul>

                <form id="companyForm" enctype="multipart/form-data">
                    <input type="hidden" id="companyId" name="id">

                    <div class="tab-content">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basicInfo">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Legal Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="legal_name" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tagline</label>
                                    <input type="text" class="form-control" name="tagline" placeholder="Your company's catchy tagline">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="4" placeholder="Brief description about your company"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Company Logo</label>
                                    <input type="file" class="form-control" name="logo" accept="image/*">
                                    <small class="text-muted">Recommended: 200x200px, PNG or JPG</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Favicon</label>
                                    <input type="file" class="form-control" name="favicon" accept="image/*">
                                    <small class="text-muted">Recommended: 32x32px, ICO or PNG</small>
                                </div>
                            </div>
                        </div>

                        <!-- Registration Tab -->
                        <div class="tab-pane fade" id="registration">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" class="form-control text-uppercase" name="pan_number" maxlength="10" placeholder="ABCDE1234F">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">GSTIN</label>
                                    <input type="text" class="form-control text-uppercase" name="gstin" maxlength="15" placeholder="22AAAAA0000A1Z5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">CIN (Corporate Identification Number)</label>
                                    <input type="text" class="form-control text-uppercase" name="cin" maxlength="21">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">RERA Number</label>
                                    <input type="text" class="form-control" name="rera_number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Incorporation Date</label>
                                    <input type="date" class="form-control" name="incorporation_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="is_active">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Tab -->
                        <div class="tab-pane fade" id="contact">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alternate Phone</label>
                                    <input type="tel" class="form-control" name="alternate_phone">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="tel" class="form-control" name="whatsapp">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Website</label>
                                    <input type="url" class="form-control" name="website" placeholder="https://www.example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Address Tab -->
                        <div class="tab-pane fade" id="address">
                            <h6 class="mb-3 text-maroon">Registered Office Address</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" class="form-control" name="registered_address_line1">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control" name="registered_address_line2">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="registered_city">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="registered_state">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="registered_pincode" maxlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" name="registered_country" value="India">
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="mb-3 text-maroon">Corporate Office Address</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" class="form-control" name="corporate_address_line1">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control" name="corporate_address_line2">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="corporate_city">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="corporate_state">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="corporate_pincode" maxlength="6">
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Tab -->
                        <div class="tab-pane fade" id="social">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-facebook me-2"></i>Facebook URL
                                    </label>
                                    <input type="url" class="form-control" name="facebook_url" placeholder="https://facebook.com/yourcompany">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-instagram me-2"></i>Instagram URL
                                    </label>
                                    <input type="url" class="form-control" name="instagram_url" placeholder="https://instagram.com/yourcompany">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-linkedin me-2"></i>LinkedIn URL
                                    </label>
                                    <input type="url" class="form-control" name="linkedin_url" placeholder="https://linkedin.com/company/yourcompany">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-twitter me-2"></i>Twitter URL
                                    </label>
                                    <input type="url" class="form-control" name="twitter_url" placeholder="https://twitter.com/yourcompany">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="bi bi-youtube me-2"></i>YouTube URL
                                    </label>
                                    <input type="url" class="form-control" name="youtube_url" placeholder="https://youtube.com/@yourcompany">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-maroon" id="btnSaveCompany">
                    <i class="bi bi-check-circle me-2"></i>
                    <span id="btnSaveText">Save Company</span>
                </button>
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

    /* Custom Dark Maroon Button */
    .btn-maroon {
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-maroon-light));
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
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
        box-shadow: 0 2px 6px rgba(128, 0, 32, 0.2);
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

    /* Company Table */
    .company-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .company-table thead th {
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.05), rgba(184, 149, 106, 0.05));
        color: var(--color-dark-maroon);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .company-table thead th:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .company-table thead th:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .company-table tbody tr {
        background: white;
        transition: all 0.2s ease;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .company-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(184, 149, 106, 0.15);
    }

    .company-table tbody td {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    .company-table tbody td:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .company-table tbody td:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    /* Company Logo in Table */
    .company-logo {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(184, 149, 106, 0.2);
    }

    .company-logo-placeholder {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--color-coffee-gold), var(--color-coffee-gold-dark));
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Status Badges */
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

    /* Action Dropdown */
    .action-dropdown {
        position: relative;
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

    /* Nav Tabs Premium */
    .nav-tabs-premium {
        border-bottom: 2px solid rgba(184, 149, 106, 0.2);
    }

    .nav-tabs-premium .nav-link {
        border: none;
        color: var(--color-text-secondary);
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .nav-tabs-premium .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--color-coffee-gold);
        transform: scaleX(0);
        transition: transform 0.2s ease;
    }

    .nav-tabs-premium .nav-link:hover {
        color: var(--color-coffee-gold);
    }

    .nav-tabs-premium .nav-link.active {
        color: var(--color-dark-maroon);
        font-weight: 600;
    }

    .nav-tabs-premium .nav-link.active::after {
        transform: scaleX(1);
    }

    /* Form Enhancements */
    .form-control {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .form-select {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .text-maroon {
        color: var(--color-dark-maroon);
    }

    .text-gold {
        color: var(--color-coffee-gold);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // API Base URL - Using web routes with session auth
    const API_BASE_URL = '/companies';

    // Current page and filters
    let currentPage = 1;
    let searchQuery = '';
    let statusFilter = '';

    // Load companies on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCompanies();

        // Event listeners
        document.getElementById('btnAddCompany').addEventListener('click', openCreateModal);
        document.getElementById('btnSaveCompany').addEventListener('click', saveCompany);
        document.getElementById('searchCompanies').addEventListener('input', debounce(handleSearch, 500));
        document.getElementById('filterStatus').addEventListener('change', handleFilterChange);
        document.getElementById('btnClearFilters').addEventListener('click', clearFilters);
    });

    // Load companies from API
    async function loadCompanies(page = 1) {
        currentPage = page;
        showLoading();

        try {
            const params = new URLSearchParams({
                page: currentPage,
                per_page: 15,
                ...(searchQuery && {
                    search: searchQuery
                }),
                ...(statusFilter !== '' && {
                    is_active: statusFilter
                })
            });

            const response = await fetch(`${API_BASE_URL}?${params}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to load companies');
            }

            const data = await response.json();
            renderCompanies(data.data);
            renderPagination(data.meta);

            if (data.data.length === 0) {
                showEmptyState();
            } else {
                showTable();
            }
        } catch (error) {
            console.error('Error loading companies:', error);
            alert('Failed to load companies. Please try again.');
            hideLoading();
        }
    }

    // Render companies in table
    function renderCompanies(companies) {
        const tbody = document.getElementById('companiesTableBody');
        tbody.innerHTML = '';

        companies.forEach(company => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                ${company.logo 
                    ? `<img src="${company.logo}" alt="${company.name}" class="company-logo">`
                    : `<div class="company-logo-placeholder">${company.name.charAt(0)}</div>`
                }
            </td>
            <td><strong>${company.name}</strong></td>
            <td>${company.legal_name || '-'}</td>
            <td>${company.email || '-'}</td>
            <td>${company.phone || '-'}</td>
            <td>
                <span class="badge-${company.is_active ? 'active' : 'inactive'}">
                    ${company.is_active ? 'Active' : 'Inactive'}
                </span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm action-btn" onclick="viewCompany(${company.id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm action-btn" onclick="editCompany(${company.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm action-btn text-danger" onclick="deleteCompany(${company.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
            tbody.appendChild(row);
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
        prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadCompanies(${meta.current_page - 1}); return false;">Previous</a>`;
        pagination.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                const li = document.createElement('li');
                li.className = `page-item ${i === meta.current_page ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadCompanies(${i}); return false;">${i}</a>`;
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
        nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadCompanies(${meta.current_page + 1}); return false;">Next</a>`;
        pagination.appendChild(nextLi);
    }

    // Open create modal
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New Company';
        document.getElementById('companyForm').reset();
        document.getElementById('companyId').value = '';
        document.getElementById('btnSaveText').textContent = 'Save Company';
        new bootstrap.Modal(document.getElementById('companyModal')).show();
    }

    // Save company
    async function saveCompany() {
        const form = document.getElementById('companyForm');
        const formData = new FormData(form);
        const companyId = document.getElementById('companyId').value;


        // FormData now includes all fields including files

        try {
            const url = companyId ? `${API_BASE_URL}/${companyId}` : API_BASE_URL;
            const method = companyId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: formData  // Send FormData directly, browser sets Content-Type automatically
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to save company');
            }

            const result = await response.json();
            alert(result.message);
            bootstrap.Modal.getInstance(document.getElementById('companyModal')).hide();
            loadCompanies(currentPage);
        } catch (error) {
            console.error('Error saving company:', error);
            alert('Failed to save company: ' + error.message);
        }
    }

    // Edit company
    async function editCompany(id) {
        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to load company');

            const result = await response.json();
            const company = result.data;

            // Fill form
            document.getElementById('modalTitle').textContent = 'Edit Company';
            document.getElementById('companyId').value = company.id;
            document.getElementById('btnSaveText').textContent = 'Update Company';

            // Fill all fields except file inputs (can't set file input values programmatically)
            Object.keys(company).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input && company[key] !== null && input.type !== 'file') {
                    input.value = company[key];
                }
            });

            new bootstrap.Modal(document.getElementById('companyModal')).show();
        } catch (error) {
            console.error('Error loading company:', error);
            alert('Failed to load company details');
        }
    }

    // Delete company
    async function deleteCompany(id) {
        if (!confirm('Are you sure you want to delete this company?')) return;

        try {
            const response = await fetch(`${API_BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to delete company');

            const result = await response.json();
            alert(result.message);
            loadCompanies(currentPage);
        } catch (error) {
            console.error('Error deleting company:', error);
            alert('Failed to delete company');
        }
    }

    // View company (placeholder - will implement show page later)
    function viewCompany(id) {
        window.location.href = `/companies/${id}`;
    }

    // Handle search
    function handleSearch(e) {
        searchQuery = e.target.value;
        loadCompanies(1);
    }

    // Handle filter change
    function handleFilterChange(e) {
        statusFilter = e.target.value;
        loadCompanies(1);
    }

    // Clear filters
    function clearFilters() {
        searchQuery = '';
        statusFilter = '';
        document.getElementById('searchCompanies').value = '';
        document.getElementById('filterStatus').value = '';
        loadCompanies(1);
    }

    // UI state functions
    function showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('tableContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'none';
    }

    function showTable() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('tableContainer').style.display = 'block';
        document.getElementById('emptyState').style.display = 'none';
    }

    function showEmptyState() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('tableContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
    }

    function hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
    }

    // Debounce helper
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/companies/index.blade.php ENDPATH**/ ?>