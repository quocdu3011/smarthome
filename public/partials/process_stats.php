<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../models/RFIDModel.php';
require_once __DIR__ . '/../../models/SensorModel.php';
require_once __DIR__ . '/../../models/DeviceModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Require authentication
$auth = new AuthController();
$auth->requireAuth();

// Initialize models
$rfidModel = new RFIDModel();
$sensorModel = new SensorModel();
$deviceModel = new DeviceModel();

// Get latest sensor data
$latestData = $sensorModel->getLatestData();

// Get temperature & humidity history for the last hour
$tempHumHistory = $sensorModel->getRecentHistory(1);

// Get RFID stats
$rfidCount = $rfidModel->getRFIDCount();
$lastRFIDScan = $rfidModel->getLastScan();

// Get device stats
$deviceCount = $deviceModel->getDeviceCount();
$activeDevices = $deviceModel->getActiveDevices('1 hour');

// Prepare response
$response = [
    'success' => true,
    'currentTemp' => $latestData ? floatval($latestData['temp']) : null,
    'currentHum' => $latestData ? floatval($latestData['hum']) : null,
    'tempUpdated' => $latestData ? $latestData['recorded_at'] : null,
    'humUpdated' => $latestData ? $latestData['recorded_at'] : null,
    'tempHumHistory' => $tempHumHistory,
    'rfidCount' => $rfidCount,
    'lastRFIDScan' => $lastRFIDScan,
    'deviceCount' => $deviceCount,
    'activeDevices' => $activeDevices
];

header('Content-Type: application/json');
echo json_encode($response);