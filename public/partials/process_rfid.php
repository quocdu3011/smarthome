<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/RFIDModel.php';

// Require authentication
$auth = new AuthController();
$auth->requireAuth();

// Initialize model
$rfidModel = new RFIDModel();

// Process action
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'list':
        $users = $rfidModel->getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;

    case 'get':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        $user = $rfidModel->getUserById($id);
        if (!$user) {
            $response = ['success' => false, 'message' => 'User not found'];
            break;
        }

        header('Content-Type: application/json');
        echo json_encode($user);
        exit;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            $response = ['success' => false, 'message' => 'Invalid data'];
            break;
        }

        // Validate meta JSON if provided
        if (isset($data['meta']) && $data['meta']) {
            if (json_decode($data['meta']) === null) {
                $response = ['success' => false, 'message' => 'Invalid meta JSON'];
                break;
            }
        }

        if ($rfidModel->updateUser($data['id'], $data)) {
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

        if ($rfidModel->deleteUser($data['id'])) {
            $response = ['success' => true, 'message' => 'Deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Delete failed'];
        }
        break;

    case 'logs':
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $uid = $_GET['uid'] ?? null;
        $logs = $rfidModel->getLogs($start, $end, $uid);
        header('Content-Type: application/json');
        echo json_encode($logs);
        exit;

    case 'recent':
        $logs = $rfidModel->getRecentLogs();
        header('Content-Type: application/json');
        echo json_encode($logs);
        exit;

    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

header('Content-Type: application/json');
echo json_encode($response);