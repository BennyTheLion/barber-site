<?php
require __DIR__ . '/config.php';
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key='legal_privacy_text'");
$stmt->execute();
$text = $stmt->fetchColumn() ?: '';
?>
<!doctype html>
<html lang="he" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>מדיניות פרטיות — LINE.</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="assets/style.css?v=<?= @filemtime(__DIR__ . '/assets/style.css') ?>">
</head>
<body>
<div class="site-header"><div class="brand">מדיניות פרטיות</div><a href="index.php" class="icon-btn">✕</a></div>
<div class="wrap" style="padding:24px 20px; white-space:pre-wrap; line-height:1.8; font-size:15px;">
<?= htmlspecialchars($text) ?>
</div>
</body>
</html>
