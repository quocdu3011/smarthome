<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../vendor/autoload.php'; // For JWT

use Firebase\JWT\JWT;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login($username, $password) {
        $user = $this->userModel->validateUser($username, $password);
        
        if ($user) {
            $token = $this->generateToken($user);
            return [
                'success' => true,
                'token' => $token,
                'user' => $user
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Invalid credentials'
        ];
    }

    private function generateToken($user) {
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION
        ];

        return JWT::encode($payload, JWT_SECRET, 'HS256');
    }

    public function validateToken($token) {
        try {
            $key = new \Firebase\JWT\Key(JWT_SECRET, 'HS256');
            $decoded = JWT::decode($token, $key);
            return [
                'success' => true,
                'data' => $decoded
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Invalid token'
            ];
        }
    }

    public function requireAuth() {
        $headers = getallheaders();
        $token = null;

        // Check Authorization header
        if (isset($headers['Authorization'])) {
            $auth = explode(' ', $headers['Authorization']);
            if (count($auth) === 2 && strtolower($auth[0]) === 'bearer') {
                $token = $auth[1];
            }
        }

        // Check session
        if (!$token && isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
        }

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $validation = $this->validateToken($token);
        if (!$validation['success']) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        return $validation['data'];
    }
}