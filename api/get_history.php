<?php
require_once __DIR__.'/../config.php';
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
$device = require_api_key();
$pdo = getPDO();

$from = $_GET['from'] ?? null; // '2025-10-01 00:00:00'
$to   = $_GET['to'] ?? null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$sql = "SELECT * FROM sensor_data WHERE device_id = :did";
$params = [':did'=>$device['id']];

if ($from) { $sql .= " AND recorded_at >= :from"; $params[':from']=$from; }
if ($to)   { $sql .= " AND recorded_at <= :to";     $params[':to']=$to; }

$sql .= " ORDER BY recorded_at DESC LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':did', $device['id'], PDO::PARAM_INT);
if ($from) $stmt->bindValue(':from', $params[':from']);
if ($to)   $stmt->bindValue(':to', $params[':to']);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();
json_response(['ok'=>1,'rows'=>$rows]);
