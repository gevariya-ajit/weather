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
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Encrypt password using MD5
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $picture = $_POST['picture'];
    
    // Check if username or email already exists
    $check_sql = "SELECT * FROM user WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Registration failed! Username or email already exists.'
        ]);
    } else {
        // Insert new user
        $sql = "INSERT INTO user (username, email, password, firstname, lastname, picture) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $email, $password, $firstname, $lastname, $picture);
        
        if ($stmt->execute()) {
            // Generate JWT token
            $payload = [
                'user_id' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname
            ];
            
            $token = JWT::encode($payload, JWT_SECRET_KEY);
            $expireTime = (time() + JWT_EXPIRE_TIME) * 1000; // Convert to epoch milliseconds
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Registration successful!',
                'token' => $token,
                'token_expires_at' => $expireTime,
                'user' => [
                    'username' => $username,
                    'email' => $email,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'picture' => $picture
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Registration failed! Error: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }
    $check_stmt->close();
}
$conn->close();
?>