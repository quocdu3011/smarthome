<?php
namespace SmartHome\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SmartHome\Models\UserModel;

class AuthController {
    private $userModel;
    private $secretKey;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->secretKey = JWT_SECRET;
    }

    private function createToken($user) {
        $payload = [
            'uid' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token) {
        try {
            $key = new Key($this->secretKey, 'HS256');
            $decoded = JWT::decode($token, $key);
            
            // Verify token belongs to correct user
            if ($decoded->uid !== $_SESSION['user_id']) {
                return false;
            }

            // Check if token is expired
            if ($decoded->exp < time()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function login($username, $password) {
        $user = $this->userModel->validateUser($username, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Create JWT token
            $token = $this->createToken($user);
            $_SESSION['token'] = $token;

            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            return true;
        }
        
        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->userModel->getUserById($_SESSION['user_id']);
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['token']) && $this->validateToken($_SESSION['token']);
    }

    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            // Store the requested URL for redirect after login
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            // Check if this is an AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized', 'redirect' => '/index.php']);
                exit;
            }
            
            // Redirect to login page
            header('Location: /index.php');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }
            header('Location: /dashboard.php');
            exit;
        }
    }

    public function refreshToken() {
        if (isset($_SESSION['user_id'])) {
            $user = $this->getCurrentUser();
            if ($user) {
                $_SESSION['token'] = $this->createToken($user);
                return true;
            }
        }
        return false;
    }
}