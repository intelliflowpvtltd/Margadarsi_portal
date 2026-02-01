@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">Master Data Management</h4>
        <p class="text-muted mb-0">Manage system configuration and lookup data</p>
    </div>

    <!-- Master Categories Grid -->
    <div class="row g-4">
        <!-- Location Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-primary-subtle">
                        <i class="bi bi-geo-alt text-primary"></i>
                    </div>
                    <h5 class="card-title">Location Masters</h5>
                    <p class="card-text text-muted">Countries, States, and Cities</p>
                    <div class="master-links">
                        <a href="{{ route('masters.countries.index') }}" class="btn btn-outline-primary btn-sm">Countries</a>
                        <a href="{{ route('masters.states.index') }}" class="btn btn-outline-primary btn-sm">States</a>
                        <a href="{{ route('masters.cities.index') }}" class="btn btn-outline-primary btn-sm">Cities</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-success-subtle">
                        <i class="bi bi-house text-success"></i>
                    </div>
                    <h5 class="card-title">Property Masters</h5>
                    <p class="card-text text-muted">Property types and statuses</p>
                    <div class="master-links">
                        <a href="{{ route('masters.property-types.index') }}" class="btn btn-outline-success btn-sm">Types</a>
                        <a href="{{ route('masters.property-statuses.index') }}" class="btn btn-outline-success btn-sm">Statuses</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenity Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-info-subtle">
                        <i class="bi bi-stars text-info"></i>
                    </div>
                    <h5 class="card-title">Amenity Masters</h5>
                    <p class="card-text text-muted">Amenity categories and items</p>
                    <div class="master-links">
                        <a href="{{ route('masters.amenity-categories.index') }}" class="btn btn-outline-info btn-sm">Categories</a>
                        <a href="{{ route('masters.amenities.index') }}" class="btn btn-outline-info btn-sm">Amenities</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lead Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-warning-subtle">
                        <i class="bi bi-funnel text-warning"></i>
                    </div>
                    <h5 class="card-title">Lead Masters</h5>
                    <p class="card-text text-muted">Sources, statuses, budgets, timelines</p>
                    <div class="master-links">
                        <a href="{{ route('masters.lead-sources.index') }}" class="btn btn-outline-warning btn-sm">Sources</a>
                        <a href="{{ route('masters.lead-statuses.index') }}" class="btn btn-outline-warning btn-sm">Statuses</a>
                        <a href="{{ route('masters.budget-ranges.index') }}" class="btn btn-outline-warning btn-sm">Budgets</a>
                        <a href="{{ route('masters.timelines.index') }}" class="btn btn-outline-warning btn-sm">Timelines</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specification Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-secondary-subtle">
                        <i class="bi bi-list-check text-secondary"></i>
                    </div>
                    <h5 class="card-title">Specification Masters</h5>
                    <p class="card-text text-muted">Specification categories and types</p>
                    <div class="master-links">
                        <a href="{{ route('masters.specification-categories.index') }}" class="btn btn-outline-secondary btn-sm">Categories</a>
                        <a href="{{ route('masters.specification-types.index') }}" class="btn btn-outline-secondary btn-sm">Types</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generic Masters -->
        <div class="col-md-6 col-lg-4">
            <div class="card master-card h-100">
                <div class="card-body">
                    <div class="master-icon bg-dark-subtle">
                        <i class="bi bi-gear text-dark"></i>
                    </div>
                    <h5 class="card-title">Generic Masters</h5>
                    <p class="card-text text-muted">Custom configurable masters</p>
                    <div class="master-links">
                        <a href="{{ route('masters.generic-masters.index') }}" class="btn btn-outline-dark btn-sm">Manage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .master-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .master-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(184, 149, 106, 0.15);
    }
    
    .master-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .master-icon i {
        font-size: 1.5rem;
    }
    
    .master-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
</style>
@endpush
