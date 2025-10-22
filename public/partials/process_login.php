<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

header('Content-Type: application/json');

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Username and password are required'
    ]);
    exit;
}

$auth = new AuthController();
$result = $auth->login($data['username'], $data['password']);

if ($result['success']) {
    session_start();
    $_SESSION['user'] = $result['user'];
    $_SESSION['token'] = $result['token'];
}

echo json_encode($result);