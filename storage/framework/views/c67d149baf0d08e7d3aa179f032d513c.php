<?php $__env->startSection('title', 'Edit Project'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
<li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('projects.index')); ?>">Projects</a></li>
<li class="breadcrumb-item"><a href="<?php echo e(route('projects.show', $project)); ?>"><?php echo e($project->name); ?></a></li>
<li class="breadcrumb-item active">Edit</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
        <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn btn-outline-secondary">
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
    </ul>

    <form id="projectForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="project_id" value="<?php echo e($project->id); ?>">

        <div class="tab-content">
            <!-- Tab 1: Basic Info -->
            <div class="tab-pane fade show active" id="basicInfo">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            <?php $__currentLoopData = \App\Models\Company::where('is_active', true)->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->id); ?>" <?php echo e($project->company_id == $company->id ? 'selected' : ''); ?>>
                                <?php echo e($company->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?php echo e($project->name); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="residential" <?php echo e($project->type == 'residential' ? 'selected' : ''); ?>>Residential</option>
                            <option value="commercial" <?php echo e($project->type == 'commercial' ? 'selected' : ''); ?>>Commercial</option>
                            <option value="villa" <?php echo e($project->type == 'villa' ? 'selected' : ''); ?>>Villa</option>
                            <option value="open_plots" <?php echo e($project->type == 'open_plots' ? 'selected' : ''); ?>>Open Plots</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="upcoming" <?php echo e($project->status == 'upcoming' ? 'selected' : ''); ?>>Upcoming</option>
                            <option value="ongoing" <?php echo e($project->status == 'ongoing' ? 'selected' : ''); ?>>Ongoing</option>
                            <option value="completed" <?php echo e($project->status == 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="sold_out" <?php echo e($project->status == 'sold_out' ? 'selected' : ''); ?>>Sold Out</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4"><?php echo e($project->description); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Highlights (One per line)</label>
                        <textarea class="form-control" id="highlightsInput" rows="4"><?php echo e(is_array($project->highlights) ? implode("\n", $project->highlights) : ''); ?></textarea>
                        <small class="text-muted">Each line will be converted to a highlight point</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Project Logo</label>
                        <?php if($project->logo): ?>
                        <div class="mb-2">
                            <img src="<?php echo e($project->logo); ?>" alt="Current Logo" class="img-thumbnail" style="max-width: 200px;">
                            <p class="text-muted small mt-1">Current logo - upload new to replace</p>
                        </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="logo" accept="image/*" id="logoInput">
                        <small class="text-muted">Recommended: 400x400px, PNG or JPG (Max 2MB)</small>
                        <div id="logoPreview" class="mt-2" style="display: none;">
                            <img src="" alt="New Logo Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" value="1" <?php echo e($project->is_featured ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="isFeatured">
                                <i class="bi bi-star-fill text-warning"></i> Featured Project
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?php echo e($project->is_active ? 'checked' : ''); ?>>
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
                        <input type="text" class="form-control" name="rera_number" value="<?php echo e($project->rera_number); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RERA Valid Until</label>
                        <input type="date" class="form-control" name="rera_valid_until" value="<?php echo e($project->rera_valid_until?->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="address_line1" value="<?php echo e($project->address_line1); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" name="address_line2" value="<?php echo e($project->address_line2); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Landmark</label>
                        <input type="text" class="form-control" name="landmark" value="<?php echo e($project->landmark); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="city" value="<?php echo e($project->city); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="state" value="<?php echo e($project->state); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PIN Code</label>
                        <input type="text" class="form-control" name="pincode" value="<?php echo e($project->pincode); ?>" maxlength="6" pattern="[0-9]{6}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="text" class="form-control" name="latitude" value="<?php echo e($project->latitude); ?>" placeholder="e.g., 17.385044">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="text" class="form-control" name="longitude" value="<?php echo e($project->longitude); ?>" placeholder="e.g., 78.486671">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Google Maps URL</label>
                        <input type="url" class="form-control" name="google_maps_url" value="<?php echo e($project->google_maps_url); ?>" placeholder="https://goo.gl/maps/...">
                    </div>
                </div>
            </div>

            <!-- Tab 3: Land & Timeline -->
            <div class="tab-pane fade" id="landTimeline">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Total Land Area</label>
                        <input type="number" step="0.01" class="form-control" name="total_land_area" value="<?php echo e($project->total_land_area); ?>" placeholder="0.00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Land Area Unit</label>
                        <select class="form-select" name="land_area_unit">
                            <option value="sqft" <?php echo e($project->land_area_unit == 'sqft' ? 'selected' : ''); ?>>Square Feet (sqft)</option>
                            <option value="sqm" <?php echo e($project->land_area_unit == 'sqm' ? 'selected' : ''); ?>>Square Meters (sqm)</option>
                            <option value="acres" <?php echo e($project->land_area_unit == 'acres' ? 'selected' : ''); ?>>Acres</option>
                            <option value="sqyds" <?php echo e($project->land_area_unit == 'sqyds' ? 'selected' : ''); ?>>Square Yards (sqyds)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Launch Date</label>
                        <input type="date" class="form-control" name="launch_date" value="<?php echo e($project->launch_date?->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Possession Date</label>
                        <input type="date" class="form-control" name="possession_date" value="<?php echo e($project->possession_date?->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Completion Percentage: <span id="completionValue"><?php echo e($project->completion_percentage ?? 0); ?></span>%</label>
                        <input type="range" class="form-range" name="completion_percentage" min="0" max="100" value="<?php echo e($project->completion_percentage ?? 0); ?>" id="completionRange">
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
                        <?php if($project->images->count() > 0): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Images</h6>
                            <div id="existingImages" class="row g-3">
                                <?php $__currentLoopData = $project->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4" data-image-id="<?php echo e($image->id); ?>">
                                    <div class="card">
                                        <img src="<?php echo e(asset($image->image_path)); ?>" class="card-img-top" alt="<?php echo e($image->alt_text); ?>" style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <small class="d-block text-truncate"><strong><?php echo e($image->title ?: 'No title'); ?></strong></small>
                                                    <small class="text-muted"><?php echo e(ucfirst(str_replace('_', ' ', $image->type))); ?></small>
                                                    <?php if($image->is_primary): ?>
                                                        <span class="badge bg-warning text-dark ms-1">Primary</span>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete-image" data-image-id="<?php echo e($image->id); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_images[]" class="delete-image-input" value="" disabled>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
                        <?php if($project->amenities->count() > 0): ?>
                        <div class="mb-4">
                            <h6 class="text-muted">Existing Amenities</h6>
                            <div id="existingAmenities" class="row g-2">
                                <?php $__currentLoopData = $project->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6" data-amenity-id="<?php echo e($amenity->id); ?>">
                                    <div class="card p-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if($amenity->icon): ?>
                                                        <i class="bi <?php echo e($amenity->icon); ?>"></i>
                                                    <?php endif; ?>
                                                    <strong><?php echo e($amenity->name); ?></strong>
                                                    <?php if($amenity->is_highlighted): ?>
                                                        <span class="badge bg-warning text-dark">★ Highlighted</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?php echo e($amenity->category_label); ?></small>
                                                <?php if($amenity->description): ?>
                                                    <p class="small mb-0 mt-1"><?php echo e($amenity->description); ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-amenity" data-amenity-id="<?php echo e($amenity->id); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="delete_amenities[]" class="delete-amenity-input" value="" disabled>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
        </div>

        <!-- Form Actions -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-maroon">
                <i class="bi bi-check-circle me-2"></i>Update Project
            </button>
            <a href="<?php echo e(route('projects.show', $project)); ?>" class="btn btn-secondary">
                <i class="bi bi-x-circle me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
            const response = await fetch('<?php echo e(route("projects.update", $project)); ?>', {
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/projects/edit.blade.php ENDPATH**/ ?>