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
            <h1>פרטי לקוח</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo SITE_URL; ?>/index.php?url=admin/customers" class="btn btn-secondary">חזרה לרשימה</a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">פרטים אישיים</h5>
                </div>
                <div class="card-body">
                    <p><strong>שם:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                    <p><strong>טלפון:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                    <p><strong>אימייל:</strong> <?= htmlspecialchars($customer['email'] ?? '-') ?></p>
                    <p><strong>הצטרף:</strong> <?= date('d/m/Y', strtotime($customer['created_at'])) ?></p>
                    <p><strong>סה"כ תורים:</strong> <?= count($appointments) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">היסטוריית תורים</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>שירות</th>
                                    <th>תאריך</th>
                                    <th>שעה</th>
                                    <th>סטטוס</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($appointments as $appointment): ?>
                                <tr>
                                    <td><?= $appointment['id'] ?></td>
                                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></td>
                                    <td><?= substr($appointment['appointment_time'], 0, 5) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $appointment['status'] == 'completed' ? 'success' : ($appointment['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                            <?= $appointment['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>