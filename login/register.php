<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';
require_once 'connection.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate required fields
        $required_fields = ['username', 'email', 'password', 'firstname', 'lastname'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Get POST data
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $picture = isset($_POST['picture']) ? $_POST['picture'] : '';
        
        // Check existing user
        $check_sql = "SELECT * FROM user WHERE username = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception('Check prepare failed: ' . $conn->error);
        }
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Username or email already exists');
        }
        
        // Insert user
        $sql = "INSERT INTO user (username, email, password, firstname, lastname, picture) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Insert prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $username, $email, $password, $firstname, $lastname, $picture);
        if (!$stmt->execute()) {
            throw new Exception('Insert failed: ' . $stmt->error);
        }
        
        $userId = $conn->insert_id;
        
        // Create JWT
        $payload = [
            'user_id' => $userId,
            'username' => $username,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];
        
        $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
        
        // Send success response
        $response = [
            'status' => 'success',
            'message' => 'Registration successful',
            'token' => $jwt,
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'picture' => $picture
            ]
        ];
        
        echo json_encode($response);
        
    } else {
        throw new Exception('Invalid request method');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($conn)) $conn->close();
}
?>