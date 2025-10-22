<?php
// config.php
// Edit these values to match your environment
define('DB_HOST', '103.200.23.120');
define('DB_NAME', 'nrojunec_smart_home');
define('DB_USER', 'nrojunec_user_1');
define('DB_PASS', 'Smarthome@123'); // XAMPP default empty
define('API_KEY_HEADER', 'X-API-KEY'); // header name to send device key

// JWT Configuration
define('JWT_SECRET', '18a530a0a923824fd70a4700a3e6b9a7bcdd5d78b28587f072b3e4edd7530ab0'); // 256-bit secure random secret
define('JWT_EXPIRATION', 3600); // Token expiration in seconds (1 hour)

// Session configuration is now in includes/session.php

// Blynk
define('BLYNK_AUTH_TOKEN', 'p2U1heWBsEQEHOBhUI9ihUyGDCGSd72g'); // optional, for server->Blynk push

function getPDO(){
    static $pdo = null;
    if($pdo === null){
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
    }
    return $pdo;
}

function require_api_key(){
    // support header or ?api_key=
    $headers = getallheaders();
    $key = null;
    if (!empty($headers[API_KEY_HEADER])) $key = $headers[API_KEY_HEADER];
    if (!$key && !empty($_GET['api_key'])) $key = $_GET['api_key'];
    if (!$key) {
        http_response_code(401);
        echo json_encode(['error'=>'API key required']);
        exit;
    }
    // validate
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM devices WHERE api_key = :k LIMIT 1");
    $stmt->execute([':k'=>$key]);
    $dev = $stmt->fetch();
    if (!$dev){
        http_response_code(403);
        echo json_encode(['error'=>'Invalid API key']);
        exit;
    }
    return $dev; // array
}

function json_response($data){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
