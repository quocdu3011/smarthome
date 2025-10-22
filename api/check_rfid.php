<?php
require_once __DIR__.'/../config.php';
$device = require_api_key();
$pdo = getPDO();

$uid = $_REQUEST['uid'] ?? null;
$uid = str_replace(' ', '', $uid); // Loại bỏ khoảng trắng trong UID
$action = $_REQUEST['action'] ?? 'entry'; // default
$note = $_REQUEST['note'] ?? null;
if (!$uid) json_response(['error'=>'uid required']);

// find user
$stmt = $pdo->prepare("SELECT id, name FROM rfid_users WHERE uid = :uid LIMIT 1");
$stmt->execute([':uid'=>$uid]);
$user = $stmt->fetch();

$user_id = $user ? $user['id'] : null;
$name = $user ? $user['name'] : null;

// insert log
$ins = $pdo->prepare("INSERT INTO rfid_logs (uid, user_id, action, note) VALUES (:uid,:uidid,:action,:note)");
$ins->execute([':uid'=>$uid, ':uidid'=>$user_id, ':action'=>$action, ':note'=>$note]);

$response = ['ok'=>1, 'uid'=>$uid, 'action'=>$action, 'log_id'=>$pdo->lastInsertId()];
if ($user) $response['user'] = $user;
else $response['user'] = null;

json_response($response);
