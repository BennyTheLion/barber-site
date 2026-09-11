<?php
require __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'method not allowed'], 405);

$input = body_json();
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if (!$username || !$password) json_out(['error' => 'נא למלא שם משתמש וסיסמה'], 400);

$stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ?');
$stmt->execute([$username]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    json_out(['error' => 'שם משתמש או סיסמה שגויים'], 401);
}

session_regenerate_id(true);
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_username'] = $admin['username'];

json_out(['ok' => true, 'username' => $admin['username']]);
