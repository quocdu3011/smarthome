<?php
require_once __DIR__.'/../config.php';
$device = require_api_key();
$pdo = getPDO();

$uid = $_POST['uid'] ?? null;
$uid = str_replace(' ', '', $uid); // Loại bỏ khoảng trắng trong UID
$name = $_POST['name'] ?? null;
if (!$uid || !$name) { http_response_code(400); json_response(['error'=>'uid and name required']); }

$stmt = $pdo->prepare("INSERT INTO rfid_users (uid, name) VALUES (:uid, :name) ON DUPLICATE KEY UPDATE name = :name2");
$stmt->execute([':uid'=>$uid, ':name'=>$name, ':name2'=>$name]);
json_response(['ok'=>1]);
