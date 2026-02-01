@extends('layouts.app')

@section('title', 'Create Project')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
<li class="breadcrumb-item active">Create Project</li>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="bi bi-plus-circle me-2 text-gold"></i>
                Create New Project
            </h1>
            <p class="text-muted mb-0">Add a new real estate project to your portfolio</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Projects
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

    <form id="projectForm" enctype="multipart/form-data" novalidate>
        @csrf
        <div class="tab-content">
            <!-- Tab 1: Basic Info -->
            <div class="tab-pane fade show active" id="basicInfo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach(\App\Models\Company::where('is_active', true)->orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="villa">Villa</option>
                            <option value="open_plots">Open Plots</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing" selected>Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="sold_out">Sold Out</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Brief description about the project"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Highlights (One per line)</label>
                        <textarea class="form-control" id="highlightsInput" rows="4" placeholder="Enter project highlights, one per line"></textarea>
                        <small class="text-muted">Each line will be converted to a highlight point</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Logo</label>
                        <input type="file" class="form-control" name="logo" accept="image/*" id="logoInput">
                        <small class="text-muted">Recommended: 400x400px, PNG or JPG (Max 2MB)</small>
                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <img src="" alt="Logo Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1">
                            <label class="form-check-label" for="isFeatured">
                                <i class="bi bi-star-fill text-warning"></i> Featured Project
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
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
                        <input type="text" class="form-control" name="rera_number">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Valid Until</label>
                        <input type="date" class="form-control" name="rera_valid_until">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" name="address_line1">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Landmark</label>
                        <input type="text" class="form-control" name="landmark">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="state" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PIN Code</label>
                        <input type="text" class="form-control" name="pincode" maxlength="6" pattern="[0-9]{6}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" placeholder="e.g., 17.385044">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" placeholder="e.g., 78.486671">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" class="form-control" name="google_maps_url" placeholder="https://goo.gl/maps/...">
                    </div>
                </div>
            </div>

            <!-- Tab 3: Land & Timeline -->
            <div class="tab-pane fade" id="landTimeline">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Land Area</label>
                        <input type="number" step="0.01" class="form-control" name="total_land_area" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Land Area Unit</label>
                        <select class="form-select" name="land_area_unit">
                            <option value="sqft">Square Feet (sqft)</option>
                            <option value="sqm">Square Meters (sqm)</option>
                            <option value="acres">Acres</option>
                            <option value="sqyds">Square Yards (sqyds)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Launch Date</label>
                        <input type="date" class="form-control" name="launch_date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Possession Date</label>
                        <input type="date" class="form-control" name="possession_date">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Completion Percentage: <span id="completionValue">0</span>%</label>
                        <input type="range" class="form-range" name="completion_percentage" min="0" max="100" value="0" id="completionRange">
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-images me-2 text-gold"></i>Project Images</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddImage">
                                <i class="bi bi-plus-circle me-1"></i>Add Image
                            </button>
                        </div>
                        <div id="imagesList">
                            <!-- Dynamic image rows will be added here -->
                        </div>
                        <div id="emptyImagesMessage" class="text-center text-muted py-5">
                            <i class="bi bi-image" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No images added yet. Click "Add Image" to upload project images.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Amenities -->
            <div class="tab-pane fade" id="amenities">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-stars me-2 text-gold"></i>Project Amenities</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddAmenity">
                                <i class="bi bi-plus-circle me-1"></i>Add Amenity
                            </button>
                        </div>
                        <div id="amenitiesList">
                            <!-- Dynamic amenity rows will be added here -->
                        </div>
                        <div id="emptyAmenitiesMessage" class="text-center text-muted py-5">
                            <i class="bi bi-stars" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No amenities added yet. Click "Add Amenity" to add project amenities.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 6: Towers/Blocks -->
            <div class="tab-pane fade" id="towers">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-building me-2 text-gold"></i>Towers/Blocks</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddTower">
                                <i class="bi bi-plus-circle me-1"></i>Add Tower
                            </button>
                        </div>
                        <div id="towersList">
                            <!-- Dynamic tower rows will be added here -->
                        </div>
                        <div id="emptyTowersMessage" class="text-center text-muted py-5">
                            <i class="bi bi-building" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No towers/blocks added yet. Click "Add Tower" to add building towers.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 7: Units -->
            <div class="tab-pane fade" id="units">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-house-door me-2 text-gold"></i>Project Units</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="btnAddUnit">
                                <i class="bi bi-plus-circle me-1"></i>Add Unit
                            </button>
                        </div>
                        <div id="unitsList">
                            <!-- Dynamic unit rows will be added here -->
                        </div>
                        <div id="emptyUnitsMessage" class="text-center text-muted py-5">
                            <i class="bi bi-house-door" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2">No units added yet. Click "Add Unit" to define project units.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 8: Specifications -->
            <div class="tab-pane fade" id="specifications">
                <div class="row g-3">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-clipboard-data me-2 text-gold"></i>Project Specifications</h5>
                        <p class="text-muted small">Specification fields will appear based on the <strong>project type</strong> selected in Tab 1.</p>
                        
                        <!-- Residential Specifications -->
                        <div id="residentialSpec" class="spec-form" style="display:none;">
                            <h6 class="border-bottom pb-2 mb-3">Residential Project Specifications</h6>
                            <div class="row g-2">
                                <div class="col-md-3"><label class="form-label small">Total Towers</label><input type="number" class="form-control form-control-sm" name="residential_spec[total_towers]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Floors per Tower</label><input type="number" class="form-control form-control-sm" name="residential_spec[total_floors_per_tower]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Total Units</label><input type="number" class="form-control form-control-sm" name="residential_spec[total_units]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Units per Floor</label><input type="number" class="form-control form-control-sm" name="residential_spec[units_per_floor]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Basement Levels</label><input type="number" class="form-control form-control-sm" name="residential_spec[basement_levels]" min="0" value="0"></div>
                                <div class="col-md-3"><label class="form-label small">Open Parking</label><input type="number" class="form-control form-control-sm" name="residential_spec[open_parking_slots]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Covered Parking</label><input type="number" class="form-control form-control-sm" name="residential_spec[covered_parking_slots]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Clubhouse Area (sqft)</label><input type="number" class="form-control form-control-sm" name="residential_spec[clubhouse_area_sqft]" step="0.01" min="0"></div>
                                <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="residential_spec[stilt_parking]" value="1"><label class="form-check-label small">Stilt Parking</label></div></div>
                                <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="residential_spec[podium_level]" value="1"><label class="form-check-label small">Podium Level</label></div></div>
                            </div>
                        </div>
                        
                        <!-- Commercial Specifications -->
                        <div id="commercialSpec" class="spec-form" style="display:none;">
                            <h6 class="border-bottom pb-2 mb-3">Commercial Project Specifications</h6>
                            <div class="row g-2">
                                <div class="col-md-3"><label class="form-label small">Total Floors</label><input type="number" class="form-control form-control-sm" name="commercial_spec[total_floors]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Total Units</label><input type="number" class="form-control form-control-sm" name="commercial_spec[total_units]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Office Units</label><input type="number" class="form-control form-control-sm" name="commercial_spec[office_units]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Retail Units</label><input type="number" class="form-control form-control-sm" name="commercial_spec[retail_units]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Food Court Area (sqft)</label><input type="number" class="form-control form-control-sm" name="commercial_spec[food_court_area_sqft]" step="0.01" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Common Area %</label><input type="number" class="form-control form-control-sm" name="commercial_spec[common_area_percentage]" step="0.01" min="0" max="100"></div>
                                <div class="col-md-3"><label class="form-label small">Visitor Parking</label><input type="number" class="form-control form-control-sm" name="commercial_spec[visitor_parking_slots]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Tenant Parking</label><input type="number" class="form-control form-control-sm" name="commercial_spec[tenant_parking_slots]" min="0"></div>
                                <div class="col-md-4"><label class="form-label small">Certification Type</label><input type="text" class="form-control form-control-sm" name="commercial_spec[certification_type]" placeholder="e.g. LEED Gold"></div>
                                <div class="col-md-2"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="commercial_spec[green_building_certified]" value="1"><label class="form-check-label small">Green Certified</label></div></div>
                            </div>
                        </div>
                        
                        <!-- Villa Specifications -->
                        <div id="villaSpec" class="spec-form" style="display:none;">
                            <h6 class="border-bottom pb-2 mb-3">Villa Project Specifications</h6>
                            <div class="row g-2">
                                <div class="col-md-3"><label class="form-label small">Total Villas</label><input type="number" class="form-control form-control-sm" name="villa_spec[total_villas]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Villa Types</label><input type="text" class="form-control form-control-sm" name="villa_spec[villa_types]" placeholder="e.g. 3BHK, 4BHK"></div>
                                <div class="col-md-3"><label class="form-label small">Floors per Villa</label><input type="number" class="form-control form-control-sm" name="villa_spec[floors_per_villa]" min="1"></div>
                                <div class="col-md-3"><label class="form-label small">Car Parking/Villa</label><input type="number" class="form-control form-control-sm" name="villa_spec[car_parking_per_villa]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Clubhouse Area (sqft)</label><input type="number" class="form-control form-control-sm" name="villa_spec[clubhouse_area_sqft]" step="0.01" min="0"></div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="villa_spec[private_garden]" value="1"><label class="form-check-label small">Private Garden</label></div></div>
                                        <div class="col"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="villa_spec[private_pool]" value="1"><label class="form-check-label small">Private Pool</label></div></div>
                                        <div class="col"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="villa_spec[servant_quarters]" value="1"><label class="form-check-label small">Servant Quarters</label></div></div>
                                        <div class="col"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="villa_spec[gated_community]" value="1"><label class="form-check-label small">Gated Community</label></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Open Plot Specifications -->
                        <div id="openPlotSpec" class="spec-form" style="display:none;">
                            <h6 class="border-bottom pb-2 mb-3">Open Plot Project Specifications</h6>
                            <div class="row g-2">
                                <div class="col-md-3"><label class="form-label small">Total Plots</label><input type="number" class="form-control form-control-sm" name="open_plot_spec[total_plots]" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Min Plot Size (sqyds)</label><input type="number" class="form-control form-control-sm" name="open_plot_spec[min_plot_size_sqyds]" step="0.01" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Max Plot Size (sqyds)</label><input type="number" class="form-control form-control-sm" name="open_plot_spec[max_plot_size_sqyds]" step="0.01" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Park Area (sqft)</label><input type="number" class="form-control form-control-sm" name="open_plot_spec[park_area_sqft]" step="0.01" min="0"></div>
                                <div class="col-md-3"><label class="form-label small">Community Hall (sqft)</label><input type="number" class="form-control form-control-sm" name="open_plot_spec[community_hall_sqft]" step="0.01" min="0"></div>
                                <div class="col-md-9">
                                    <label class="form-label small">Infrastructure Features:</label>
                                    <div class="row">
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[underground_drainage]" value="1"><label class="form-check-label small">Underground Drainage</label></div></div>
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[underground_electricity]" value="1"><label class="form-check-label small">Underground Electricity</label></div></div>
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[water_supply]" value="1"><label class="form-check-label small">Water Supply</label></div></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[compound_wall]" value="1"><label class="form-check-label small">Compound Wall</label></div></div>
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[avenue_plantation]" value="1"><label class="form-check-label small">Avenue Plantation</label></div></div>
                                        <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="open_plot_spec[fencing]" value="1"><label class="form-check-label small">Plot Fencing</label></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-maroon">
                <i class="bi bi-check-circle me-2"></i>Create Project
            </button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
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
    // Logo preview
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

    // ==================== IMAGE MANAGEMENT ====================
    let imageIndex = 0;
    document.getElementById('btnAddImage')?.addEventListener('click', () => addImageRow());
    
    function addImageRow() {
        const list = document.getElementById('imagesList');
        const empty = document.getElementById('emptyImagesMessage');
        const row = document.createElement('div');
        row.className = 'image-row card mb-3 p-3';
        row.innerHTML = `<div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small">Image <span class="text-danger">*</span></label>
                <input type="file" class="form-control form-control-sm img-file" name="images[${imageIndex}][file]" accept="image/*" required>
                <div class="img-preview mt-2" style="display:none"><img src="" class="img-thumbnail" style="max-width:100%;max-height:120px"></div>
            </div>
            <div class="col-md-2"><label class="form-label small">Title</label><input type="text" class="form-control form-control-sm" name="images[${imageIndex}][title]"></div>
            <div class="col-md-2"><label class="form-label small">Type <span class="text-danger">*</span></label><select class="form-select form-select-sm" name="images[${imageIndex}][type]" required><option value="gallery">Gallery</option><option value="floor_plan">Floor Plan</option><option value="master_plan">Master Plan</option><option value="brochure">Brochure</option><option value="elevation">Elevation</option><option value="amenity">Amenity</option><option value="other">Other</option></select></div>
            <div class="col-md-2"><label class="form-label small">Alt Text</label><input type="text" class="form-control form-control-sm" name="images[${imageIndex}][alt_text]"></div>
            <div class="col-md-1"><label class="form-label small">Sort</label><input type="number" class="form-control form-control-sm" name="images[${imageIndex}][sort_order]" value="0"></div>
            <div class="col-md-2 text-end"><label class="form-label small d-block">&nbsp;</label><div class="form-check form-check-inline"><input class="form-check-input img-primary" type="checkbox" name="images[${imageIndex}][is_primary]" value="1"><label class="form-check-label small">Primary</label></div><button type="button" class="btn btn-sm btn-danger btn-remove-img ms-2"><i class="bi bi-trash"></i></button></div>
        </div>`;
        
        list.appendChild(row);
        empty.style.display = 'none';
        
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
            if (!document.querySelectorAll('.image-row').length) empty.style.display = 'block';
        });
        
        row.querySelector('.img-primary').addEventListener('change', function() {
            if (this.checked) document.querySelectorAll('.img-primary').forEach(cb => { if (cb !== this) cb.checked = false; });
        });
        
        imageIndex++;
    }

    // ==================== AMENITY MANAGEMENT ====================
    let amenityIndex = 0;
    document.getElementById('btnAddAmenity')?.addEventListener('click', () => addAmenityRow());
    
    function addAmenityRow() {
        const list = document.getElementById('amenitiesList');
        const empty = document.getElementById('emptyAmenitiesMessage');
        const row = document.createElement('div');
        row.className = 'amenity-row card mb-3 p-3';
        row.innerHTML = `<div class="row g-2">
            <div class="col-md-3"><label class="form-label small">Name <span class="text-danger">*</span></label><input type="text" class="form-control form-control-sm" name="amenities[${amenityIndex}][name]" placeholder="e.g. Swimming Pool" required></div>
            <div class="col-md-2"><label class="form-label small">Category <span class="text-danger">*</span></label><select class="form-select form-select-sm" name="amenities[${amenityIndex}][category]" required><option value="lifestyle">Lifestyle</option><option value="sports">Sports & Recreation</option><option value="convenience">Convenience</option><option value="security">Security</option><option value="kids">Kids</option><option value="health">Health & Wellness</option><option value="green">Green Spaces</option><option value="other">Other</option></select></div>
            <div class="col-md-2"><label class="form-label small">Icon</label><input type="text" class="form-control form-control-sm" name="amenities[${amenityIndex}][icon]" placeholder="bi-swimming"></div>
            <div class="col-md-3"><label class="form-label small">Description</label><input type="text" class="form-control form-control-sm" name="amenities[${amenityIndex}][description]"></div>
            <div class="col-md-1"><label class="form-label small">Sort</label><input type="number" class="form-control form-control-sm" name="amenities[${amenityIndex}][sort_order]" value="0"></div>
            <div class="col-md-1 text-end"><label class="form-label small d-block">&nbsp;</label><div class="form-check mb-1"><input class="form-check-input" type="checkbox" name="amenities[${amenityIndex}][is_highlighted]" value="1"><label class="form-check-label small">Highlight</label></div><button type="button" class="btn btn-sm btn-danger btn-remove-amenity w-100"><i class="bi bi-trash"></i></button></div>
        </div>`;
        
        list.appendChild(row);
        if (empty) empty.style.display = 'none';
        
        row.querySelector('.btn-remove-amenity').addEventListener('click', () => {
            row.remove();
            if (!document.querySelectorAll('.amenity-row').length && empty) empty.style.display = 'block';
        });
        
        amenityIndex++;
    }

    // ==================== TOWER MANAGEMENT ====================
    let towerIndex = 0;
    document.getElementById('btnAddTower')?.addEventListener('click', () => addTowerRow());
    
    function addTowerRow() {
        const list = document.getElementById('towersList');
        const empty = document.getElementById('emptyTowersMessage');
        const row = document.createElement('div');
        row.className = 'tower-row card mb-3 p-3';
        row.innerHTML = `<div class="row g-2">
            <div class="col-md-3"><label class="form-label small">Tower/Block Name <span class="text-danger">*</span></label><input type="text" class="form-control form-control-sm" name="towers[${towerIndex}][name]" placeholder="e.g. Tower A" required></div>
            <div class="col-md-2"><label class="form-label small">Total Floors <span class="text-danger">*</span></label><input type="number" class="form-control form-control-sm" name="towers[${towerIndex}][total_floors]" min="1" required></div>
            <div class="col-md-2"><label class="form-label small">Units/Floor</label><input type="number" class="form-control form-control-sm" name="towers[${towerIndex}][units_per_floor]" min="1"></div>
            <div class="col-md-2"><label class="form-label small">Basement Levels</label><input type="number" class="form-control form-control-sm" name="towers[${towerIndex}][basement_levels]" value="0" min="0"></div>
            <div class="col-md-3"><label class="form-label small">Status <span class="text-danger">*</span></label><select class="form-select form-select-sm" name="towers[${towerIndex}][status]" required><option value="upcoming">Upcoming</option><option value="construction" selected>Under Construction</option><option value="completed">Completed</option></select></div>
            <div class="col-md-3"><label class="form-label small">Completion Date</label><input type="date" class="form-control form-control-sm" name="towers[${towerIndex}][completion_date]"></div>
            <div class="col-md-1"><label class="form-label small">Sort</label><input type="number" class="form-control form-control-sm" name="towers[${towerIndex}][sort_order]" value="0"></div>
            <div class="col-md-2 text-end"><label class="form-label small d-block">&nbsp;</label><div class="form-check mb-1"><input class="form-check-input" type="checkbox" name="towers[${towerIndex}][has_terrace]" value="1"><label class="form-check-label small">Has Terrace</label></div><button type="button" class="btn btn-sm btn-danger btn-remove-tower w-100"><i class="bi bi-trash"></i> Remove</button></div>
        </div>`;
        
        list.appendChild(row);
        if (empty) empty.style.display = 'none';
        
        row.querySelector('.btn-remove-tower').addEventListener('click', () => {
            row.remove();
            if (!document.querySelectorAll('.tower-row').length && empty) empty.style.display = 'block';
        });
        
        towerIndex++;
    }

    // ==================== UNIT MANAGEMENT ====================
    let unitIndex = 0;
    document.getElementById('btnAddUnit')?.addEventListener('click', () => addUnitRow());
    
    function addUnitRow() {
        const list = document.getElementById('unitsList');
        const empty = document.getElementById('emptyUnitsMessage');
        const row = document.createElement('div');
        row.className = 'unit-row card mb-3 p-3';
        row.innerHTML = `<div class="row g-2">
            <div class="col-12"><h6 class="border-bottom pb-2">Unit Configuration</h6></div>
            <div class="col-md-3"><label class="form-label small">Unit Name <span class="text-danger">*</span></label><input type="text" class="form-control form-control-sm" name="units[${unitIndex}][name]" placeholder="e.g. Type A" required></div>
            <div class="col-md-3"><label class="form-label small">Unit Type <span class="text-danger">*</span></label><select class="form-select form-select-sm" name="units[${unitIndex}][type]" required><option value="1bhk">1 BHK</option><option value="2bhk">2 BHK</option><option value="3bhk">3 BHK</option><option value="4bhk">4 BHK</option><option value="5bhk">5 BHK</option><option value="studio">Studio</option><option value="penthouse">Penthouse</option><option value="duplex">Duplex</option><option value="shop">Shop</option><option value="office">Office</option><option value="showroom">Showroom</option><option value="plot">Plot</option><option value="villa">Villa</option></select></div>
            <div class="col-md-2"><label class="form-label small">Carpet (sqft)</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][carpet_area_sqft]" step="0.01" min="0"></div>
            <div class="col-md-2"><label class="form-label small">Built-up (sqft)</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][built_up_area_sqft]" step="0.01" min="0"></div>
            <div class="col-md-2"><label class="form-label small">Super Built-up (sqft)</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][super_built_up_sqft]" step="0.01" min="0"></div>
            
            <div class="col-md-2"><label class="form-label small">Bedrooms</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][bedrooms]" min="0"></div>
            <div class="col-md-2"><label class="form-label small">Bathrooms</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][bathrooms]" min="0"></div>
            <div class="col-md-2"><label class="form-label small">Balconies</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][balconies]" min="0"></div>
            <div class="col-md-3"><label class="form-label small">Facing</label><select class="form-select form-select-sm" name="units[${unitIndex}][facing]"><option value="">Select...</option><option value="north">North</option><option value="south">South</option><option value="east">East</option><option value="west">West</option><option value="north-east">North-East</option><option value="north-west">North-West</option><option value="south-east">South-East</option><option value="south-west">South-West</option></select></div>
            <div class="col-md-3"><label class="form-label small">Base Price (₹)</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][base_price]" step="1000" min="0"></div>
            
            <div class="col-md-3"><label class="form-label small">Total Units</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][total_units]" min="1" value="1"></div>
            <div class="col-md-3"><label class="form-label small">Available Units</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][available_units]" min="0" value="1"></div>
            <div class="col-md-2"><label class="form-label small">Sort Order</label><input type="number" class="form-control form-control-sm" name="units[${unitIndex}][sort_order]" value="0"></div>
            <div class="col-md-1"><label class="form-label small d-block">&nbsp;</label><div class="form-check"><input class="form-check-input" type="checkbox" name="units[${unitIndex}][is_active]" value="1" checked><label class="form-check-label small">Active</label></div></div>
            
            <div class="col-12"><label class="form-label small">Floor Plan Image</label><input type="file" class="form-control form-control-sm unit-floor-plan" name="units[${unitIndex}][floor_plan_image]" accept="image/*"><div class="unit-floor-plan-preview mt-2" style="display:none;"><img src="" class="img-thumbnail" style="max-height:120px;"></div></div>
            <div class="col-12 text-end"><button type="button" class="btn btn-sm btn-danger btn-remove-unit"><i class="bi bi-trash"></i> Remove Unit</button></div>
        </div>`;
        
        list.appendChild(row);
        if (empty) empty.style.display = 'none';
        
        // Floor plan image preview with file size validation
        row.querySelector('.unit-floor-plan').addEventListener('change', function(e) {
            if (e.target.files?.[0]) {
                const file = e.target.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert(`Floor plan "${file.name}" is too large!\n\nFile size: ${(file.size / 1024 / 1024).toFixed(2)}MB\nMaximum: 10MB\n\nPlease compress or choose a smaller file.`);
                    e.target.value = '';
                    return;
                }
                const reader = new FileReader();
                const preview = row.querySelector('.unit-floor-plan-preview');
                reader.onload = ev => { preview.querySelector('img').src = ev.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
        
        row.querySelector('.btn-remove-unit').addEventListener('click', () => {
            row.remove();
            if (!document.querySelectorAll('.unit-row').length && empty) empty.style.display = 'block';
        });
        
        unitIndex++;
    }

    // ==================== SPECIFICATION FORM SWITCHING ====================
    function updateSpecificationForm() {
        const projectType = document.querySelector('select[name="type"]')?.value;
        document.querySelectorAll('.spec-form').forEach(form => form.style.display = 'none');
        
        const specMapping = {
            'residential': 'residentialSpec',
            'commercial': 'commercialSpec',
            'villa': 'villaSpec',
            'open_plot': 'openPlotSpec'
        };
        
        if (specMapping[projectType]) {
            const targetForm = document.getElementById(specMapping[projectType]);
            if (targetForm) targetForm.style.display = 'block';
        }
    }
    
    // Listen to project type changes
    document.querySelector('select[name="type"]')?.addEventListener('change', updateSpecificationForm);
    
    // Initial call to set correct spec form on page load
    updateSpecificationForm();


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
            formData.delete('highlights'); // Remove if exists
            // Append as array items: highlights[0], highlights[1], etc.
            highlightsArray.forEach((highlight, index) => {
                formData.append(`highlights[${index}]`, highlight);
            });
        }

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

        try {
            const response = await fetch('{{ route("projects.store") }}', {
                method: 'POST',
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
                    throw new Error(data.message || 'Failed to create project');
                }
            } else {
                // Success - redirect to project show page
                showToast('Project created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = `/projects/${data.data.id}`;
                }, 1000);
            }
        } catch (error) {
            console.error('Error creating project:', error);
            alert('Failed to create project. Please try again.');
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