<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/SensorModel.php';

// Ensure we're sending JSON response even for errors
function json_error($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
    exit;
}

// Set error handler to catch PHP errors
set_error_handler(function($severity, $message, $file, $line) {
    json_error($message);
});

// Require authentication
$auth = new AuthController();
$auth->requireAuth();

// Initialize model
$sensorModel = new SensorModel();

// Process action
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'latest':
        $data = $sensorModel->getLatestData();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    case 'history':
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $interval = $_GET['interval'] ?? 'minute';

        $data = $sensorModel->getHistoricalData($start, $end, null, $interval);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    case 'stats':
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $deviceId = $_GET['device_id'] ?? null;
        try {
            $data = $sensorModel->getStatistics($start, $end, $deviceId);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        } catch (Exception $e) {
            json_error($e->getMessage());
        }

    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

header('Content-Type: application/json');
echo json_encode($response);