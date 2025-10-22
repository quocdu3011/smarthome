<?php
// insert_sensor.php
require_once __DIR__.'/../config.php';
$device = require_api_key();
$pdo = getPDO();

// Accept GET or POST
$temp = isset($_REQUEST['temp']) ? floatval($_REQUEST['temp']) : null;
$hum  = isset($_REQUEST['hum']) ? floatval($_REQUEST['hum']) : null;
$gas  = isset($_REQUEST['gas']) ? floatval($_REQUEST['gas']) : null;
$ldr  = isset($_REQUEST['ldr']) ? intval($_REQUEST['ldr']) : null;
$rain = isset($_REQUEST['rain']) ? floatval($_REQUEST['rain']) : null;
$extra = isset($_REQUEST['extra']) ? $_REQUEST['extra'] : null;

$stmt = $pdo->prepare("INSERT INTO sensor_data (device_id, temp, hum, gas, ldr, rain, extra) VALUES (:did,:t,:h,:g,:l,:r,:e)");
$stmt->execute([
    ':did'=>$device['id'],
    ':t'=>$temp,
    ':h'=>$hum,
    ':g'=>$gas,
    ':l'=>$ldr,
    ':r'=>$rain,
    ':e'=>$extra ? json_encode($extra) : null
]);

json_response(['ok'=>1,'insert_id'=>$pdo->lastInsertId()]);
