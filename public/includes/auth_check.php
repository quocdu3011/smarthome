<?php
session_start();
require_once __DIR__ . '/../../controllers/AuthController.php';

$auth = new AuthController();

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['token'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // AJAX request
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    } else {
        // Regular request
        header('Location: index.php');
        exit;
    }
}

// Validate token
$validation = $auth->validateToken($_SESSION['token']);
if (!$validation['success']) {
    // Token invalid/expired
    session_destroy();
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // AJAX request
        http_response_code(401);
        echo json_encode(['error' => 'Token expired']);
        exit;
    } else {
        // Regular request
        header('Location: index.php');
        exit;
    }
}

// Set user data for use in pages
$user = $_SESSION['user'];