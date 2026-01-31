// ==================== IMAGE MANAGEMENT ====================
let imageIndex = 0;

// Add new image row
document.getElementById('btnAddImage')?.addEventListener('click', function() {
    addImageRow();
});

function addImageRow() {
    const imagesList = document.getElementById('imagesList');
    const emptyMessage = document.getElementById('emptyImagesMessage');
    
    const imageRow = document.createElement('div');
    imageRow.className = 'image-row card mb-3 p-3';
    imageRow.dataset.imageIndex = imageIndex;
    
    imageRow.innerHTML = `
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small">Image File <span class="text-danger">*</span></label>
                <input type="file" class="form-control form-control-sm image-file-input" 
                       name="images[${imageIndex}][file]" accept="image/*" required>
                <div class="image-preview mt-2" style="display: none;">
                    <img src="" alt="Preview" class="img-thumbnail" style="max-width: 100%; max-height: 120px;">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Title</label>
                <input type="text" class="form-control form-control-sm" 
                       name="images[${imageIndex}][title]" placeholder="Optional title">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Type <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" name="images[${imageIndex}][type]" required>
                    <option value="gallery">Gallery</option>
                    <option value="floor_plan">Floor Plan</option>
                    <option value="master_plan">Master Plan</option>
                    <option value="brochure">Brochure</option>
                    <option value="elevation">Elevation</option>
                    <option value="amenity">Amenity</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Alt Text</label>
                <input type="text" class=" form-control form-control-sm" 
                       name="images[${imageIndex}][alt_text]" placeholder="For SEO">
            </div>
            <div class="col-md-1">
                <label class="form-label small">Sort</label>
                <input type="number" class="form-control form-control-sm text-center" 
                       name="images[${imageIndex}][sort_order]" value="0" min="0">
            </div>
            <div class="col-md-2 text-end">
                <label class="form-label small d-block">&nbsp;</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input image-primary-checkbox" type="checkbox" 
                           name="images[${imageIndex}][is_primary]" value="1">
                    <label class="form-check-label small">Primary</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger btn-remove-image ms-2">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    imagesList.appendChild(imageRow);
    emptyMessage.style.display = 'none';
    
    // Event listeners
    const fileInput = imageRow.querySelector('.image-file-input');
    const preview = imageRow.querySelector('.image-preview');
    const previewImg = preview.querySelector('img');
    const removeBtn = imageRow.querySelector('.btn-remove-image');
    const primaryCheckbox = imageRow.querySelector('.image-primary-checkbox');
    
    // File preview
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    
    // Remove row
    removeBtn.addEventListener('click', function() {
        imageRow.remove();
        const remaining = document.querySelectorAll('.image-row').length;
        if (remaining === 0) {
            emptyMessage.style.display = 'block';
        }
    });
    
    // Primary image toggle (only one can be primary)
    primaryCheckbox.addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.image-primary-checkbox').forEach(cb => {
                if (cb !== this) cb.checked = false;
            });
        }
    });
    
    imageIndex++;
}
