<?php
require __DIR__ . '/config.php';

// Only allow this page to run while there is no admin yet.
$count = $pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
$done = false;
$error = '';

if ($count == 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (strlen($username) < 3 || strlen($password) < 6) {
        $error = 'שם משתמש (3+ תווים) וסיסמה (6+ תווים) נדרשים.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, $hash]);
        $done = true;
    }
}
?>
<!doctype html>
<html lang="he" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>הגדרה ראשונית — יצירת מנהל</title>
<style>
  body { font-family: system-ui, sans-serif; background:#16171c; color:#eee; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; padding:20px; }
  .box { max-width:380px; width:100%; background:#212228; border-radius:16px; padding:24px; }
  input { width:100%; padding:12px; margin-top:6px; margin-bottom:16px; border-radius:10px; border:1px solid #444; background:#16171c; color:#eee; font-size:16px; box-sizing:border-box; }
  button { width:100%; padding:14px; border-radius:10px; border:none; background:#7ed957; color:#111; font-weight:700; font-size:15px; cursor:pointer; }
  label { font-size:13px; color:#aaa; }
  .err { color:#ff6b6b; margin-bottom:12px; font-size:14px; }
  .ok { color:#7ed957; }
</style>
</head>
<body>
<div class="box">
<?php if ($count > 0 && !$done): ?>
  <p>כבר קיים חשבון מנהל במערכת. עבור ל-<a href="admin.php" style="color:#7ed957">פאנל הניהול</a>.</p>
  <p style="color:#f5a623;font-size:13px;">מומלץ למחוק את הקובץ setup.php מהשרת עכשיו מטעמי אבטחה.</p>
<?php elseif ($done): ?>
  <p class="ok">✔ חשבון המנהל נוצר בהצלחה.</p>
  <p><a href="admin.php" style="color:#7ed957">כניסה לפאנל הניהול</a></p>
  <p style="color:#f5a623;font-size:13px;">חשוב: מחקו את הקובץ setup.php מהשרת עכשיו כדי שאף אחד אחר לא יוכל ליצור חשבון מנהל נוסף.</p>
<?php else: ?>
  <h2>יצירת חשבון מנהל ראשון</h2>
  <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <label>שם משתמש</label>
    <input type="text" name="username" required>
    <label>סיסמה</label>
    <input type="password" name="password" required minlength="6">
    <button type="submit">צור חשבון</button>
  </form>
<?php endif; ?>
</div>
</body>
</html>
