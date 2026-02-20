<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item active">Dashboard</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-speedometer2 me-2 text-gold"></i>
        Dashboard
    </h1>
    <p class="text-muted">Welcome back, <?php echo e(auth()->user()->first_name); ?>! Here's an overview of your portal.</p>
</div>

<!-- Top-Level Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="luxury-card text-center stat-glow">
            <i class="bi bi-building stat-icon text-gold"></i>
            <h3 class="text-maroon mb-1"><?php echo e(\App\Models\Company::count()); ?></h3>
            <p class="text-muted mb-0 small">Companies</p>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="luxury-card text-center stat-glow">
            <i class="bi bi-briefcase stat-icon text-gold"></i>
            <h3 class="text-gold mb-1"><?php echo e(\App\Models\Project::count()); ?></h3>
            <p class="text-muted mb-0 small">Projects</p>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="luxury-card text-center stat-glow">
            <i class="bi bi-people stat-icon text-maroon"></i>
            <h3 class="text-maroon mb-1"><?php echo e(\App\Models\User::count()); ?></h3>
            <p class="text-muted mb-0 small">Users</p>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="luxury-card text-center stat-glow">
            <i class="bi bi-telephone stat-icon text-gold"></i>
            <h3 class="text-gold mb-1" id="totalLeads">—</h3>
            <p class="text-muted mb-0 small">Total Leads</p>
        </div>
    </div>
</div>

<!-- Lead Pipeline Stats (loaded via API) -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="luxury-card">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-funnel me-2 text-gold"></i>
                    Lead Pipeline
                </h3>
                <a href="<?php echo e(route('leads.index')); ?>" class="btn btn-sm btn-outline-gold">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="p-3">
                <div class="pipeline-track" id="pipelineTrack">
                    <div class="pipeline-loading text-center py-4">
                        <div class="spinner-border spinner-border-sm text-gold" role="status"></div>
                        <span class="ms-2 text-muted">Loading pipeline...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two-Column: SLA & Conversion Metrics -->
<div class="row g-4 mb-4">
    <!-- SLA Performance -->
    <div class="col-md-4">
        <div class="luxury-card h-100">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-clock-history me-2 text-gold"></i>
                    SLA Performance
                </h3>
            </div>
            <div class="p-3">
                <div class="sla-metric mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">SLA Compliance</span>
                        <span class="fw-bold" id="slaPercent">—</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" id="slaBar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">SLA Breached</span>
                    <span class="fw-bold text-danger" id="slaBreached">0</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Avg Response Time</span>
                    <span class="fw-bold" id="avgResponse">—</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Follow-ups Due</span>
                    <span class="fw-bold text-warning" id="followupsDue">0</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Dormant Leads</span>
                    <span class="fw-bold text-secondary" id="dormantLeads">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Metrics -->
    <div class="col-md-4">
        <div class="luxury-card h-100">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-graph-up-arrow me-2 text-gold"></i>
                    Conversion Rates
                </h3>
            </div>
            <div class="p-3">
                <div class="conversion-metric mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Lead → Qualified</span>
                        <span class="fw-bold text-success" id="qualifyRate">—</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" id="qualifyBar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="conversion-metric mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Qualified → Handed Over</span>
                        <span class="fw-bold text-primary" id="handoverRate">—</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" id="handoverBar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="conversion-metric mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Lost Rate</span>
                        <span class="fw-bold text-danger" id="lostRate">—</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" id="lostBar" style="width: 0%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between py-2 border-top mt-2">
                    <span class="text-muted small">Total Qualified</span>
                    <span class="fw-bold text-success" id="totalQualified">0</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Total Handed Over</span>
                    <span class="fw-bold text-primary" id="totalHandedOver">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-md-4">
        <div class="luxury-card h-100">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-activity me-2 text-gold"></i>
                    Today's Highlights
                </h3>
            </div>
            <div class="p-3">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">New Leads Today</span>
                    <span class="fw-bold text-primary" id="todayNew">0</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Calls Made Today</span>
                    <span class="fw-bold" id="todayCalls">0</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Qualified Today</span>
                    <span class="fw-bold text-success" id="todayQualified">0</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Handed Over Today</span>
                    <span class="fw-bold text-primary" id="todayHandedOver">0</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Active Projects</span>
                    <span class="fw-bold text-gold"><?php echo e(\App\Models\Project::where('is_active', true)->count()); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Projects & Quick Actions -->
<div class="row g-4">
    <div class="col-md-8">
        <div class="luxury-card">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-briefcase me-2 text-gold"></i>
                    Recent Projects
                </h3>
                <a href="<?php echo e(route('projects.index')); ?>" class="btn btn-sm btn-outline-gold">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>City</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = \App\Models\Project::latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-medium"><?php echo e($project->name); ?></td>
                            <td><span class="badge bg-secondary"><?php echo e(ucfirst($project->type)); ?></span></td>
                            <td>
                                <?php
                                $statusColors = ['upcoming'=>'info','ongoing'=>'warning','completed'=>'success','on-hold'=>'danger'];
                                ?>
                                <span class="badge bg-<?php echo e($statusColors[$project->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($project->status)); ?></span>
                            </td>
                            <td><?php echo e($project->city); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="luxury-card mb-4">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-lightning-charge me-2 text-gold"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="d-grid gap-2 p-3">
                <a href="<?php echo e(route('leads.index')); ?>" class="btn btn-outline-gold">
                    <i class="bi bi-telephone me-2"></i>Manage Leads
                </a>
                <a href="<?php echo e(route('companies.index')); ?>" class="btn btn-outline-gold">
                    <i class="bi bi-building me-2"></i>Manage Companies
                </a>
                <a href="<?php echo e(route('projects.index')); ?>" class="btn btn-outline-gold">
                    <i class="bi bi-briefcase me-2"></i>Manage Projects
                </a>
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-gold">
                    <i class="bi bi-people me-2"></i>Manage Users
                </a>
                <a href="<?php echo e(route('roles.index')); ?>" class="btn btn-outline-gold">
                    <i class="bi bi-shield-check me-2"></i>Manage Roles
                </a>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="luxury-card">
            <div class="text-center p-3">
                <div class="user-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.5rem;">
                    <?php echo e(strtoupper(substr(auth()->user()->first_name, 0, 1))); ?><?php echo e(strtoupper(substr(auth()->user()->last_name, 0, 1))); ?>

                </div>
                <h5 class="mb-1"><?php echo e(auth()->user()->first_name); ?> <?php echo e(auth()->user()->last_name); ?></h5>
                <p class="text-muted mb-1 small"><?php echo e(auth()->user()->role->name ?? 'N/A'); ?></p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-envelope me-1"></i><?php echo e(auth()->user()->email); ?>

                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stat-icon { font-size: 2.5rem; display: block; margin-bottom: 0.5rem; }
    .stat-glow { transition: all 0.3s ease; }
    .stat-glow:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(184,149,106,0.2); }

    .pipeline-track { display: flex; gap: 0; overflow-x: auto; }
    .pipeline-stage {
        flex: 1; min-width: 120px; text-align: center; padding: 1rem 0.5rem;
        position: relative; transition: all 0.3s;
    }
    .pipeline-stage::after {
        content: ''; position: absolute; right: -8px; top: 50%; transform: translateY(-50%);
        width: 0; height: 0; border-left: 8px solid; border-top: 8px solid transparent;
        border-bottom: 8px solid transparent; z-index: 1;
    }
    .pipeline-stage:last-child::after { display: none; }
    .pipeline-count { font-size: 1.75rem; font-weight: 700; }
    .pipeline-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }
    .pipeline-stage.ps-new { background: #DBEAFE; color: #1D4ED8; }
    .pipeline-stage.ps-new::after { border-left-color: #DBEAFE; }
    .pipeline-stage.ps-contacted { background: #EDE9FE; color: #6D28D9; }
    .pipeline-stage.ps-contacted::after { border-left-color: #EDE9FE; }
    .pipeline-stage.ps-unreachable { background: #FEF3C7; color: #B45309; }
    .pipeline-stage.ps-unreachable::after { border-left-color: #FEF3C7; }
    .pipeline-stage.ps-qualified { background: #D1FAE5; color: #047857; }
    .pipeline-stage.ps-qualified::after { border-left-color: #D1FAE5; }
    .pipeline-stage.ps-handed_over { background: #DCFCE7; color: #15803D; }
    .pipeline-stage.ps-handed_over::after { border-left-color: #DCFCE7; }
    .pipeline-stage.ps-not_qualified { background: #F3F4F6; color: #4B5563; }
    .pipeline-stage.ps-not_qualified::after { border-left-color: #F3F4F6; }
    .pipeline-stage.ps-lost { background: #FEE2E2; color: #B91C1C; }
    .pipeline-stage.ps-lost::after { border-left-color: #FEE2E2; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardStats();
        // Animate cards
        document.querySelectorAll('.luxury-card').forEach((card, i) => {
            setTimeout(() => card.classList.add('fade-in'), i * 50);
        });
    });

    async function loadDashboardStats() {
        try {
            const res = await fetch('/api/v1/leads/statistics', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!data.success) return;

            const stats = data.data;
            const byStatus = stats.by_status || {};

            // Total leads
            const total = Object.values(byStatus).reduce((s, v) => s + v, 0);
            document.getElementById('totalLeads').textContent = total;

            // Pipeline
            const stages = [
                { key: 'new', label: 'New' },
                { key: 'contacted', label: 'Contacted' },
                { key: 'unreachable', label: 'Unreachable' },
                { key: 'qualified', label: 'Qualified' },
                { key: 'handed_over', label: 'Handed Over' },
                { key: 'not_qualified', label: 'Not Qualified' },
                { key: 'lost', label: 'Lost' },
            ];

            document.getElementById('pipelineTrack').innerHTML = stages.map(s =>
                `<div class="pipeline-stage ps-${s.key}">
                    <div class="pipeline-count">${byStatus[s.key] || 0}</div>
                    <div class="pipeline-label">${s.label}</div>
                </div>`
            ).join('');

            // SLA metrics
            const slaBreached = stats.sla_breached || 0;
            const slaCompliant = total > 0 ? Math.round(((total - slaBreached) / total) * 100) : 100;
            document.getElementById('slaPercent').textContent = slaCompliant + '%';
            document.getElementById('slaBar').style.width = slaCompliant + '%';
            document.getElementById('slaBar').className = `progress-bar ${slaCompliant >= 80 ? 'bg-success' : slaCompliant >= 50 ? 'bg-warning' : 'bg-danger'}`;
            document.getElementById('slaBreached').textContent = slaBreached;
            document.getElementById('followupsDue').textContent = stats.followups_due || 0;
            document.getElementById('dormantLeads').textContent = stats.dormant || 0;

            if (stats.avg_response_seconds) {
                const mins = Math.floor(stats.avg_response_seconds / 60);
                document.getElementById('avgResponse').textContent = mins > 0 ? `${mins}m` : `${stats.avg_response_seconds}s`;
            }

            // Conversion rates
            const qualified = byStatus['qualified'] || 0;
            const handedOver = byStatus['handed_over'] || 0;
            const lost = byStatus['lost'] || 0;
            const nq = byStatus['not_qualified'] || 0;

            const qualifyRate = total > 0 ? Math.round(((qualified + handedOver) / total) * 100) : 0;
            const handoverRate = (qualified + handedOver) > 0 ? Math.round((handedOver / (qualified + handedOver)) * 100) : 0;
            const lostRate = total > 0 ? Math.round(((lost + nq) / total) * 100) : 0;

            document.getElementById('qualifyRate').textContent = qualifyRate + '%';
            document.getElementById('qualifyBar').style.width = qualifyRate + '%';
            document.getElementById('handoverRate').textContent = handoverRate + '%';
            document.getElementById('handoverBar').style.width = handoverRate + '%';
            document.getElementById('lostRate').textContent = lostRate + '%';
            document.getElementById('lostBar').style.width = lostRate + '%';
            document.getElementById('totalQualified').textContent = qualified;
            document.getElementById('totalHandedOver').textContent = handedOver;

            // Today highlights
            document.getElementById('todayNew').textContent = stats.today_new || 0;
            document.getElementById('todayCalls').textContent = stats.today_calls || 0;
            document.getElementById('todayQualified').textContent = stats.today_qualified || 0;
            document.getElementById('todayHandedOver').textContent = stats.today_handed_over || 0;
        } catch (err) {
            console.error('Dashboard stats error:', err);
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/dashboard/index.blade.php ENDPATH**/ ?>