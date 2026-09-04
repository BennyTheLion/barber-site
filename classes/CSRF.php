<?php
class CSRF {
    public static function generateToken() {
        if(!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    public static function validateToken($token) {
        if(!isset($_SESSION[CSRF_TOKEN_NAME]) || !hash_equals($_SESSION[CSRF_TOKEN_NAME], $token)) {
            throw new Exception("Invalid CSRF token");
        }
        return true;
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
