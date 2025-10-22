<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__.'/../config.php';

// ✅ Bảo vệ bằng password tạm (đặt thủ công)
$admin_password = "admin123"; // đổi thành mật khẩu của bạn

if (!isset($_GET['pw']) || $_GET['pw'] !== $admin_password) {
    http_response_code(403);
    echo "Access denied";
    exit;
}

$name = $_GET['name'] ?? 'esp32_device';
$key = bin2hex(random_bytes(16));

$pdo = getPDO();
$stmt = $pdo->prepare("INSERT INTO devices (device_name, api_key) VALUES (:n, :k)");
$stmt->execute([':n'=>$name, ':k'=>$key]);
$id = $pdo->lastInsertId();

echo "<h3>✅ Device created successfully!</h3>";
echo "<b>ID:</b> {$id}<br>";
echo "<b>Name:</b> {$name}<br>";
echo "<b>API KEY:</b> <code>{$key}</code><br>";
