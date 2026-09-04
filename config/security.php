<?php
// config/security.php

// CSRF Protection
define("CSRF_TOKEN_NAME", "csrf_token");
define("CSRF_TOKEN_LENGTH", 32);

// Password Hashing
define("PASSWORD_ALGO", PASSWORD_BCRYPT);
define("PASSWORD_OPTIONS", ["cost" => 12]);

// Session
define("SESSION_TIMEOUT", 3600); // 1 hour

// Security Headers Function
function setSecurityHeaders() {
    // Prevent XSS attacks
    header("X-XSS-Protection: 1; mode=block");
    
    // Prevent MIME type sniffing
    header("X-Content-Type-Options: nosniff");
    
    // Prevent clickjacking
    header("X-Frame-Options: DENY");
    
    // Referrer policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    // Content Security Policy (CSP) - מושבת זמנית לדיבאג
    // header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:;");
    
    // Permissions policy
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    
    // Strict Transport Security (HSTS) - רק ב-HTTPS
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }
}

// Session security function - תיקון
function setSecureSession() {
    // וודא שה-session פעיל
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Use strict mode
    ini_set('session.use_strict_mode', 1);
    
    // Use only cookies
    ini_set('session.use_only_cookies', 1);
    
    // HttpOnly cookies
    ini_set('session.cookie_httponly', 1);
    
    // SameSite strict
    ini_set('session.cookie_samesite', 'Strict');
    
    // Secure cookie (only HTTPS)
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    // Set session name
    session_name('barber_secure_session');
    
    // Regenerate session ID to prevent fixation - רק אם ה-session כבר פעיל
    if(session_status() === PHP_SESSION_ACTIVE && !isset($_SESSION['initialized'])) {
        session_regenerate_id(true);
        $_SESSION['initialized'] = true;
    }
}

// Input sanitization
function sanitizeInput($input) {
    if(is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate secure token
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone (Israeli format)
function validatePhone($phone) {
    // Remove non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if valid Israeli phone number
    return preg_match('/^(05[0-9]{8}|0[2-9][0-9]{7,8})$/', $phone);
}

// Rate limiting helper
function checkRateLimit($key, $limit = 5, $window = 900) {
    $rateFile = __DIR__ . "/../logs/rate_limit_" . $key . ".log";
    $currentTime = time();
    
    if(file_exists($rateFile)) {
        $data = json_decode(file_get_contents($rateFile), true);
        
        // Clean old entries
        $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($currentTime, $window) {
            return $timestamp > ($currentTime - $window);
        });
        
        if(count($data['attempts']) >= $limit) {
            return false;
        }
        
        $data['attempts'][] = $currentTime;
        file_put_contents($rateFile, json_encode($data));
        return true;
    }
    
    $data = ['attempts' => [$currentTime]];
    file_put_contents($rateFile, json_encode($data));
    return true;
}

// Log security events
function logSecurityEvent($event, $details = null) {
    $logFile = __DIR__ . "/../logs/security.log";
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $logEntry = "[$timestamp] [$event] IP: $ip | UA: $userAgent";
    if($details) {
        $logEntry .= " | Details: $details";
    }
    $logEntry .= PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// CSRF Token Functions
function generateCSRFToken() {
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if(!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateCSRFToken($token) {
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if(!isset($_SESSION[CSRF_TOKEN_NAME]) || !hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
        throw new Exception("Invalid CSRF token");
    }
    return true;
}

function getCSRFTokenField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
?>