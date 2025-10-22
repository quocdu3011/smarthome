<?php
require_once __DIR__.'/../config.php';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$device = require_api_key();
$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM sensor_data WHERE device_id = :did ORDER BY recorded_at DESC LIMIT 1");
$stmt->execute([':did'=>$device['id']]);
$row = $stmt->fetch();
json_response(['ok'=>1,'data'=>$row]);
