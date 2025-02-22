<?php
$host = "localhost";
$dbname = "if0_38360278_carelocal";
$username = "if0_38360278";
$password = "P0giRtiAC6eHo";

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_errno) {
    die("Connection error: " . $conn->connect_error);
}
?>
