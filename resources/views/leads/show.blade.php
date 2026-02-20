@extends('layouts.app')

@section('title', 'Lead Details')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active" id="breadcrumbName">Loading...</li>
@endsection

@section('content')
<div id="leadDetailApp">
    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
        <div class="spinner-border text-gold" role="status"></div>
        <p class="mt-3 text-muted">Loading lead details...</p>
    </div>

    <!-- Main Content (hidden until loaded) -->
    <div id="mainContent" style="display:none">
        <!-- Header Bar -->
        <div class="lead-header-card mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="lead-avatar" id="leadAvatar">?</div>
                    <div>
                        <h2 class="mb-1 lead-title" id="leadName"></h2>
                        <div class="d-flex flex-wrap gap-3 text-muted">
                            <span id="leadMobile"><i class="bi bi-telephone me-1"></i></span>
                            <span id="leadEmail"><i class="bi bi-envelope me-1"></i></span>
                            <span id="leadProject"><i class="bi bi-building me-1"></i></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <span class="status-badge" id="leadStatus"></span>
                    <span class="stage-badge" id="leadStage"></span>
                    <span class="sla-indicator" id="slaIndicator"></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Details + Timeline -->
            <div class="col-lg-8">
                <!-- Info Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon"><i class="bi bi-telephone-outbound"></i></div>
                            <div class="mini-stat-value" id="callCount">0</div>
                            <div class="mini-stat-label">Call Attempts</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon text-success"><i class="bi bi-telephone-inbound"></i></div>
                            <div class="mini-stat-value" id="connectedCount">0</div>
                            <div class="mini-stat-label">Connected</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon text-warning"><i class="bi bi-clock-history"></i></div>
                            <div class="mini-stat-value" id="slaTime">—</div>
                            <div class="mini-stat-label">SLA Response</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mini-stat-card">
                            <div class="mini-stat-icon text-info"><i class="bi bi-graph-up-arrow"></i></div>
                            <div class="mini-stat-value" id="engScore">0</div>
                            <div class="mini-stat-label">Engagement</div>
                        </div>
                    </div>
                </div>

                <!-- Tabs: Details / Activity / Calls -->
                <div class="premium-card">
                    <ul class="nav nav-tabs premium-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabDetails">Details</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabActivity">Activity <span class="badge bg-secondary ms-1" id="activityCount">0</span></a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabCalls">Calls <span class="badge bg-secondary ms-1" id="callTabCount">0</span></a></li>
                    </ul>
                    <div class="tab-content p-4">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="tabDetails">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-person me-2"></i>Contact Information</h6>
                                    <div class="detail-grid" id="contactInfo"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-geo-alt me-2"></i>Location</h6>
                                    <div class="detail-grid" id="locationInfo"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-funnel me-2"></i>Source & Campaign</h6>
                                    <div class="detail-grid" id="sourceInfo"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-house me-2"></i>Requirements</h6>
                                    <div class="detail-grid" id="requirementInfo"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-people me-2"></i>Assignment</h6>
                                    <div class="detail-grid" id="assignmentInfo"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="section-label"><i class="bi bi-calendar-event me-2"></i>Timeline</h6>
                                    <div class="detail-grid" id="timelineInfo"></div>
                                </div>
                            </div>
                            <div class="mt-4" id="requirementsNotes" style="display:none">
                                <h6 class="section-label"><i class="bi bi-card-text me-2"></i>Requirements Notes</h6>
                                <div class="notes-block" id="notesContent"></div>
                            </div>
                        </div>
                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="tabActivity">
                            <div class="activity-timeline" id="activityTimeline">
                                <div class="text-center text-muted py-4">No activities yet</div>
                            </div>
                        </div>
                        <!-- Calls Tab -->
                        <div class="tab-pane fade" id="tabCalls">
                            <div id="callHistory">
                                <div class="text-center text-muted py-4">No calls logged yet</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions + Quick Info -->
            <div class="col-lg-4">
                <!-- Workflow Actions -->
                <div class="premium-card mb-4">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="p-3" id="workflowActions"></div>
                </div>

                <!-- Log Call -->
                <div class="premium-card mb-4">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-telephone-plus me-2"></i>Log Call</h6>
                    </div>
                    <div class="p-3">
                        <form id="logCallForm">
                            <div class="mb-3">
                                <label class="form-label">Outcome *</label>
                                <select class="form-select form-select-sm" name="outcome" required>
                                    <option value="">Select outcome</option>
                                    <option value="connected">Connected</option>
                                    <option value="no_answer">No Answer</option>
                                    <option value="busy">Busy</option>
                                    <option value="switched_off">Switched Off</option>
                                    <option value="wrong_number">Wrong Number</option>
                                    <option value="voicemail">Voicemail</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Duration (seconds)</label>
                                <input type="number" class="form-control form-control-sm" name="duration_seconds" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control form-control-sm" name="notes" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Next Follow-up</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="next_followup_at">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-telephone-plus me-1"></i> Log Call
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Schedule Follow-up -->
                <div class="premium-card mb-4">
                    <div class="card-header-premium">
                        <h6 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Schedule Follow-up</h6>
                    </div>
                    <div class="p-3">
                        <form id="followupForm">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Date *</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="followup_at" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control form-control-sm" name="notes" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-info btn-sm w-100 text-white">
                                <i class="bi bi-calendar-check me-1"></i> Schedule
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="premium-card" id="dangerZone" style="display:none">
                    <div class="card-header-premium bg-danger bg-opacity-10">
                        <h6 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Actions</h6>
                    </div>
                    <div class="p-3" id="dangerActions"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- NQ Reason Modal -->
<div class="modal fade" id="nqModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content modal-premium">
        <div class="modal-header"><h5 class="modal-title">Mark Not Qualified</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Reason *</label>
                <select class="form-select" id="nqReasonSelect" required>
                    <option value="">Select reason</option>
                    <option value="invalid_contact">Invalid Contact</option>
                    <option value="not_interested">Not Interested</option>
                    <option value="already_purchased">Already Purchased</option>
                    <option value="budget_mismatch">Budget Mismatch</option>
                    <option value="location_mismatch">Location Mismatch</option>
                    <option value="duplicate">Duplicate Lead</option>
                    <option value="spam">Spam/Fake</option>
                    <option value="wrong_number">Wrong Number</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="nqNotes" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="submitNQ()">Mark NQ</button>
        </div>
    </div></div>
</div>

<!-- Lost Reason Modal -->
<div class="modal fade" id="lostModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content modal-premium">
        <div class="modal-header"><h5 class="modal-title">Mark as Lost</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Reason *</label>
                <select class="form-select" id="lostReasonSelect" required>
                    <option value="">Select reason</option>
                    <option value="budget_constraints">Budget Constraints</option>
                    <option value="competitor">Bought from Competitor</option>
                    <option value="location">Location Not Suitable</option>
                    <option value="project_delay">Project Delayed</option>
                    <option value="financial">Financial Issues</option>
                    <option value="changed_mind">Changed Mind</option>
                    <option value="better_deal">Better Deal Elsewhere</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Competitor Name (if applicable)</label>
                <input type="text" class="form-control" id="lostCompetitor">
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="lostNotes" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="submitLost()">Mark Lost</button>
        </div>
    </div></div>
</div>

<!-- Handover Modal -->
<div class="modal fade" id="handoverModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content modal-premium">
        <div class="modal-header"><h5 class="modal-title">Hand Over Lead</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Handover Notes</label>
                <textarea class="form-control" id="handoverNotes" rows="3" placeholder="Key info for the sales team..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" onclick="submitHandover()">Hand Over</button>
        </div>
    </div></div>
</div>
@endsection

@push('styles')
<style>
    .lead-header-card {
        background: linear-gradient(135deg, rgba(128,0,32,0.03), rgba(184,149,106,0.06));
        border: 1px solid rgba(184,149,106,0.2);
        border-radius: 16px;
        padding: 1.5rem 2rem;
    }
    .lead-avatar {
        width: 56px; height: 56px;
        background: linear-gradient(135deg, var(--color-dark-maroon), var(--color-coffee-gold));
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.5rem; font-weight: 700;
    }
    .lead-title { font-family: var(--font-primary); color: var(--color-dark-maroon); font-size: 1.5rem; font-weight: 700; }
    .status-badge {
        padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .stage-badge {
        padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500;
        background: rgba(184,149,106,0.15); color: var(--color-coffee-gold-dark);
    }
    .sla-indicator { padding: 0.3rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .sla-ok { background: #D1FAE5; color: #059669; }
    .sla-breach { background: #FEE2E2; color: #DC2626; animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }

    .mini-stat-card {
        background: white; border: 1px solid rgba(184,149,106,0.15); border-radius: 12px;
        padding: 1rem; text-align: center; transition: transform 0.2s;
    }
    .mini-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .mini-stat-icon { font-size: 1.25rem; color: var(--color-coffee-gold); margin-bottom: 0.25rem; }
    .mini-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--color-dark-maroon); }
    .mini-stat-label { font-size: 0.75rem; color: var(--color-text-secondary); }

    .premium-card {
        background: white; border: 1px solid rgba(184,149,106,0.15); border-radius: 12px;
        overflow: hidden;
    }
    .card-header-premium {
        padding: 0.75rem 1rem; background: rgba(184,149,106,0.05);
        border-bottom: 1px solid rgba(184,149,106,0.15); font-weight: 600;
    }
    .premium-tabs { border-bottom: 2px solid rgba(184,149,106,0.15); padding: 0 1rem; }
    .premium-tabs .nav-link { color: var(--color-text-secondary); border: none; padding: 0.75rem 1rem; }
    .premium-tabs .nav-link.active {
        color: var(--color-dark-maroon); border-bottom: 2px solid var(--color-coffee-gold);
        font-weight: 600; background: transparent;
    }

    .section-label { color: var(--color-dark-maroon); font-weight: 600; margin-bottom: 0.75rem; font-size: 0.9rem; }
    .detail-grid { display: grid; gap: 0.5rem; }
    .detail-row { display: flex; gap: 0.5rem; font-size: 0.875rem; }
    .detail-label { color: var(--color-text-secondary); min-width: 120px; flex-shrink: 0; }
    .detail-value { color: var(--color-text-primary); font-weight: 500; }
    .notes-block {
        background: rgba(184,149,106,0.05); border-radius: 8px; padding: 1rem;
        font-size: 0.875rem; white-space: pre-wrap; border-left: 3px solid var(--color-coffee-gold);
    }

    /* Activity Timeline */
    .activity-item {
        display: flex; gap: 1rem; padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .activity-icon {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.875rem; flex-shrink: 0;
    }
    .activity-icon.status-change { background: #EDE9FE; color: #7C3AED; }
    .activity-icon.stage-change { background: #D1FAE5; color: #059669; }
    .activity-icon.call { background: #DBEAFE; color: #2563EB; }
    .activity-icon.note { background: #FEF3C7; color: #D97706; }
    .activity-icon.assignment { background: #FCE7F3; color: #DB2777; }
    .activity-desc { font-size: 0.875rem; }
    .activity-time { font-size: 0.75rem; color: var(--color-text-muted); }
    .activity-user { font-size: 0.75rem; color: var(--color-coffee-gold-dark); font-weight: 500; }

    /* Call History */
    .call-item {
        display: flex; gap: 1rem; align-items: center; padding: 0.75rem;
        border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; margin-bottom: 0.5rem;
    }
    .call-outcome-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
    .call-connected { background: #D1FAE5; color: #059669; }
    .call-missed { background: #FEE2E2; color: #DC2626; }
    .call-busy { background: #FEF3C7; color: #D97706; }

    /* Action Buttons */
    .action-btn {
        display: flex; align-items: center; gap: 0.5rem;
        width: 100%; padding: 0.6rem 1rem; border-radius: 8px;
        border: 1px solid; margin-bottom: 0.5rem; font-size: 0.875rem;
        font-weight: 500; transition: all 0.2s; cursor: pointer;
        background: transparent;
    }
    .action-btn:hover { transform: translateX(4px); }
    .action-btn.btn-qualify { border-color: #10B981; color: #059669; }
    .action-btn.btn-qualify:hover { background: #10B981; color: white; }
    .action-btn.btn-nq { border-color: #6B7280; color: #4B5563; }
    .action-btn.btn-nq:hover { background: #6B7280; color: white; }
    .action-btn.btn-handover { border-color: #22C55E; color: #16A34A; }
    .action-btn.btn-handover:hover { background: #22C55E; color: white; }
    .action-btn.btn-lost { border-color: #EF4444; color: #DC2626; }
    .action-btn.btn-lost:hover { background: #EF4444; color: white; }
    .action-btn.btn-transition { border-color: #3B82F6; color: #2563EB; }
    .action-btn.btn-transition:hover { background: #3B82F6; color: white; }

    .modal-premium { border-radius: 12px; }
    .modal-premium .modal-header {
        background: linear-gradient(135deg, rgba(128,0,32,0.05), rgba(184,149,106,0.05));
        border-bottom: 1px solid rgba(184,149,106,0.2);
    }
</style>
@endpush

@push('scripts')
<script>
    const LEAD_ID = {{ $leadId }};
    const API_BASE = '/api/v1/leads';
    let currentLead = null;
    let currentWorkflow = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadLeadDetail();
        document.getElementById('logCallForm').addEventListener('submit', submitLogCall);
        document.getElementById('followupForm').addEventListener('submit', submitFollowup);
    });

    async function loadLeadDetail() {
        try {
            const res = await fetch(`${API_BASE}/${LEAD_ID}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!data.success) { showToast('Failed to load lead', 'danger'); return; }

            currentLead = data.data;
            currentWorkflow = data.workflow;
            renderHeader();
            renderStats();
            renderDetails();
            renderWorkflowActions();
            renderActivities();
            renderCalls();

            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('mainContent').style.display = 'block';
        } catch (err) {
            console.error(err);
            showToast('Error loading lead details', 'danger');
        }
    }

    function renderHeader() {
        const l = currentLead, w = currentWorkflow;
        document.getElementById('breadcrumbName').textContent = l.name;
        document.getElementById('leadName').textContent = l.name;
        document.getElementById('leadAvatar').textContent = l.name.charAt(0).toUpperCase();
        document.getElementById('leadMobile').innerHTML = `<i class="bi bi-telephone me-1"></i>${l.mobile}`;
        document.getElementById('leadEmail').innerHTML = l.email ? `<i class="bi bi-envelope me-1"></i>${l.email}` : '';
        document.getElementById('leadProject').innerHTML = `<i class="bi bi-building me-1"></i>${l.project?.name || 'N/A'}`;

        const statusEl = document.getElementById('leadStatus');
        statusEl.textContent = w.status_label;
        statusEl.style.background = w.status_color + '20';
        statusEl.style.color = w.status_color;

        document.getElementById('leadStage').textContent = w.stage_label;

        const slaEl = document.getElementById('slaIndicator');
        if (l.sla_breached) {
            slaEl.className = 'sla-indicator sla-breach';
            slaEl.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>SLA Breached';
        } else {
            slaEl.className = 'sla-indicator sla-ok';
            slaEl.innerHTML = '<i class="bi bi-check-circle me-1"></i>SLA OK';
        }
    }

    function renderStats() {
        const l = currentLead;
        document.getElementById('callCount').textContent = l.call_attempts || 0;
        document.getElementById('connectedCount').textContent = l.connected_calls || 0;
        document.getElementById('engScore').textContent = l.engagement_score || 0;
        if (l.sla_response_seconds) {
            const mins = Math.floor(l.sla_response_seconds / 60);
            document.getElementById('slaTime').textContent = mins > 0 ? `${mins}m` : `${l.sla_response_seconds}s`;
        }
    }

    function renderDetails() {
        const l = currentLead;

        document.getElementById('contactInfo').innerHTML = detailRows([
            ['Name', l.name],
            ['Mobile', l.mobile],
            ['Alt Mobile', l.alt_mobile],
            ['WhatsApp', l.whatsapp],
            ['Email', l.email],
        ]);

        document.getElementById('locationInfo').innerHTML = detailRows([
            ['City', l.city],
            ['State', l.state],
            ['Pincode', l.pincode],
            ['Address', l.address],
        ]);

        document.getElementById('sourceInfo').innerHTML = detailRows([
            ['Source', l.lead_source?.name],
            ['Campaign', l.source_campaign],
            ['Medium', l.source_medium],
            ['UTM Source', l.utm_source],
            ['UTM Medium', l.utm_medium],
            ['UTM Campaign', l.utm_campaign],
        ]);

        document.getElementById('requirementInfo').innerHTML = detailRows([
            ['Budget', l.budget_range?.name],
            ['Property Type', l.property_type?.name],
            ['Timeline', l.timeline?.name],
            ['Budget Confirmed', l.budget_confirmed ? 'Yes' : 'No'],
        ]);

        document.getElementById('assignmentInfo').innerHTML = detailRows([
            ['Assignee', l.current_assignee ? `${l.current_assignee.first_name} ${l.current_assignee.last_name}` : 'Unassigned'],
            ['Team', l.team?.name],
            ['Original Owner', l.original_owner ? `${l.original_owner.first_name} ${l.original_owner.last_name}` : '—'],
            ['Assigned At', formatDate(l.assigned_at)],
        ]);

        document.getElementById('timelineInfo').innerHTML = detailRows([
            ['Created', formatDate(l.created_at)],
            ['First Call', formatDate(l.first_call_at)],
            ['Last Call', formatDate(l.last_call_at)],
            ['Next Follow-up', formatDate(l.next_followup_at)],
            ['Last Activity', formatDate(l.last_activity_at)],
        ]);

        if (l.requirements_notes) {
            document.getElementById('requirementsNotes').style.display = 'block';
            document.getElementById('notesContent').textContent = l.requirements_notes;
        }
    }

    function renderWorkflowActions() {
        const w = currentWorkflow;
        let html = '';

        if (w.allowed_transitions && w.allowed_transitions.length > 0) {
            w.allowed_transitions.forEach(t => {
                const statusInfo = {
                    contacted: { icon: 'bi-telephone-fill', cls: 'btn-transition', label: 'Mark Contacted' },
                    unreachable: { icon: 'bi-telephone-x-fill', cls: 'btn-nq', label: 'Mark Unreachable' },
                    qualified: { icon: 'bi-check-circle-fill', cls: 'btn-qualify', label: 'Mark Qualified' },
                    not_qualified: { icon: 'bi-x-circle-fill', cls: 'btn-nq', label: 'Mark Not Qualified' },
                    handed_over: { icon: 'bi-box-arrow-right', cls: 'btn-handover', label: 'Hand Over' },
                    lost: { icon: 'bi-dash-circle-fill', cls: 'btn-lost', label: 'Mark Lost' },
                };
                const info = statusInfo[t] || { icon: 'bi-arrow-right', cls: 'btn-transition', label: t };

                if (t === 'not_qualified') {
                    html += `<button class="action-btn ${info.cls}" onclick="showNQModal()"><i class="bi ${info.icon}"></i>${info.label}</button>`;
                } else if (t === 'lost') {
                    html += `<button class="action-btn ${info.cls}" onclick="showLostModal()"><i class="bi ${info.icon}"></i>${info.label}</button>`;
                } else if (t === 'handed_over') {
                    html += `<button class="action-btn ${info.cls}" onclick="showHandoverModal()"><i class="bi ${info.icon}"></i>${info.label}</button>`;
                } else if (t === 'qualified') {
                    html += `<button class="action-btn ${info.cls}" onclick="doQualify()"><i class="bi ${info.icon}"></i>${info.label}</button>`;
                } else {
                    html += `<button class="action-btn ${info.cls}" onclick="doTransition('${t}')"><i class="bi ${info.icon}"></i>${info.label}</button>`;
                }
            });
        }

        if (!html) {
            html = '<div class="text-center text-muted py-3"><i class="bi bi-check-circle-fill me-2"></i>No further transitions available</div>';
        }

        document.getElementById('workflowActions').innerHTML = html;

        // Danger zone
        const dangerEl = document.getElementById('dangerZone');
        if (!currentWorkflow.is_final_status) {
            dangerEl.style.display = 'block';
            document.getElementById('dangerActions').innerHTML = `
                <button class="action-btn btn-lost" onclick="showLostModal()">
                    <i class="bi bi-dash-circle-fill"></i> Mark as Lost
                </button>
            `;
        }
    }

    function renderActivities() {
        const activities = currentLead.activities || [];
        document.getElementById('activityCount').textContent = activities.length;

        if (activities.length === 0) return;

        const iconMap = {
            status_change: 'status-change', stage_change: 'stage-change',
            call: 'call', note: 'note', assignment: 'assignment'
        };
        const iconSymbol = {
            status_change: 'bi-arrow-repeat', stage_change: 'bi-diagram-3',
            call: 'bi-telephone', note: 'bi-chat-text', assignment: 'bi-person-check'
        };

        document.getElementById('activityTimeline').innerHTML = activities.map(a => `
            <div class="activity-item">
                <div class="activity-icon ${iconMap[a.activity_type] || 'note'}">
                    <i class="bi ${iconSymbol[a.activity_type] || 'bi-circle'}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="activity-desc">${a.description}</div>
                    <div class="d-flex gap-3 mt-1">
                        <span class="activity-time">${formatDate(a.created_at)}</span>
                        ${a.user ? `<span class="activity-user">${a.user.first_name || ''}</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    function renderCalls() {
        const calls = currentLead.calls || [];
        document.getElementById('callTabCount').textContent = calls.length;

        if (calls.length === 0) return;

        const outcomeCls = { connected: 'call-connected', no_answer: 'call-missed', busy: 'call-busy', switched_off: 'call-missed', wrong_number: 'call-missed' };

        document.getElementById('callHistory').innerHTML = calls.map(c => `
            <div class="call-item">
                <i class="bi bi-telephone fs-5 text-muted"></i>
                <div class="flex-grow-1">
                    <span class="call-outcome-badge ${outcomeCls[c.outcome] || 'call-busy'}">${c.outcome?.replace('_',' ')}</span>
                    ${c.duration_seconds ? `<span class="ms-2 text-muted small">${c.duration_seconds}s</span>` : ''}
                    ${c.notes ? `<p class="mb-0 mt-1 small text-muted">${c.notes}</p>` : ''}
                </div>
                <span class="text-muted small">${formatDate(c.created_at)}</span>
            </div>
        `).join('');
    }

    // Workflow action handlers
    async function doTransition(status) {
        await apiPost(`${API_BASE}/${LEAD_ID}/transition`, { status, sub_status: null });
        loadLeadDetail();
    }

    async function doQualify() {
        await apiPost(`${API_BASE}/${LEAD_ID}/qualify`, {});
        loadLeadDetail();
    }

    function showNQModal() { new bootstrap.Modal(document.getElementById('nqModal')).show(); }
    function showLostModal() { new bootstrap.Modal(document.getElementById('lostModal')).show(); }
    function showHandoverModal() { new bootstrap.Modal(document.getElementById('handoverModal')).show(); }

    async function submitNQ() {
        const reason = document.getElementById('nqReasonSelect').value;
        if (!reason) { showToast('Select a reason', 'warning'); return; }
        await apiPost(`${API_BASE}/${LEAD_ID}/disqualify`, {
            reason: reason,
            notes: document.getElementById('nqNotes').value
        });
        bootstrap.Modal.getInstance(document.getElementById('nqModal')).hide();
        loadLeadDetail();
    }

    async function submitLost() {
        const reason = document.getElementById('lostReasonSelect').value;
        if (!reason) { showToast('Select a reason', 'warning'); return; }
        await apiPost(`${API_BASE}/${LEAD_ID}/mark-lost`, {
            reason: reason,
            competitor: document.getElementById('lostCompetitor').value,
            notes: document.getElementById('lostNotes').value
        });
        bootstrap.Modal.getInstance(document.getElementById('lostModal')).hide();
        loadLeadDetail();
    }

    async function submitHandover() {
        await apiPost(`${API_BASE}/${LEAD_ID}/handover`, {
            notes: document.getElementById('handoverNotes').value
        });
        bootstrap.Modal.getInstance(document.getElementById('handoverModal')).hide();
        loadLeadDetail();
    }

    async function submitLogCall(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        await apiPost(`${API_BASE}/${LEAD_ID}/call`, Object.fromEntries(fd));
        e.target.reset();
        loadLeadDetail();
    }

    async function submitFollowup(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        await apiPost(`${API_BASE}/${LEAD_ID}/followup`, Object.fromEntries(fd));
        e.target.reset();
        loadLeadDetail();
    }

    // Helpers
    async function apiPost(url, body) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                credentials: 'same-origin',
                body: JSON.stringify(body)
            });
            const data = await res.json();
            if (data.success) { showToast(data.message || 'Done!', 'success'); }
            else { showToast(data.message || 'Error occurred', 'danger'); }
            return data;
        } catch (err) {
            showToast('Network error', 'danger');
            console.error(err);
        }
    }

    function csrfToken() { return document.querySelector('meta[name="csrf-token"]').content; }

    function detailRows(pairs) {
        return pairs.filter(([,v]) => v).map(([label, value]) =>
            `<div class="detail-row"><span class="detail-label">${label}</span><span class="detail-value">${value}</span></div>`
        ).join('');
    }

    function formatDate(d) {
        if (!d) return '—';
        const dt = new Date(d);
        return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush
