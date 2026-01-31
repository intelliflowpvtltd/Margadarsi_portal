@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-speedometer2 me-2 text-gold"></i>
        Dashboard
    </h1>
    <p class="text-muted">Welcome back, {{ auth()->user()->first_name }}! Here's an overview of your portal.</p>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <!-- Total Companies -->
    <div class="col-md-3">
        <div class="luxury-card text-center">
            <div class="mb-3">
                <i class="bi bi-building" style="font-size: 3rem; color: var(--color-secondary);"></i>
            </div>
            <h3 class="text-maroon mb-1">{{ \App\Models\Company::count() }}</h3>
            <p class="text-muted mb-0">Total Companies</p>
        </div>
    </div>

    <!-- Total Projects -->
    <div class="col-md-3">
        <div class="luxury-card text-center">
            <div class="mb-3">
                <i class="bi bi-briefcase" style="font-size: 3rem; color: var(--color-gold);"></i>
            </div>
            <h3 class="text-gold mb-1">{{ \App\Models\Project::count() }}</h3>
            <p class="text-muted mb-0">Total Projects</p>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-md-3">
        <div class="luxury-card text-center">
            <div class="mb-3">
                <i class="bi bi-people" style="font-size: 3rem; color: var(--color-secondary);"></i>
            </div>
            <h3 class="text-maroon mb-1">{{ \App\Models\User::count() }}</h3>
            <p class="text-muted mb-0">Total Users</p>
        </div>
    </div>

    <!-- Active Projects -->
    <div class="col-md-3">
        <div class="luxury-card text-center">
            <div class="mb-3">
                <i class="bi bi-bar-chart-fill" style="font-size: 3rem; color: var(--color-gold);"></i>
            </div>
            <h3 class="text-gold mb-1">{{ \App\Models\Project::where('is_active', true)->count() }}</h3>
            <p class="text-muted mb-0">Active Projects</p>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row g-4">
    <!-- Recent Projects -->
    <div class="col-md-8">
        <div class="luxury-card">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-clock-history me-2 text-gold"></i>
                    Recent Projects
                </h3>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-gold">
                    View All
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>City</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Project::latest()->take(5)->get() as $project)
                        <tr>
                            <td class="fw-medium">{{ $project->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($project->type) }}</span>
                            </td>
                            <td>
                                @php
                                $statusColors = [
                                'upcoming' => 'info',
                                'ongoing' => 'warning',
                                'completed' => 'success',
                                'on-hold' => 'danger'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>{{ $project->city }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="luxury-card">
            <div class="luxury-card-header">
                <h3 class="luxury-card-title">
                    <i class="bi bi-lightning-charge me-2 text-gold"></i>
                    Quick Actions
                </h3>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('companies.index') }}" class="btn btn-outline-gold">
                    <i class="bi bi-building me-2"></i>
                    Manage Companies
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-gold">
                    <i class="bi bi-briefcase me-2"></i>
                    Manage Projects
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-outline-gold">
                    <i class="bi bi-people me-2"></i>
                    Manage Users
                </a>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-gold">
                    <i class="bi bi-shield-check me-2"></i>
                    Manage Roles
                </a>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="luxury-card mt-4">
            <div class="text-center">
                <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                </div>
                <h4 class="mb-1">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                <p class="text-muted mb-2">{{ auth()->user()->role->name }}</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-envelope me-1"></i>
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add fade-in animation
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.luxury-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('fade-in');
            }, index * 50);
        });
    });
</script>
@endpush