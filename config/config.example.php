<?php
// Site Configuration
define("SITE_NAME", "ספר בראש צעיר");

// ✅ Auto-detect base path so the exact same code works both on Hostinger
// (project sits at the domain root) and on localhost/XAMPP (project sits
// under a subfolder, e.g. http://localhost/barber-site). We compute the
// subfolder from the project's location on disk relative to the web
// server's document root, so it works no matter which file was hit
// directly (index.php, or a view file linked to directly).
$__projectRoot = str_replace('\\', '/', rtrim(dirname(__DIR__), '/'));
$__documentRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
$__basePath = '';
if ($__documentRoot !== '' && strpos($__projectRoot, $__documentRoot) === 0) {
    $__basePath = substr($__projectRoot, strlen($__documentRoot));
}
$__basePath = rtrim($__basePath, '/');

$__protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$__host = $_SERVER['HTTP_HOST'] ?? 'localhost';

define("BASE_PATH", $__basePath); // '' on Hostinger, '/barber-site' on localhost
define("SITE_URL", $__protocol . '://' . $__host . $__basePath);

unset($__projectRoot, $__documentRoot, $__basePath, $__protocol, $__host);
define("SITE_EMAIL", "admin@example.com");
define('CONTACT_PHONE', '052-1234567');  // ← Your actual phone number
define('CONTACT_EMAIL', 'mybarber@example.com');
define('CONTACT_ADDRESS', 'רחוב הרצל 10, נתניה');

// Appointment Settings
define("MIN_CANCELLATION_HOURS", 12);
define("APPOINTMENT_DURATION", 30); // minutes
define("MAX_ADVANCE_DAYS", 30);

// Timezone
date_default_timezone_set("Asia/Jerusalem");

// Upload Settings
define("MAX_FILE_SIZE", 5242880); // 5MB
define("ALLOWED_EXTENSIONS", ["jpg", "jpeg", "png", "gif"]);

// Rate Limiting
define("MAX_LOGIN_ATTEMPTS", 5);
define("RATE_LIMIT_WINDOW", 900); // 15 minutes

// ✅ Email Notifications (appointment created/updated/cancelled)
// Sent via Gmail SMTP using PHPMailer (see classes/Mailer.php).
// Fill in a Gmail address + App Password: Google Account -> Security ->
// 2-Step Verification -> App Passwords (requires 2-Step Verification on).
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' (port 587) or 'ssl' (port 465)
define('SMTP_USERNAME', ''); // ← Your Gmail address, e.g. yourname@gmail.com
define('SMTP_PASSWORD', ''); // ← Your 16-character Gmail App Password
define('SMTP_FROM_EMAIL', SMTP_USERNAME);
define('SMTP_FROM_NAME', SITE_NAME);

// Admin address that receives a copy of every appointment notification
define('ADMIN_NOTIFICATION_EMAIL', 'admin@example.com');

// Error Reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/../logs/error.log");


