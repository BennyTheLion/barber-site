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
            <h1>שעות פעילות</h1>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">ימי פעילות</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>יום</th>
                                <th>שעת פתיחה</th>
                                <th>שעת סגירה</th>
                                <th>פעיל</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $days = ['', 'ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'];
                            foreach($hours as $hour): 
                            ?>
                            <tr>
                                <td><?= $days[$hour['day_of_week']] ?></td>
                                <td>
                                    <input type="time" name="hours[<?= $hour['day_of_week'] ?>][start]" value="<?= substr($hour['start_time'], 0, 5) ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="time" name="hours[<?= $hour['day_of_week'] ?>][end]" value="<?= substr($hour['end_time'], 0, 5) ?>" class="form-control">
                                </td>
                                <td>
                                    <input type="checkbox" name="hours[<?= $hour['day_of_week'] ?>][active]" value="1" <?= $hour['is_active'] ? 'checked' : '' ?>>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary">שמור שינויים</button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">ימים חסומים (חופשות/חגים)</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo SITE_URL; ?>/index.php?url=admin/addBlockedDate" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="reason" class="form-control" placeholder="סיבה (אופציונלי)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger">הוסף יום חסום</button>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>תאריך</th>
                            <th>סיבה</th>
                            <th>פעולות</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($blockedDates as $blocked): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($blocked['blocked_date'])) ?></td>
                            <td><?= htmlspecialchars($blocked['reason'] ?? '-') ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/index.php?url=admin/deleteBlockedDate&id=<?= $blocked['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('האם אתה בטוח?')">
                                    <i class="fas fa-trash"></i> הסר
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