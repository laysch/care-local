<?php
$servername = "localhost";
$username = "root";
$password = "";  // No password

// Create connection to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the 'job_postings' database if it doesn't exist
$query = "CREATE DATABASE IF NOT EXISTS job_postings";
$conn->query($query);

// Select the 'job_postings' database
$conn->select_db('job_postings');

// Create 'jobs' table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jobtitle VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    skills TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($query);

// Create 'users' table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(255) NOT NULL
)";
$conn->query($query);

// Close the connection
$conn->close();

// Success message and button to redirect to jobpost.php
echo "Database and tables created successfully.";
?>
<!-- Add a button to redirect to jobpost.php after successful setup -->
<form action="JobPost.php" method="get">
    <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px;">Go to Job Post</button>
</form>
