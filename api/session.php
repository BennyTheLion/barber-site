<?php
require __DIR__ . '/../config.php';
json_out([
    'loggedIn' => !empty($_SESSION['admin_id']),
    'username' => $_SESSION['admin_username'] ?? null,
]);
