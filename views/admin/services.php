<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
 
 if(!$isAjax) {
     // views/admin/appointments.php
    require_once __DIR__ . "/../layout/admin_header.php";
 }

?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1><i class="fas fa-cut text-danger"></i> ניהול שירותים</h1>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetServiceForm()">
                <i class="fas fa-plus"></i> שירות חדש
            </button>
            <button class="btn btn-success" onclick="window.loadPage('<?php echo SITE_URL; ?>/index.php?url=admin/services-content', 'services')">
                <i class="fas fa-sync"></i> רענן
            </button>
        </div>
    </div>
    
    <?php if(isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['admin_success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['admin_error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Services Table -->
    <div class="card">
        <div class="card-body">
            <?php if(isset($services) && count($services) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>תמונה</th>
                                <th>שם השירות</th>
                                <th>תיאור</th>
                                <th>מחיר</th>
                                <th>משך (דק')</th>
                                <th>סטטוס</th>
                                <th>פעולות</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($services as $service): ?>
                            <tr>
                                <td><?= $service['id'] ?></td>
                                <td>
                                    <?php if(!empty($service['image'])): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/services/<?= htmlspecialchars($service['image']) ?>" 
                                             alt="<?= htmlspecialchars($service['name']) ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-cut text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($service['name']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($service['description'] ?? '', 0, 50)) ?>...</td>
                                <td>₪<?= number_format($service['price'], 2) ?></td>
                                <td><?= $service['duration'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $service['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $service['is_active'] ? 'פעיל' : 'לא פעיל' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editService(<?= htmlspecialchars(json_encode($service)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo SITE_URL; ?>/index.php?url=admin/deleteService/<?= $service['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('האם אתה בטוח?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-cut fa-3x text-muted mb-3"></i>
                    <p class="text-muted">אין שירותים להצגה</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetServiceForm()">
                        <i class="fas fa-plus"></i> הוסף שירות ראשון
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cut text-danger"></i> <span id="modalTitle">שירות חדש</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo SITE_URL; ?>/index.php?url=admin/services-content" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="serviceId" value="0">
                    <input type="hidden" name="existing_image" id="existingImage" value="">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">שם השירות <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="serviceName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">תיאור</label>
                                <textarea name="description" id="serviceDescription" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">מחיר (₪) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="servicePrice" class="form-control" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">משך (דקות) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" id="serviceDuration" class="form-control" value="30" required>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="serviceActive" class="form-check-input" value="1" checked>
                                    <label class="form-check-label">שירות פעיל</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Image Upload -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <small class="text-muted">תמונת שירות</small>
                                </div>
                                <div class="card-body text-center">
                                    <div id="imagePreview" class="mb-3">
                                        <img id="currentImagePreview" src="" alt="תמונת שירות" 
                                             style="max-width: 100%; max-height: 150px; display: none; border-radius: 8px;">
                                        <div id="noImagePlaceholder" class="text-muted">
                                            <i class="fas fa-image fa-3x"></i>
                                            <p class="small">אין תמונה</p>
                                        </div>
                                    </div>
                                    <input type="file" name="image" id="serviceImage" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">JPEG, PNG, GIF, WEBP (מקסימום 2MB)</small>
                                    <div id="imageFileName" class="small text-muted mt-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ביטול</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> שמור שירות
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetServiceForm() {
    document.getElementById('modalTitle').textContent = 'שירות חדש';
    document.getElementById('serviceId').value = '0';
    document.getElementById('serviceName').value = '';
    document.getElementById('serviceDescription').value = '';
    document.getElementById('servicePrice').value = '';
    document.getElementById('serviceDuration').value = '30';
    document.getElementById('serviceActive').checked = true;
    document.getElementById('existingImage').value = '';
    document.getElementById('currentImagePreview').style.display = 'none';
    document.getElementById('noImagePlaceholder').style.display = 'block';
    document.getElementById('serviceImage').value = '';
    document.getElementById('imageFileName').textContent = '';
}

function editService(service) {
    document.getElementById('modalTitle').textContent = 'עריכת שירות';
    document.getElementById('serviceId').value = service.id;
    document.getElementById('serviceName').value = service.name;
    document.getElementById('serviceDescription').value = service.description || '';
    document.getElementById('servicePrice').value = service.price;
    document.getElementById('serviceDuration').value = service.duration;
    document.getElementById('serviceActive').checked = service.is_active == 1;
    document.getElementById('existingImage').value = service.image || '';
    
    // Show existing image - Updated path
    if(service.image) {
        document.getElementById('currentImagePreview').src = '<?php echo SITE_URL; ?>/uploads/services/' + service.image;
        document.getElementById('currentImagePreview').style.display = 'block';
        document.getElementById('noImagePlaceholder').style.display = 'none';
        document.getElementById('imageFileName').textContent = 'תמונה נוכחית: ' + service.image;
    } else {
        document.getElementById('currentImagePreview').style.display = 'none';
        document.getElementById('noImagePlaceholder').style.display = 'block';
        document.getElementById('imageFileName').textContent = '';
    }
    
    new bootstrap.Modal(document.getElementById('serviceModal')).show();
}

// Image preview on file select
document.getElementById('serviceImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('currentImagePreview').src = event.target.result;
            document.getElementById('currentImagePreview').style.display = 'block';
            document.getElementById('noImagePlaceholder').style.display = 'none';
            document.getElementById('imageFileName').textContent = 'קובץ נבחר: ' + file.name;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>