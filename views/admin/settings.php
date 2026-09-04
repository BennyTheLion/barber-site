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
            <h1>הגדרות מערכת</h1>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">שם העסק</label>
                            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'ספר בראש צעיר') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">אימייל</label>
                            <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">טלפון</label>
                            <input type="tel" name="site_phone" class="form-control" value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="tel" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">כתובת</label>
                    <input type="text" name="site_address" class="form-control" value="<?= htmlspecialchars($settings['site_address'] ?? '') ?>">
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="url" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">TikTok</label>
                            <input type="url" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($settings['tiktok_url'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">אודות (תוכן)</label>
                    <textarea name="about_content" class="form-control" rows="10"><?= htmlspecialchars($settings['about_content'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">שמור הגדרות</button>
            </form>
        </div>
    </div>
</div>

<?php
if(!$isAjax) {
    require_once __DIR__ . "/../layout/admin_footer.php";
}
?>