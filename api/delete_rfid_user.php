<?php
require_once __DIR__.'/../config.php';
$device = require_api_key();
$pdo = getPDO();
$uid = $_POST['uid'] ?? null;
if (!$uid) json_response(['error'=>'uid required']);
$stmt = $pdo->prepare("DELETE FROM rfid_users WHERE uid = :uid");
$stmt->execute([':uid'=>$uid]);
json_response(['ok'=>1, 'deleted'=>$stmt->rowCount()]);
