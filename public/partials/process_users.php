<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/UserModel.php';

// Require authentication and admin role
$auth = new AuthController();
$userData = $auth->requireAuth();
if ($userData->role !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Initialize model
$userModel = new UserModel();

// Process action
$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'list':
        $users = $userModel->getUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;

    case 'get':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        $user = $userModel->getUserById($id);
        if (!$user) {
            $response = ['success' => false, 'message' => 'User not found'];
            break;
        }

        // Don't send password hash
        unset($user['password']);

        header('Content-Type: application/json');
        echo json_encode($user);
        exit;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['username']) || !isset($data['password']) || !isset($data['email'])) {
            $response = ['success' => false, 'message' => 'Missing required fields'];
            break;
        }

        // Validate password
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $data['password'])) {
            $response = ['success' => false, 'message' => 'Password does not meet requirements'];
            break;
        }

        // Check if username or email exists
        if ($userModel->getUserByUsername($data['username'])) {
            $response = ['success' => false, 'message' => 'Username already exists'];
            break;
        }
        if ($userModel->getUserByEmail($data['email'])) {
            $response = ['success' => false, 'message' => 'Email already exists'];
            break;
        }

        if ($userModel->createUser($data['username'], $data['password'], $data['email'], $data['role'] ?? 'user')) {
            $response = ['success' => true, 'message' => 'User created successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to create user'];
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id'])) {
            $response = ['success' => false, 'message' => 'ID is required'];
            break;
        }

        // Prevent modifying admin user
        $user = $userModel->getUserById($data['id']);
        if ($user['username'] === 'admin') {
            $response = ['success' => false, 'message' => 'Cannot modify admin user'];
            break;
        }

        // Validate password if provided
        if (isset($data['password']) && !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $data['password'])) {
            $response = ['success' => false, 'message' => 'Password does not meet requirements'];
            break;
        }

        // Check email if changed
        if (isset($data['email']) && $data['email'] !== $user['email']) {
            if ($userModel->getUserByEmail($data['email'])) {
                $response = ['success' => false, 'message' => 'Email already exists'];
                break;
            }
        }

        if ($userModel->updateUser($data['id'], $data)) {
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

        // Prevent deleting admin user
        $user = $userModel->getUserById($data['id']);
        if ($user['username'] === 'admin') {
            $response = ['success' => false, 'message' => 'Cannot delete admin user'];
            break;
        }

        if ($userModel->deleteUser($data['id'])) {
            $response = ['success' => true, 'message' => 'Deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Delete failed'];
        }
        break;

    default:
        $response = ['success' => false, 'message' => 'Invalid action'];
}

header('Content-Type: application/json');
echo json_encode($response);