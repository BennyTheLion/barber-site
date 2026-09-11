<?php
require __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $isAdmin = !empty($_SESSION['admin_id']);
    $sql = 'SELECT * FROM services' . ($isAdmin ? '' : ' WHERE active = 1') . ' ORDER BY sort_order ASC, id ASC';
    json_out($pdo->query($sql)->fetchAll());
}

if ($method === 'POST') {
    require_admin();
    $input = body_json();
    $name = trim($input['name'] ?? '');
    $duration = (int) ($input['duration_minutes'] ?? 30);
    $price = trim($input['price'] ?? '');
    $sort = (int) ($input['sort_order'] ?? 0);
    $active = !empty($input['active']) ? 1 : 0;
    if (!$name || $duration < 5) json_out(['error' => 'שם השירות ומשך תקין נדרשים'], 400);

    if (!empty($input['id'])) {
        $stmt = $pdo->prepare('UPDATE services SET name=?, duration_minutes=?, price=?, sort_order=?, active=? WHERE id=?');
        $stmt->execute([$name, $duration, $price, $sort, $active, (int) $input['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO services (name, duration_minutes, price, sort_order, active) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $duration, $price, $sort, $active]);
    }
    json_out(['ok' => true]);
}

if ($method === 'DELETE') {
    require_admin();
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) json_out(['error' => 'missing id'], 400);
    $pdo->prepare('DELETE FROM services WHERE id=?')->execute([$id]);
    json_out(['ok' => true]);
}

json_out(['error' => 'method not allowed'], 405);
