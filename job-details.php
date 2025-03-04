<?php
$currentPage = 'Job Details';
session_start();

require_once 'inc/database.php';

// Get job ID from URL
$job_id = $_GET['id'];

// Query to get the job details
$query = "SELECT * FROM jobs WHERE id = $job_id";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch the job details
$job = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details</title>
    <style>
        body {
            background-color: #5D674C; /* Olive green background */
            font-family: Georgia, serif;
            color: #FFFFFF; /* White text */
        }

        .job-details {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #FCEADE; /* Soft peach for buttons */
            color: #5D674C; /* Olive green text */
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #F3E9B5; /* Light yellow on hover */
            color: #5D674C; /* Keep olive green text */
        }
    </style>
</head>
<body>
    <div class="job-details">
        <h1>Job Title: <?php echo htmlspecialchars($job['jobtitle']); ?></h1>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
        <p><strong>Skills Required:</strong> <?php echo htmlspecialchars($job['skills']); ?></p>
        
        <a href="jobsearch.php" class="btn">Back to Job Listings</a>
    </div>
</body>
</html>