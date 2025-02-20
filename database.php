<?php
$host = "localhost";
$dbname = "job_postings";
$username = "root";
$password = "";  // No password

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_errno) {
    die("Connection error: " . $conn->connect_error);
}
?>
