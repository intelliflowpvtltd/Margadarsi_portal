@extends('layouts.app')

@section('title', 'Edit Lead')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">Edit Lead</li>
@endsection

@section('content')
<div class="edit-lead-page">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
        <div class="spinner-border text-gold" role="status"></div>
        <p class="mt-3 text-muted">Loading lead data...</p>
    </div>

    <!-- Main Form (hidden until loaded) -->
    <div id="mainContent" style="display:none">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="page-title mb-1" style="font-size:1.5rem;">
                    <i class="bi bi-pencil-square me-2 text-gold"></i>Edit Lead: <span id="leadNameHeader"></span>
                </h1>
                <p class="text-muted mb-0" style="font-size:0.875rem;">Update lead information and details</p>
            </div>
            <div class="d-flex gap-2">
                <a id="viewLeadLink" href="#" class="btn btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> View Lead
                </a>
                <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <form id="editLeadForm" novalidate>
            <div class="row g-3">
                <!-- Left Column -->
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
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="mobile" required maxlength="10">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alt Mobile</label>
                                    <input type="tel" class="form-control" name="alt_mobile" maxlength="10">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="tel" class="form-control" name="whatsapp" maxlength="10">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email">
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
                                    <input type="text" class="form-control" name="city">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control" name="state">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="pincode" maxlength="6">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2"></textarea>
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
                                    <textarea class="form-control" name="requirements_notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Source & Project -->
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
                                <input type="text" class="form-control" name="source_campaign">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Source Medium</label>
                                <input type="text" class="form-control" name="source_medium">
                            </div>
                        </div>
                    </div>

                    <!-- Assignment -->
                    <div class="premium-card mb-3">
                        <div class="card-header-premium">
                            <h6 class="mb-0"><i class="bi bi-person-check me-2"></i>Assignment</h6>
                        </div>
                        <div class="p-3">
                            <div class="mb-3">
                                <label class="form-label">Assigned To</label>
                                <select class="form-select" name="current_assignee_id" id="assigneeSelect">
                                    <option value="">Select User</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" id="currentStatus" disabled>
                            </div>
                        </div>
                    </div>

                    <!-- Save -->
                    <div class="premium-card">
                        <div class="p-3">
                            <button type="submit" class="btn btn-primary w-100 mb-2" id="btnUpdateLead">
                                <i class="bi bi-check-circle me-1"></i> Update Lead
                            </button>
                            <a id="cancelLink" href="{{ route('leads.index') }}" class="btn btn-outline-secondary w-100">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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
    const LEAD_ID = {{ $leadId }};
    const API = '/api/v1';
    let currentLead = null;

    document.addEventListener('DOMContentLoaded', async () => {
        await loadDropdowns();
        await loadLeadData();
        document.getElementById('editLeadForm').addEventListener('submit', handleSubmit);
    });

    async function loadDropdowns() {
        await Promise.all([
            loadSelect(`${API}/projects`, 'projectSelect'),
            loadSelect(`${API}/lead-sources`, 'leadSourceSelect'),
            loadSelect(`${API}/budget-ranges`, 'budgetRangeSelect'),
            loadSelect(`${API}/property-types`, 'propertyTypeSelect'),
            loadSelect(`${API}/timelines`, 'timelineSelect'),
            loadSelect(`${API}/users`, 'assigneeSelect', item => `${item.first_name} ${item.last_name}`),
        ]);
    }

    async function loadSelect(url, selectId, labelFn) {
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
                    `<option value="${i.id}">${labelFn ? labelFn(i) : i.name}</option>`
                ).join('');
            }
        } catch (e) {
            console.error(`Failed to load ${selectId}:`, e);
        }
    }

    async function loadLeadData() {
        try {
            const res = await fetch(`${API}/leads/${LEAD_ID}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!data.success) { showToast('Failed to load lead', 'danger'); return; }

            currentLead = data.data;
            populateForm(currentLead);

            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
        } catch (err) {
            console.error(err);
            showToast('Error loading lead data', 'danger');
        }
    }

    function populateForm(lead) {
        const form = document.getElementById('editLeadForm');
        document.getElementById('leadNameHeader').textContent = lead.name;
        document.getElementById('viewLeadLink').href = `/leads/${LEAD_ID}`;

        // Text inputs
        const textFields = ['name', 'mobile', 'alt_mobile', 'whatsapp', 'email', 'city', 'state', 'pincode', 'address', 'source_campaign', 'source_medium', 'requirements_notes'];
        textFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input && lead[field]) {
                if (input.tagName === 'TEXTAREA') input.value = lead[field];
                else input.value = lead[field];
            }
        });

        // Select fields
        const selectMap = {
            'project_id': lead.project_id,
            'lead_source_id': lead.lead_source_id,
            'budget_range_id': lead.budget_range_id,
            'property_type_id': lead.property_type_id,
            'timeline_id': lead.timeline_id,
            'current_assignee_id': lead.current_assignee_id,
        };
        Object.entries(selectMap).forEach(([name, value]) => {
            if (value) {
                const select = form.querySelector(`[name="${name}"]`);
                if (select) select.value = value;
            }
        });

        // Status
        const statusLabels = { new: 'New', contacted: 'Contacted', unreachable: 'Unreachable', qualified: 'Qualified', handed_over: 'Handed Over', not_qualified: 'Not Qualified', lost: 'Lost' };
        document.getElementById('currentStatus').value = statusLabels[lead.status] || lead.status;
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        if (!form.checkValidity()) { form.classList.add('was-validated'); return; }

        const btn = document.getElementById('btnUpdateLead');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

        const fd = new FormData(form);
        const body = {};
        fd.forEach((v, k) => { if (v) body[k] = v; });

        try {
            const res = await fetch(`${API}/leads/${LEAD_ID}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify(body)
            });
            const data = await res.json();

            if (data.success || res.ok) {
                showToast('Lead updated successfully!', 'success');
                setTimeout(() => window.location.href = `/leads/${LEAD_ID}`, 1500);
            } else {
                const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
                showToast(errors || 'Failed to update lead', 'danger');
            }
        } catch (err) {
            showToast('Network error', 'danger');
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Lead';
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
