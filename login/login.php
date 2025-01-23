<?php
session_start();
header('Content-Type: application/json');
require_once 'config.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . $conn->connect_error
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encrypt password using MD5 for comparison
    
    // Check user credentials
    $sql = "SELECT username, email, firstname, lastname, picture FROM user WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username']; // Set session if needed
        
        // Generate JWT token
        $payload = [
            'user_id' => $user['username'],
            'email' => $user['email'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname']
        ];
        
        $token = JWT::encode($payload, JWT_SECRET_KEY);
        $expireTime = (time() + JWT_EXPIRE_TIME) * 1000; // Convert to epoch milliseconds
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful!',
            'token' => $token,
            'token_expires_at' => $expireTime,
            'user' => [
                'username' => $user['username'],
                'email' => $user['email'],
                'firstname' => $user['firstname'],
                'lastname' => $user['lastname'],
                'picture' => $user['picture']
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password!'
        ]);
    }
    $stmt->close();
}
$conn->close();
?>
