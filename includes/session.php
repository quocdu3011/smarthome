<?php
// Ensure this file is included before any session_start() calls
if (session_status() !== PHP_SESSION_NONE) {
    return;
}

// Set session name
session_name('SMARTHOME_SESSION');

// Configure session cookie parameters
session_set_cookie_params([
    'lifetime' => 0,                   // Until browser closes
    'path' => '/',                     // Available across entire domain
    'domain' => '',                    // Current domain only
    'secure' => true,                  // Only send over HTTPS
    'httponly' => true,               // Not accessible via JavaScript
    'samesite' => 'Strict'            // Strict same-site policy
]);

// Other session security settings
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Start the session
session_start();