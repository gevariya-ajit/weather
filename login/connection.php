<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Add this constant for JWT
define('JWT_SECRET_KEY', 'fuu7FM6j6d1GC/ir6QH5VQskSypjpq5mjFQPpw7Hx5s='); 

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($hostname, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Close connection
$conn->close();
?> 