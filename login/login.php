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
        $required_fields = ['username', 'password'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Get POST data
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        
        // Check user credentials
        $sql = "SELECT id, username, email, firstname, lastname, picture FROM user WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            throw new Exception('Invalid username or password!');
        }
        
        $user = $result->fetch_assoc();
        
        // Create JWT
        $payload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24)
        ];
        
        $jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');
        
        // Send success response
        $response = [
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $jwt,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'picture' => $user['picture']
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
    if (isset($conn)) $conn->close();
}
?>
