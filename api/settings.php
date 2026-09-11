<?php
require __DIR__ . '/../config.php';

$ALLOWED_KEYS = [
    'owner_name', 'tagline', 'phone', 'whatsapp_phone', 'email', 'address',
    'instagram_url', 'facebook_url', 'tiktok_url', 'slot_interval_minutes',
    'working_hours', 'legal_privacy_text', 'legal_terms_text',
    'admin_notification_email', 'mail_enabled', 'smtp_host', 'smtp_port',
    'smtp_username', 'smtp_password', 'smtp_secure', 'smtp_from_email', 'smtp_from_name',
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $isAdmin = !empty($_SESSION['admin_id']);
    // Mail/SMTP credentials are never returned to public (unauthenticated) requests.
    $privateKeys = ['admin_notification_email', 'mail_enabled', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_secure', 'smtp_from_email', 'smtp_from_name'];

    $rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
    $out = [];
    foreach ($rows as $r) {
        if (!$isAdmin && in_array($r['setting_key'], $privateKeys, true)) continue;
        $out[$r['setting_key']] = $r['setting_value'];
    }
    if (isset($out['working_hours'])) {
        $decoded = json_decode($out['working_hours'], true);
        $out['working_hours'] = $decoded ?: new stdClass();
    }
    json_out($out);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin();
    $input = body_json();
    $stmt = $pdo->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    foreach ($ALLOWED_KEYS as $key) {
        if (!array_key_exists($key, $input)) continue;
        $value = $input[$key];
        if ($key === 'working_hours' && is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        $stmt->execute([$key, $value]);
    }
    json_out(['ok' => true]);
}

json_out(['error' => 'method not allowed'], 405);
