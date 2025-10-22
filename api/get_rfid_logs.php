<?php
require_once __DIR__.'/../config.php';
$device = require_api_key();
$pdo = getPDO();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$stmt = $pdo->prepare("SELECT l.*, u.name FROM rfid_logs l LEFT JOIN rfid_users u ON l.user_id = u.id ORDER BY l.logged_at DESC LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
json_response(['ok'=>1,'rows'=>$rows]);
