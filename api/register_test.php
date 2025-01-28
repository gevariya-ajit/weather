<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Test 1: Basic PHP
echo json_encode([
    'test1' => 'Basic PHP working',
    'post_data' => $_POST
]);
exit;

// If Test 1 works, comment it out and uncomment Test 2
/*
// Test 2: Database Connection
require_once 'config.php';
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    echo json_encode([
        'test2' => 'Database connection successful',
        'host' => DB_HOST,
        'database' => DB_NAME
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
exit;
*/

// If Test 2 works, comment it out and uncomment Test 3
/*
// Test 3: JWT Library
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;

try {
    $payload = ['test' => 'data'];
    $jwt = JWT::encode($payload, 'test_key', 'HS256');
    echo json_encode([
        'test3' => 'JWT working',
        'token' => $jwt
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
exit;
*/

// If Test 3 works, comment it out and uncomment Test 4
/*
// Test 4: Database Insert
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $test_username = 'test_user_' . time();
    $test_email = 'test' . time() . '@example.com';
    $test_password = md5('test_password');
    
    $sql = "INSERT INTO user (username, email, password, firstname, lastname) 
            VALUES (?, ?, ?, 'Test', 'User')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $test_username, $test_email, $test_password);
    
    if ($stmt->execute()) {
        echo json_encode([
            'test4' => 'Database insert successful',
            'user_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
exit;
*/ 