@extends('layouts.app')

@section('title', 'Edit Project')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-pencil me-2 text-gold"></i>
                Edit Project
            </h1>
            <p class="text-muted mb-0">Update project information</p>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Project
        </a>
    </div>
</div>

<!-- Project Form -->
<div class="form-container">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-tabs-premium mb-4" id="projectTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#basicInfo">
                <i class="bi bi-info-circle me-2"></i>Basic Info
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reraLocation">
                <i class="bi bi-geo-alt me-2"></i>RERA & Location
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#landTimeline">
                <i class="bi bi-calendar-event me-2"></i>Land & Timeline
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#imagesMedia">
                <i class="bi bi-images me-2"></i>Images & Media
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#amenities">
                <i class="bi bi-stars me-2"></i>Amenities
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#towers">
                <i class="bi bi-building me-2"></i>Towers/Blocks
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#units">
                <i class="bi bi-house-door me-2"></i>Units
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specifications">
                <i class="bi bi-clipboard-data me-2"></i>Specifications
            </button>
        </li>
    </ul>

    <form id="projectForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="project_id" value="{{ $project->id }}">

        <div class="tab-content">
            <!-- Tab 1: Basic Info -->
            <div class="tab-pane fade show active" id="basicInfo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ $project->company_id == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $project->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="residential" {{ $project->type == 'residential' ? 'selected' : '' }}>Residential</option>
                            <option value="commercial" {{ $project->type == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="villa" {{ $project->type == 'villa' ? 'selected' : '' }}>Villa</option>
                            <option value="open_plots" {{ $project->type == 'open_plots' ? 'selected' : '' }}>Open Plots</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="upcoming" {{ $project->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ $project->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="sold_out" {{ $project->status == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4">{{ $project->description }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Highlights (One per line)</label>
                        <textarea class="form-control" id="highlightsInput" rows="4">{{ is_array($project->highlights) ? implode("\n", $project->highlights) : '' }}</textarea>
                        <small class="text-muted">Each line will be converted to a highlight point</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Logo</label>
                        @if($project->logo)
                        <div class="mb-2">
                            <img src="{{ $project->logo }}" alt="Current Logo" class="img-thumbnail" style="max-width: 200px;">
                            <p class="text-muted small mt-1">Current logo - upload new to replace</p>
                        </div>
                        @endif
                        <input type="file" class="form-control" name="logo" accept="image/*" id="logoInput">
                        <small class="text-muted">Recommended: 400x400px, PNG or JPG (Max 2MB)</small>
                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <img src="" alt="New Logo Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" {{ $project->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="isFeatured">
                                <i class="bi bi-star-fill text-warning"></i> Featured Project
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $project->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: RERA & Location -->
            <div class="tab-pane fade" id="reraLocation">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">RERA Number</label>
                        <input type="text" class="form-control" name="rera_number" value="{{ $project->rera_number }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Valid Until</label>
                        <input type="date" class="form-control" name="rera_valid_until" value="{{ $project->rera_valid_until?->format('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address_line1" value="{{ $project->address_line1 }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2" value="{{ $project->address_line2 }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Landmark</label>
                        <input type="text" class="form-control" name="landmark" value="{{ $project->landmark }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" value="{{ $project->city }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="state" value="{{ $project->state }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PIN Code</label>
                        <input type="text" class="form-control" name="pincode" value="{{ $project->pincode }}" maxlength="6" pattern="[0-9]{6}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" value="{{ $project->latitude }}" placeholder="e.g., 17.385044">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" value="{{ $project->longitude }}" placeholder="e.g., 78.486671">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" class="form-control" name="google_maps_url" value="{{ $project->google_maps_url }}" placeholder="https://goo.gl/maps/...">
                    </div>
                </div>
            </div>

            <!-- Tab 3: Land & Timeline -->
            <div class="tab-pane fade" id="landTimeline">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Land Area</label>
                        <input type="number" step="0.01" class="form-control" name="total_land_area" value="{{ $project->total_land_area }}" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Land Area Unit</label>
                        <select class="form-select" name="land_area_unit">
                            <option value="sqft" {{ $project->land_area_unit == 'sqft' ? 'selected' : '' }}>Square Feet (sqft)</option>
                            <option value="sqm" {{ $project->land_area_unit == 'sqm' ? 'selected' : '' }}>Square Meters (sqm)</option>
                            <option value="acres" {{ $project->land_area_unit == 'acres' ? 'selected' : '' }}>Acres</option>
                            <option value="sqyds" {{ $project->land_area_unit == 'sqyds' ? 'selected' : '' }}>Square Yards (sqyds)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Launch Date</label>
                        <input type="date" class="form-control" name="launch_date" value="{{ $project->launch_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Possession Date</label>
                        <input type="date" class="form-control" name="possession_date" value="{{ $project->possession_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Completion Percentage: <span id="completionValue">{{ $project->completion_percentage ?? 0 }}</span>%</label>
                        <input type="range" class="form-range" name="completion_percentage" min="0" max="100" value="{{ $project->completion_percentage ?? 0 }}" id="completionRange">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                            <span>75%</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: Images & Media -->
            <div class="tab-pane fade" id="imagesMedia">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-images me-2 text-gold"></i>Project Images</h5>
                        
                        <!-- Existing Images -->
                        @if($project->images->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Images</h6>
                            <div id="existingImages" class="row g-3">
                                @foreach($project->images as $image)
                                <div class="col-md-4" data-image-id="{{ $image->id }}">
                                    <div class="card">
                                        <img src="{{ asset($image->image_path) }}" class="card-img-top" alt="{{ $image->alt_text }}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <small class="d-block text-truncate"><strong>{{ $image->title ?: 'No title' }}</strong></small>
                                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $image->type)) }}</small>
                                                    @if($image->is_primary)
                                                        <span class="badge bg-warning text-dark ms-1">Primary</span>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete-image" data-image-id="{{ $image->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_images[]" class="delete-image-input" value="" disabled>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Add New Images -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-muted">Add New Images</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddImage">
                                <i class="bi bi-plus-circle me-1"></i>Add Image
                            </button>
                        </div>
                        <div id="imagesList">
                            <!-- Dynamic image rows will be added here -->
                        </div>
                        <div id="emptyImagesMessage" class="text-center text-muted py-3" style="display:none;">
                            <i class="bi bi-image" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mt-2 small">No new images to add. Click "Add Image" to upload more.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Amenities -->
            <div class="tab-pane fade" id="amenities">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-stars me-2 text-gold"></i>Project Amenities</h5>
                        
                        <!-- Existing Amenities -->
                        @if($project->amenities->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Amenities</h6>
                            <div id="existingAmenities" class="row g-2">
                                @foreach($project->amenities as $amenity)
                                <div class="col-md-6" data-amenity-id="{{ $amenity->id }}">
                                    <div class="card p-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($amenity->icon)
                                                        <i class="bi {{ $amenity->icon }}"></i>
                                                    @endif
                                                    <strong>{{ $amenity->name }}</strong>
                                                    @if($amenity->is_highlighted)
                                                        <span class="badge bg-warning text-dark">★ Highlighted</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $amenity->category_label }}</small>
                                                @if($amenity->description)
                                                    <p class="small mb-0 mt-1">{{ $amenity->description }}</p>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-amenity" data-amenity-id="{{ $amenity->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_amenities[]" class="delete-amenity-input" value="" disabled>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Add New Amenities -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-muted">Add New Amenities</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddAmenity">
                                <i class="bi bi-plus-circle me-1"></i>Add Amenity
                            </button>
                        </div>
                        <div id="amenitiesList">
                            <!-- Dynamic amenity rows will be added here -->
                        </div>
                        <div id="emptyAmenitiesMessage" class="text-center text-muted py-3" style="display:none;">
                            <i class="bi bi-stars" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mt-2 small">No new amenities to add. Click "Add Amenity" to add more.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Towers/Blocks -->
            <div class="tab-pane fade" id="towers">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-building me-2 text-gold"></i>Towers/Blocks</h5>
                        
                        @if($project->towers->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Towers</h6>
                            <div id="existingTowers" class="row g-2">
                                @foreach($project->towers as $tower)
                                <div class="col-md-6" data-tower-id="{{ $tower->id }}">
                                    <div class="card p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $tower->name }}</h6>
                                                <small class="text-muted d-block">{{ $tower->total_floors }} Floors | {{ $tower->units_per_floor ?? 'N/A' }} Units/Floor</small>
                                                <small class="text-muted d-block">Status: <span class="badge badge-sm bg-{{ $tower->status === 'completed' ? 'success' : ($tower->status === 'construction' ? 'warning' : 'secondary') }}">{{ $tower->status_label }}</span></small>
                                                @if($tower->completion_date)
                                                    <small class="text-muted d-block">Completion: {{ $tower->completion_date->format('M Y') }}</small>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-tower" data-tower-id="{{ $tower->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_towers[]" class="delete-tower-input" value="" disabled>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-muted">Add New Towers</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddTower">
                                <i class="bi bi-plus-circle me-1"></i>Add Tower
                            </button>
                        </div>
                        <div id="towersList"></div>
                        <div id="emptyTowersMessage" class="text-center text-muted py-3" style="display:none;">
                            <i class="bi bi-building" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mt-2 small">No new towers to add.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 7: Units -->
            <div class="tab-pane fade" id="units">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-house-door me-2 text-gold"></i>Project Units</h5>
                        
                        @if($project->units->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Units</h6>
                            <div id="existingUnits" class="row g-2">
                                @foreach($project->units as $unit)
                                <div class="col-md-6" data-unit-id="{{ $unit->id }}">
                                    <div class="card p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="mb-0">{{ $unit->name }}</h6>
                                                    <span class="badge bg-primary">{{ $unit->type_label }}</span>
                                                    @if(!$unit->is_active)
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block">
                                                    @if($unit->carpet_area_sqft){{ number_format($unit->carpet_area_sqft) }} sqft @endif
                                                    @if($unit->bedrooms) | {{ $unit->bedrooms }}BHK @endif
                                                    @if($unit->facing) | {{ ucfirst($unit->facing) }} Facing @endif
                                                </small>
                                                @if($unit->base_price)
                                                    <small class="text-success"><strong>₹{{ number_format($unit->base_price) }}</strong></small>
                                                @endif
                                                <small class="text-muted d-block">Available: {{ $unit->available_units }}/{{ $unit->total_units }}</small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-unit" data-unit-id="{{ $unit->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_units[]" class="delete-unit-input" value="" disabled>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-muted">Add New Units</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddUnit">
                                <i class="bi bi-plus-circle me-1"></i>Add Unit
                            </button>
                        </div>
                        <div id="unitsList"></div>
                        <div id="emptyUnitsMessage" class="text-center text-muted py-3" style="display:none;">
                            <i class="bi bi-house-door" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mt-2 small">No new units to add.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 8: Specifications -->
            <div class="tab-pane fade" id="specifications">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-clipboard-data me-2 text-gold"></i>Project Specifications</h5>
                        <p class="text-muted small">Specifications are type-specific. Current project type: <strong class="text-primary">{{ ucfirst(str_replace('_', ' ', $project->type)) }}</strong></p>
                        
                        @if($project->type === 'residential' && $project->residentialSpec)
                            @php $spec = $project->residentialSpec; @endphp
                            <div class="card p-3 mb-3"><h6 class="text-primary mb-3">Current Residential Specifications</h6><div class="row g-2 small"><div class="col-md-3"><strong>Total Towers:</strong> {{ $spec->total_towers ?? 'N/A' }}</div><div class="col-md-3"><strong>Floors/Tower:</strong> {{ $spec->total_floors_per_tower ?? 'N/A' }}</div><div class="col-md-3"><strong>Total Units:</strong> {{ $spec->total_units ?? 'N/A' }}</div><div class="col-md-3"><strong>Parking:</strong> {{ ($spec->open_parking_slots + $spec->covered_parking_slots) ?? 'N/A' }} slots</div></div></div>
                            <input type="hidden" name="residential_spec[total_towers]" value="{{ $spec->total_towers }}">
                            <input type="hidden" name="residential_spec[total_floors_per_tower]" value="{{ $spec->total_floors_per_tower }}">
                            <input type="hidden" name="residential_spec[total_units]" value="{{ $spec->total_units }}">
                            <input type="hidden" name="residential_spec[units_per_floor]" value="{{ $spec->units_per_floor }}">
                            <input type="hidden" name="residential_spec[basement_levels]" value="{{ $spec->basement_levels }}">
                            <input type="hidden" name="residential_spec[open_parking_slots]" value="{{ $spec->open_parking_slots }}">
                            <input type="hidden" name="residential_spec[covered_parking_slots]" value="{{ $spec->covered_parking_slots }}">
                            <input type="hidden" name="residential_spec[clubhouse_area_sqft]" value="{{ $spec->clubhouse_area_sqft }}">
                            @if($spec->stilt_parking)<input type="hidden" name="residential_spec[stilt_parking]" value="1">@endif
                            @if($spec->podium_level)<input type="hidden" name="residential_spec[podium_level]" value="1">@endif
                        @elseif($project->type === 'commercial' && $project->commercialSpec)
                            @php $spec = $project->commercialSpec; @endphp
                            <div class="card p-3 mb-3"><h6 class="text-primary mb-3">Current Commercial Specifications</h6><div class="row g-2 small"><div class="col-md-4"><strong>Total Floors:</strong> {{ $spec->total_floors ?? 'N/A' }}</div><div class="col-md-4"><strong>Office Units:</strong> {{ $spec->office_units ?? 'N/A' }}</div><div class="col-md-4"><strong>Retail Units:</strong> {{ $spec->retail_units ?? 'N/A' }}</div>@if($spec->green_building_certified)<div class="col-12"><span class="badge bg-success"><i class="bi bi-award"></i> {{ $spec->certification_type ?? 'Green Certified' }}</span></div>@endif</div></div>
                            <input type="hidden" name="commercial_spec[total_floors]" value="{{ $spec->total_floors }}">
                            <input type="hidden" name="commercial_spec[total_units]" value="{{ $spec->total_units }}">
                            <input type="hidden" name="commercial_spec[office_units]" value="{{ $spec->office_units }}">
                            <input type="hidden" name="commercial_spec[retail_units]" value="{{ $spec->retail_units }}">
                            <input type="hidden" name="commercial_spec[food_court_area_sqft]" value="{{ $spec->food_court_area_sqft }}">
                            <input type="hidden" name="commercial_spec[common_area_percentage]" value="{{ $spec->common_area_percentage }}">
                            <input type="hidden" name="commercial_spec[visitor_parking_slots]" value="{{ $spec->visitor_parking_slots }}">
                            <input type="hidden" name="commercial_spec[tenant_parking_slots]" value="{{ $spec->tenant_parking_slots }}">
                            @if($spec->green_building_certified)<input type="hidden" name="commercial_spec[green_building_certified]" value="1">@endif
                            <input type="hidden" name="commercial_spec[certification_type]" value="{{ $spec->certification_type }}">
                        @elseif($project->type === 'villa' && $project->villaSpec)
                            @php $spec = $project->villaSpec; @endphp
                            <div class="card p-3 mb-3"><h6 class="text-primary mb-3">Current Villa Specifications</h6><div class="row g-2 small"><div class="col-md-4"><strong>Total Villas:</strong> {{ $spec->total_villas ?? 'N/A' }}</div><div class="col-md-4"><strong>Villa Types:</strong> {{ $spec->villa_types ?? 'N/A' }}</div><div class="col-md-4"><strong>Floors/Villa:</strong> {{ $spec->floors_per_villa ?? 'N/A' }}</div><div class="col-12">@if($spec->private_pool)<span class="badge bg-info me-1">Private Pool</span>@endif @if($spec->private_garden)<span class="badge bg-success me-1">Private Garden</span>@endif @if($spec->gated_community)<span class="badge bg-primary">Gated Community</span>@endif</div></div></div>
                            <input type="hidden" name="villa_spec[total_villas]" value="{{ $spec->total_villas }}">
                            <input type="hidden" name="villa_spec[villa_types]" value="{{ $spec->villa_types }}">
                            <input type="hidden" name="villa_spec[floors_per_villa]" value="{{ $spec->floors_per_villa }}">
                            <input type="hidden" name="villa_spec[car_parking_per_villa]" value="{{ $spec->car_parking_per_villa }}">
                            <input type="hidden" name="villa_spec[clubhouse_area_sqft]" value="{{ $spec->clubhouse_area_sqft }}">
                            @if($spec->private_garden)<input type="hidden" name="villa_spec[private_garden]" value="1">@endif
                            @if($spec->private_pool)<input type="hidden" name="villa_spec[private_pool]" value="1">@endif
                            @if($spec->servant_quarters)<input type="hidden" name="villa_spec[servant_quarters]" value="1">@endif
                            @if($spec->gated_community)<input type="hidden" name="villa_spec[gated_community]" value="1">@endif
                        @elseif($project->type === 'open_plot' && $project->openPlotSpec)
                            @php $spec = $project->openPlotSpec; @endphp
                            <div class="card p-3 mb-3"><h6 class="text-primary mb-3">Current Open Plot Specifications</h6><div class="row g-2 small"><div class="col-md-4"><strong>Total Plots:</strong> {{ $spec->total_plots ?? 'N/A' }}</div><div class="col-md-4"><strong>Plot Size Range:</strong> {{ $spec->min_plot_size_sqyds ?? 'N/A' }} - {{ $spec->max_plot_size_sqyds ?? 'N/A' }} sqyds</div><div class="col-12"><strong>Infrastructure:</strong> @foreach($spec->infrastructure_features as $feature)<span class="badge bg-success me-1">{{ $feature }}</span>@endforeach</div></div></div>
                            <input type="hidden" name="open_plot_spec[total_plots]" value="{{ $spec->total_plots }}">
                            <input type="hidden" name="open_plot_spec[min_plot_size_sqyds]" value="{{ $spec->min_plot_size_sqyds }}">
                            <input type="hidden" name="open_plot_spec[max_plot_size_sqyds]" value="{{ $spec->max_plot_size_sqyds }}">
                            <input type="hidden" name="open_plot_spec[park_area_sqft]" value="{{ $spec->park_area_sqft }}">
                            <input type="hidden" name="open_plot_spec[community_hall_sqft]" value="{{ $spec->community_hall_sqft }}">
                            @if($spec->underground_drainage)<input type="hidden" name="open_plot_spec[underground_drainage]" value="1">@endif
                            @if($spec->underground_electricity)<input type="hidden" name="open_plot_spec[underground_electricity]" value="1">@endif
                            @if($spec->water_supply)<input type="hidden" name="open_plot_spec[water_supply]" value="1">@endif
                            @if($spec->compound_wall)<input type="hidden" name="open_plot_spec[compound_wall]" value="1">@endif
                            @if($spec->avenue_plantation)<input type="hidden" name="open_plot_spec[avenue_plantation]" value="1">@endif
                            @if($spec->fencing)<input type="hidden" name="open_plot_spec[fencing]" value="1">@endif
                        @else
                            <div class="alert alert-info">No specifications added yet. Specs will be saved when you update the project.</div>
                        @endif
                        
                        <p class="text-muted small mt-3"><i class="bi bi-info-circle"></i> Specification values are retained for update. To modify, edit the values in Tab 8 on create form.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-maroon">
                <i class="bi bi-check-circle me-2"></i>Update Project
            </button>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-container {
        background: white;
        border: 1px solid rgba(184, 149, 106, 0.2);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
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
    .form-control,
    .form-select {
        border: 1px solid rgba(184, 149, 106, 0.3);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }

    .form-label {
        font-weight: 600;
        color: var(--color-dark-maroon);
        margin-bottom: 0.5rem;
    }

    .form-check-input:checked {
        background-color: var(--color-coffee-gold);
        border-color: var(--color-coffee-gold);
    }

    .form-range::-webkit-slider-thumb {
        background: var(--color-coffee-gold);
    }

    .form-range::-moz-range-thumb {
        background: var(--color-coffee-gold);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(184, 149, 106, 0.2);
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
</style>
@endpush

@push('scripts')
<script>
    // Logo preview for new upload
    document.getElementById('logoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // ==================== IMAGE MANAGEMENT (DELETE EXISTING) ====================
    document.querySelectorAll('.btn-delete-image').forEach(btn => {
        btn.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            const imageCard = this.closest('[data-image-id]');
            const deleteInput = imageCard.querySelector('.delete-image-input');
            
            if (confirm('Are you sure you want to delete this image?')) {
                // Mark for deletion
                deleteInput.value = imageId;
                deleteInput.disabled = false;
                // Hide visually
                imageCard.style.opacity = '0.3';
                imageCard.style.pointerEvents = 'none';
                this.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
                this.classList.remove('btn-danger');
                this.classList.add('btn-warning');
                this.onclick = function(e) {
                    e.preventDefault();
                    // Undo deletion
                    deleteInput.value = '';
                    deleteInput.disabled = true;
                    imageCard.style.opacity = '1';
                    imageCard.style.pointerEvents = 'auto';
                    location.reload(); // Refresh to restore original state
                };
            }
        });
    });

    // ==================== IMAGE MANAGEMENT (ADD NEW) ====================
    let imageIndex = 0;
    document.getElementById('btnAddImage')?.addEventListener('click', () => addImageRow());
    
    function addImageRow() {
        const list = document.getElementById('imagesList');
        const empty = document.getElementById('emptyImagesMessage');
        const row = document.createElement('div');
        row.className = 'image-row card mb-3 p-3';
        row.innerHTML = `<div class="row g-2">
            <div class="col-md-3"><label class="form-label small">Image <span class="text-danger">*</span></label><input type="file" class="form-control form-control-sm img-file" name="images[${imageIndex}][file]" accept="image/*" required><div class="img-preview mt-2" style="display:none"><img src="" class="img-thumbnail" style="max-width:100%;max-height:120px"></div></div>
            <div class="col-md-2"><label class="form-label small">Title</label><input type="text" class="form-control form-control-sm" name="images[${imageIndex}][title]"></div>
            <div class="col-md-2"><label class="form-label small">Type <span class="text-danger">*</span></label><select class="form-select form-select-sm" name="images[${imageIndex}][type]" required><option value="gallery">Gallery</option><option value="floor_plan">Floor Plan</option><option value="master_plan">Master Plan</option><option value="brochure">Brochure</option><option value="elevation">Elevation</option><option value="amenity">Amenity</option><option value="other">Other</option></select></div>
            <div class="col-md-2"><label class="form-label small">Alt Text</label><input type="text" class="form-control form-control-sm" name="images[${imageIndex}][alt_text]"></div>
            <div class="col-md-1"><label class="form-label small">Sort</label><input type="number" class="form-control form-control-sm" name="images[${imageIndex}][sort_order]" value="0"></div>
            <div class="col-md-2 text-end"><label class="form-label small d-block">&nbsp;</label><div class="form-check form-check-inline"><input class="form-check-input img-primary" type="checkbox" name="images[${imageIndex}][is_primary]" value="1"><label class="form-check-label small">Primary</label></div><button type="button" class="btn btn-sm btn-danger btn-remove-img ms-2"><i class="bi bi-trash"></i></button></div>
        </div>`;
        
        list.appendChild(row);
        if (empty) empty.style.display = 'none';
        
        row.querySelector('.img-file').addEventListener('change', function(e) {
            if (e.target.files?.[0]) {
                const file = e.target.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB
                
                if (file.size > maxSize) {
                    alert(`File "${file.name}" is too large!\n\nFile size: ${(file.size / 1024 / 1024).toFixed(2)}MB\nMaximum allowed: 10MB\n\nPlease compress the image or choose a smaller file.`);
                    e.target.value = ''; // Clear the input
                    return;
                }
                
                const reader = new FileReader();
                const preview = row.querySelector('.img-preview');
                reader.onload = ev => { preview.querySelector('img').src = ev.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
        
        row.querySelector('.btn-remove-img').addEventListener('click', () => {
            row.remove();
            if (!document.querySelectorAll('.image-row').length && empty) empty.style.display = 'block';
        });
        
        row.querySelector('.img-primary').addEventListener('change', function() {
            if (this.checked) document.querySelectorAll('.img-primary').forEach(cb => { if (cb !== this) cb.checked = false; });
        });
        
        imageIndex++;
    }

    // ==================== AMENITY MANAGEMENT (DELETE EXISTING) ====================
    document.querySelectorAll('.btn-delete-amenity').forEach(btn => {
        btn.addEventListener('click', function() {
            const amenityId = this.dataset.amenityId;
            const amenityCard = this.closest('[data-amenity-id]');
            const deleteInput = amenityCard.querySelector('.delete-amenity-input');
            
            if (confirm('Are you sure you want to delete this amenity?')) {
                // Mark for deletion
                deleteInput.value = amenityId;
                deleteInput.disabled = false;
                // Hide visually
                amenityCard.style.opacity = '0.3';
                amenityCard.style.pointerEvents = 'none';
                this.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i>';
                this.classList.remove('btn-danger');
                this.classList.add('btn-warning');
                this.onclick = function(e) {
                    e.preventDefault();
                    location.reload(); // Refresh to restore
                };
            }
        });
    });


    // Completion percentage slider
    document.getElementById('completionRange')?.addEventListener('input', function(e) {
        document.getElementById('completionValue').textContent = e.target.value;
    });

    // Form submission
    document.getElementById('projectForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Handle highlights (convert textarea to array for FormData)
        const highlightsText = document.getElementById('highlightsInput').value;
        if (highlightsText.trim()) {
            const highlightsArray = highlightsText.split('\n').filter(line => line.trim());
            formData.delete('highlights');
            // Append as array items: highlights[0], highlights[1], etc.
            highlightsArray.forEach((highlight, index) => {
                formData.append(`highlights[${index}]`, highlight);
            });
        }

        // DEBUG: Log FormData contents
        console.log('=== FORM SUBMISSION DEBUG ===');
        console.log('Total FormData entries:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(`${pair[0]}: [File] ${pair[1].name} (${pair[1].size} bytes)`);
            } else {
                console.log(`${pair[0]}: ${pair[1]}`);
            }
        }
        console.log('=== END DEBUG ===');


        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';

        try {
            const response = await fetch('{{ route("projects.update", $project) }}', {
                method: 'POST', // Using POST with _method=PUT spoofing
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!response.ok) {
                // Handle validation errors
                if (data.errors) {
                    let errorMessage = 'Please fix the following errors:\n';
                    Object.values(data.errors).forEach(errors => {
                        errors.forEach(error => errorMessage += '• ' + error + '\n');
                    });
                    alert(errorMessage);
                } else {
                    throw new Error(data.message || 'Failed to update project');
                }
            } else {
                // Success - redirect to project show page
                showToast('Project updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = `/projects/${data.data.id}`;
                }, 1000);
            }
        } catch (error) {
            console.error('Error updating project:', error);
            alert('Failed to update project. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Toast notification
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