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
            <h1>ניהול לקוחות</h1>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="חיפוש לפי שם או טלפון..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit" class="btn btn-primary">חפש</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>שם</th>
                            <th>טלפון</th>
                            <th>אימייל</th>
                            <th>מספר תורים</th>
                            <th>תאריך אחרון</th>
                            <th>פעולות</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customers as $customer): ?>
                        <tr>
                            <td><?= $customer['id'] ?></td>
                            <td><?= htmlspecialchars($customer['name']) ?></td>
                            <td><?= htmlspecialchars($customer['phone']) ?></td>
                            <td><?= htmlspecialchars($customer['email'] ?? '-') ?></td>
                            <td><?= $customer['total_visits'] ?? 0 ?></td>
                            <td><?= $customer['last_visit'] ? date('d/m/Y', strtotime($customer['last_visit'])) : '-' ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/index.php?url=admin/customerDetails&id=<?= $customer['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>