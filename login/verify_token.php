<?php
require_once 'config.php';

function getAuthorizationHeader(){
    $headers = null;
    
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    
    return $headers;
}

function getBearerToken() {
    $headers = getAuthorizationHeader();
    
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function verifyToken() {
    $token = getBearerToken();
    
    if (!$token) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode([
            'status' => 'error',
            'message' => 'No token provided'
        ]);
        exit();
    }
    
    $decoded = JWT::decode($token, JWT_SECRET_KEY);
    
    if (!$decoded) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token'
        ]);
        exit();
    }
    
    return $decoded;
}
?> 