<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/DeviceModel.php';

// Require authentication
$auth = new AuthController();
$auth->requireAuth();

// Initialize model
$deviceModel = new DeviceModel();

// Process action
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'list':
        $devices = $deviceModel->getAllDevices();
        foreach ($devices as &$device) {
            // Get last data timestamp and count
            $stats = $deviceModel->getDeviceStats($device['id']);
            $device['last_data'] = $stats['last_data'];
            $device['data_count'] = $stats['data_count'];
        }
        header('Content-Type: application/json');
        echo json_encode($devices);
        exit;

    case 'get':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        $device = $deviceModel->getDevice($id);
        if (!$device) {
            $response = ['success' => false, 'message' => 'Device not found'];
            break;
        }

        // Get device stats
        $stats = $deviceModel->getDeviceStats($id);
        $device['last_data'] = $stats['last_data'];
        $device['data_count'] = $stats['data_count'];

        header('Content-Type: application/json');
        echo json_encode($device);
        exit;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['device_name'])) {
            $response = ['success' => false, 'message' => 'Device name is required'];
            break;
        }

        $result = $deviceModel->createDevice($data['device_name']);
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Device created successfully',
                'device' => $result,
                'api_key' => $result['api_key']
            ];
        } else {
            $response = ['success' => false, 'message' => 'Failed to create device'];
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        if ($deviceModel->updateDevice($data['id'], $data)) {
            $response = ['success' => true, 'message' => 'Updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Update failed'];
        }
        break;

    case 'delete':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        if ($deviceModel->deleteDevice($data['id'])) {
            $response = ['success' => true, 'message' => 'Deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Delete failed'];
        }
        break;

    case 'regenerate':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        $newKey = $deviceModel->regenerateApiKey($data['id']);
        if ($newKey) {
            $response = [
                'success' => true,
                'message' => 'API key regenerated successfully',
                'api_key' => $newKey
            ];
        } else {
            $response = ['success' => false, 'message' => 'Failed to regenerate API key'];
        }
        break;

    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

header('Content-Type: application/json');
echo json_encode($response);