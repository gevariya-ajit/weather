<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Add this constant for JWT
define('JWT_SECRET_KEY', 'fuu7FM6j6d1GC/ir6QH5VQskSypjpq5mjFQPpw7Hx5s='); 

$hostname = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$username = "3pKsFkk7zuFPMUt.root";
$password = "luyoL7hTkuLOrbJD";
$dbname = "test";
$port = 4000;

// Create connection with SSL
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $hostname, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 