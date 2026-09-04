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
            <h1>לוגים</h1>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-danger" onclick="clearLogs()">נקה לוגים</button>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>תאריך</th>
                            <th>רמה</th>
                            <th>הודעה</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <?php if(trim($log) != ''): ?>
                        <tr>
                            <td style="white-space: nowrap;"><?= substr($log, 1, 19) ?></td>
                            <td>
                                <?php 
                                $level = '';
                                if(strpos($log, 'ERROR') !== false) $level = 'danger';
                                elseif(strpos($log, 'SECURITY') !== false) $level = 'warning';
                                elseif(strpos($log, 'ADMIN') !== false) $level = 'info';
                                else $level = 'secondary';
                                ?>
                                <span class="badge bg-<?= $level ?>"><?= explode(']', explode('[', $log)[2] ?? '')[0] ?? 'INFO' ?></span>
                            </td>
                            <td><?= htmlspecialchars($log) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function clearLogs() {
    if(confirm('האם אתה בטוח שברצונך לנקות את כל הלוגים?')) {
        window.location.href = '<?php echo SITE_URL; ?>/index.php?url=admin/clearLogs';
    }
}
</script>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>