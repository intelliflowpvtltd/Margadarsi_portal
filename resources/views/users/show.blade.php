@extends('layouts.app')

@section('title', $user->first_name . ' ' . $user->last_name . ' - User Profile')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
<li class="breadcrumb-item active">{{ $user->first_name }} {{ $user->last_name }}</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-person-badge me-2 text-gold"></i>
                {{ $user->first_name }} {{ $user->last_name }}
            </h1>
            <p class="text-muted mb-0">
                {{ $user->designation ?? $user->role?->name ?? 'Team Member' }} 
                @if($user->department)
                    • {{ $user->department->name }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
            @can('users.update')
            <a href="{{ route('users.index') }}?edit={{ $user->id }}" class="btn btn-gold">
                <i class="bi bi-pencil me-2"></i>Edit User
            </a>
            @endcan
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Profile Card -->
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="premium-card mb-4">
            <div class="card-body text-center py-5">
                <!-- Avatar -->
                @if($user->avatar || $user->profile_photo)
                    <img src="{{ asset('storage/' . ($user->avatar ?? $user->profile_photo)) }}" 
                         alt="{{ $user->first_name }}" 
                         class="profile-avatar-xl mb-4">
                @else
                    <div class="profile-avatar-xl-placeholder mx-auto mb-4">
                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                    </div>
                @endif
                
                <!-- Name -->
                <h3 class="mb-2 fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h3>
                
                <!-- Designation -->
                <p class="text-muted mb-3">{{ $user->designation ?? 'Team Member' }}</p>
                
                <!-- Status Badge -->
                @if($user->is_active)
                    <span class="status-badge status-active">
                        <i class="bi bi-check-circle-fill me-1"></i>Active
                    </span>
                @else
                    <span class="status-badge status-inactive">
                        <i class="bi bi-x-circle-fill me-1"></i>Inactive
                    </span>
                @endif
                
                <!-- Quick Actions -->
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <a href="mailto:{{ $user->email }}" class="btn btn-outline-gold btn-sm">
                        <i class="bi bi-envelope"></i>
                    </a>
                    @if($user->phone)
                    <a href="tel:{{ $user->phone }}" class="btn btn-outline-gold btn-sm">
                        <i class="bi bi-telephone"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Stats Card -->
        <div class="premium-card">
            <div class="card-body">
                <h6 class="card-section-title mb-4">
                    <i class="bi bi-bar-chart me-2"></i>Quick Stats
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-box-value text-gold">{{ $user->projects->count() }}</div>
                            <div class="stat-box-label">Projects</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-box-value text-gold">{{ $user->directReports->count() }}</div>
                            <div class="stat-box-label">Team Members</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Details -->
    <div class="col-lg-8">
        <!-- Organization Info Card -->
        <div class="premium-card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-building me-2 text-gold"></i>
                <h5 class="mb-0">Organization Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Company -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Company</span>
                                <span class="info-block-value">{{ $user->company->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Department</span>
                                <span class="info-block-value">
                                    @if($user->department)
                                        {{ $user->department->name }}
                                        @if($user->department->isCompanyLevel())
                                            <span class="badge bg-info-subtle text-info ms-1">Company Level</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Role -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Role</span>
                                <span class="info-block-value">
                                    @if($user->role)
                                        <span class="badge-role">{{ $user->role->name }}</span>
                                        @if($user->role->scope === 'company')
                                            <span class="badge bg-primary-subtle text-primary ms-1">L{{ $user->role->hierarchy_level }}</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary ms-1">L{{ $user->role->hierarchy_level }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reporting Manager -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Reports To</span>
                                <span class="info-block-value">
                                    @if($user->manager)
                                        <a href="{{ route('users.show', $user->manager->id) }}" class="text-decoration-none">
                                            <strong>{{ $user->manager->first_name }} {{ $user->manager->last_name }}</strong>
                                        </a>
                                        <small class="text-muted d-block">{{ $user->manager->designation ?? $user->manager->role?->name ?? '' }}</small>
                                    @else
                                        <span class="text-muted">No Manager (Top Level)</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Info Card -->
        <div class="premium-card mb-4">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-person-lines-fill me-2 text-gold"></i>
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Email Address</span>
                                <a href="mailto:{{ $user->email }}" class="info-block-value text-decoration-none">
                                    {{ $user->email }}
                                </a>
                                @if($user->email_verified_at)
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Phone Number</span>
                                @if($user->phone)
                                    <a href="tel:{{ $user->phone }}" class="info-block-value text-decoration-none">
                                        {{ $user->phone }}
                                    </a>
                                @else
                                    <span class="info-block-value text-muted">Not Provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employee Code -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-credit-card-2-front"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Employee Code</span>
                                <span class="info-block-value">
                                    @if($user->employee_code)
                                        <code class="employee-code">{{ $user->employee_code }}</code>
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Last Login -->
                    <div class="col-md-6">
                        <div class="info-block">
                            <div class="info-block-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="info-block-content">
                                <span class="info-block-label">Last Login</span>
                                <span class="info-block-value">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">Never Logged In</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timestamps -->
        <div class="premium-card">
            <div class="card-body py-3">
                <div class="row text-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Created: {{ $user->created_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="bi bi-calendar-check me-1"></i>
                            Updated: {{ $user->updated_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Projects Section -->
@if($user->projects->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="premium-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <i class="bi bi-kanban me-2 text-gold"></i>
                    <span class="fw-semibold">Assigned Projects</span>
                    <span class="badge bg-gold-subtle text-gold ms-2">{{ $user->projects->count() }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($user->projects as $project)
                    <div class="col-md-4 col-lg-3">
                        <div class="project-card">
                            <div class="project-card-header">
                                <h6 class="project-name mb-1">{{ $project->name }}</h6>
                                <span class="project-access-badge">
                                    {{ ucfirst($project->pivot->access_level ?? 'member') }}
                                </span>
                            </div>
                            @if($project->location)
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $project->location }}
                            </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Direct Reports Section -->
@if($user->directReports->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="premium-card">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-people me-2 text-gold"></i>
                <span class="fw-semibold">Team Members Reporting</span>
                <span class="badge bg-gold-subtle text-gold ms-2">{{ $user->directReports->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40%">Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->directReports as $report)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            {{ strtoupper(substr($report->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $report->first_name }} {{ $report->last_name }}</div>
                                            <small class="text-muted">{{ $report->designation ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $report->role->name ?? 'No Role' }}
                                    </span>
                                </td>
                                <td>{{ $report->email }}</td>
                                <td class="text-center">
                                    @if($report->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('users.show', $report->id) }}" class="btn btn-sm btn-outline-gold">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
/* Profile Avatar */
.profile-avatar-xl {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(212, 175, 55, 0.3);
    box-shadow: 0 8px 30px rgba(212, 175, 55, 0.2);
}

.profile-avatar-xl-placeholder {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d4af37 0%, #f5d77a 50%, #d4af37 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    color: #1a1a2e;
    border: 4px solid rgba(212, 175, 55, 0.3);
    box-shadow: 0 8px 30px rgba(212, 175, 55, 0.2);
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1.25rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-active {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
}

.status-inactive {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
}

/* Stat Box */
.stat-box {
    padding: 1.25rem;
    background: rgba(212, 175, 55, 0.05);
    border: 1px solid rgba(212, 175, 55, 0.15);
    border-radius: 0.75rem;
    text-align: center;
}

.stat-box-value {
    font-size: 1.75rem;
    font-weight: 700;
}

.stat-box-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Info Block */
.info-block {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.info-block:hover {
    background: rgba(212, 175, 55, 0.05);
}

.info-block-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1));
    border-radius: 0.5rem;
    color: #d4af37;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.info-block-content {
    flex: 1;
    min-width: 0;
}

.info-block-label {
    display: block;
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-block-value {
    display: block;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-primary);
    word-break: break-word;
}

/* Badge Role */
.badge-role {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: linear-gradient(135deg, #d4af37, #f5d77a);
    color: #1a1a2e;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Employee Code */
.employee-code {
    padding: 0.25rem 0.5rem;
    background: rgba(212, 175, 55, 0.1);
    color: #d4af37;
    border-radius: 0.25rem;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.9rem;
}

/* Project Card */
.project-card {
    padding: 1rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(212, 175, 55, 0.15);
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.project-card:hover {
    background: rgba(212, 175, 55, 0.08);
    border-color: rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

.project-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.project-name {
    font-weight: 600;
    color: var(--text-primary);
}

.project-access-badge {
    font-size: 0.65rem;
    padding: 0.2rem 0.5rem;
    background: rgba(212, 175, 55, 0.15);
    color: #d4af37;
    border-radius: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Avatar SM */
.avatar-sm {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #d4af37, #f5d77a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    color: #1a1a2e;
}

/* Card Section Title */
.card-section-title {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    font-weight: 600;
}

/* Button Outline Gold */
.btn-outline-gold {
    color: #d4af37;
    border-color: rgba(212, 175, 55, 0.5);
}

.btn-outline-gold:hover {
    background: #d4af37;
    border-color: #d4af37;
    color: #1a1a2e;
}

.text-gold {
    color: #d4af37 !important;
}

.bg-gold-subtle {
    background-color: rgba(212, 175, 55, 0.15) !important;
}
</style>
@endpush
