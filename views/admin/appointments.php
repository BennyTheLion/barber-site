<?php
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
 
 if(!$isAjax) {
     // views/admin/appointments.php
    require_once __DIR__ . "/../layout/admin_header.php";
 }

?>
<!-- ב-appointments.php - הוסף debug -->
<?php error_log("=== APPOINTMENTS LOADED ==="); ?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1><i class="fas fa-calendar-alt text-danger"></i> ניהול תורים</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments&filter=today" class="btn btn-primary btn-sm">היום</a>
            <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments&filter=upcoming" class="btn btn-success btn-sm">הבאים</a>
            <a href="<?php echo SITE_URL; ?>/index.php?url=admin/appointments&filter=all" class="btn btn-secondary btn-sm">הכל</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3" action="<?php echo SITE_URL; ?>/index.php?url=admin/appointments">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="חיפוש לפי שם או טלפון..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary">חפש</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>לקוח</th>
                            <th>טלפון</th>
                            <th>שירות</th>
                            <th>תאריך</th>
                            <th>שעה</th>
                            <th>סטטוס</th>
                            <th>פעולות</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($appointments) && count($appointments) > 0): ?>
                            <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td><?= $appointment['id'] ?></td>
                                <td><?= htmlspecialchars($appointment['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($appointment['phone'] ?? '') ?></td>
                                <td><?= htmlspecialchars($appointment['service_name'] ?? '') ?></td>
                                <td><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></td>
                                <td><?= substr($appointment['appointment_time'] ?? '', 0, 5) ?></td>
                                <td>
                                    <select class="form-select form-select-sm status-select" 
                                        data-id="<?= $appointment['id'] ?>" 
                                        style="width: 120px;"  
                                        onchange="updateAppointmentStatus(this.dataset.id, this.value)">
                                        <option value="pending" <?= $appointment['status'] == 'pending' ? 'selected' : '' ?>>⏳ ממתין</option>
                                        <option value="confirmed" <?= $appointment['status'] == 'confirmed' ? 'selected' : '' ?>>✅ מאושר</option>
                                        <option value="completed" <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>✔️ הושלם</option>
                                        <option value="cancelled" <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>❌ בוטל</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editAppointment(<?= htmlspecialchars(json_encode($appointment)) ?>)" title="ערוך תור">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteAppointment(<?= $appointment['id'] ?>)" title="מחק תור">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">אין תורים להצגה</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Load JavaScript -->
<script src="<?php echo SITE_URL; ?>/assets/js/admin-appointments.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('appointments.php loaded');
    // Re-initialize after content loads
    if (typeof initAppointments === 'function') {
        initAppointments();
    }
});
</script>
<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>