@extends('layouts.app')

@section('title', 'Create Lead')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">Create Lead</li>
@endsection

@section('content')
<div class="create-lead-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title mb-1" style="font-size:1.5rem;">
                <i class="bi bi-person-plus me-2 text-gold"></i>Create New Lead
            </h1>
            <p class="text-muted mb-0" style="font-size:0.875rem;">Fill in the details to add a new lead to the pipeline</p>
        </div>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Leads
        </a>
    </div>

    <!-- Form Card -->
    <form id="createLeadForm" novalidate>
        <div class="row g-3">
            <!-- Left Column: Lead Details -->
            <div class="col-lg-8">
                <!-- Contact Information -->
                <div class="premium-card mb-3">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-person me-2"></i>Contact Information</h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g. Ravi Kumar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="mobile" required placeholder="e.g. 9876543210" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternate Mobile</label>
                                <input type="tel" class="form-control" name="alt_mobile" placeholder="Optional" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="tel" class="form-control" name="whatsapp" placeholder="Same as mobile if blank" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="e.g. ravi@email.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="premium-card mb-3">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Location</h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" value="Hyderabad">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" value="Telangana">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" maxlength="6">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2" placeholder="Full address (optional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="premium-card mb-3">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-house me-2"></i>Requirements</h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Budget Range</label>
                                <select class="form-select" name="budget_range_id" id="budgetRangeSelect">
                                    <option value="">Select Budget</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Property Type</label>
                                <select class="form-select" name="property_type_id" id="propertyTypeSelect">
                                    <option value="">Select Type</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Timeline</label>
                                <select class="form-select" name="timeline_id" id="timelineSelect">
                                    <option value="">Select Timeline</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Requirements Notes</label>
                                <textarea class="form-control" name="requirements_notes" rows="3" placeholder="Looking for 3BHK, east-facing, near school..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Source & Assignment -->
            <div class="col-lg-4">
                <!-- Project & Source -->
                <div class="premium-card mb-3">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Source & Project</h6>
                    </div>
                    <div class="p-3">
                        <div class="mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select" name="project_id" id="projectSelect" required>
                                <option value="">Select Project</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lead Source</label>
                            <select class="form-select" name="lead_source_id" id="leadSourceSelect">
                                <option value="">Select Source</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Source Campaign</label>
                            <input type="text" class="form-control" name="source_campaign" placeholder="e.g. Facebook Ads March">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Source Medium</label>
                            <input type="text" class="form-control" name="source_medium" placeholder="e.g. Social Media">
                        </div>
                    </div>
                </div>

                <!-- UTM Parameters -->
                <div class="premium-card mb-3">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>UTM Tracking</h6>
                    </div>
                    <div class="p-3">
                        <div class="mb-3">
                            <label class="form-label">UTM Source</label>
                            <input type="text" class="form-control form-control-sm" name="utm_source">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">UTM Medium</label>
                            <input type="text" class="form-control form-control-sm" name="utm_medium">
                        </div>
                        <div class="mb-0">
                            <label class="form-label">UTM Campaign</label>
                            <input type="text" class="form-control form-control-sm" name="utm_campaign">
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="premium-card">
                    <div class="p-3">
                        <button type="submit" class="btn btn-primary w-100 mb-2" id="btnCreateLead">
                            <i class="bi bi-check-circle me-1"></i> Create Lead
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100" id="btnCreateAnother">
                            <i class="bi bi-plus me-1"></i> Create & Add Another
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .premium-card {
        background: white;
        border: 1px solid rgba(184,149,106,0.15);
        border-radius: 10px;
        overflow: hidden;
    }
    .card-header-premium {
        padding: 0.75rem 1rem;
        background: rgba(184,149,106,0.05);
        border-bottom: 1px solid rgba(184,149,106,0.15);
        font-weight: 600;
    }
    .card-header-premium h6 {
        color: var(--color-dark-maroon);
        font-size: 0.9rem;
    }
    .form-label {
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--color-text-secondary);
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    const API = '/api/v1';
    let addAnother = false;

    document.addEventListener('DOMContentLoaded', () => {
        loadDropdowns();
        document.getElementById('createLeadForm').addEventListener('submit', handleSubmit);
        document.getElementById('btnCreateAnother').addEventListener('click', () => {
            addAnother = true;
            document.getElementById('createLeadForm').requestSubmit();
        });
    });

    async function loadDropdowns() {
        await Promise.all([
            loadSelect(`${API}/projects`, 'projectSelect'),
            loadSelect(`${API}/lead-sources`, 'leadSourceSelect'),
            loadSelect(`${API}/budget-ranges`, 'budgetRangeSelect'),
            loadSelect(`${API}/property-types`, 'propertyTypeSelect'),
            loadSelect(`${API}/timelines`, 'timelineSelect'),
        ]);
    }

    async function loadSelect(url, selectId) {
        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin'
            });
            const json = await res.json();
            const items = json.data || json;
            const select = document.getElementById(selectId);
            if (select && Array.isArray(items)) {
                const defaultOpt = select.querySelector('option');
                select.innerHTML = defaultOpt.outerHTML + items.map(i =>
                    `<option value="${i.id}">${i.name}</option>`
                ).join('');
            }
        } catch (e) {
            console.error(`Failed to load ${selectId}:`, e);
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const btn = document.getElementById('btnCreateLead');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

        const fd = new FormData(form);
        const body = {};
        fd.forEach((v, k) => { if (v) body[k] = v; });
        body.company_id = {{ auth()->user()->company_id }};

        try {
            const res = await fetch(`${API}/leads`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify(body)
            });
            const data = await res.json();

            if (data.success || res.status === 201) {
                showToast('Lead created successfully!', 'success');
                if (addAnother) {
                    form.reset();
                    addAnother = false;
                } else {
                    const id = data.data?.id;
                    if (id) {
                        window.location.href = `/leads/${id}`;
                    } else {
                        window.location.href = '/leads';
                    }
                }
            } else {
                const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                showToast(errors || 'Failed to create lead', 'danger');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'danger');
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Create Lead';
        }
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
@endpush
