<?php
// Start the session
session_start();

// Check if the job ID is provided
if (!isset($_GET['job_id'])) {
    header('Location: job-cart.php');
    exit;
}

$jobId = $_GET['job_id'];

// Check if the job exists in the cart
if (!isset($_SESSION['job_cart'][$jobId])) {
    echo "<p>Job not found in cart.</p>";
    exit;
}

// Get the job details
$job = $_SESSION['job_cart'][$jobId];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .apply-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .job-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }
        .job-description {
            font-size: 1em;
            color: #666;
            margin-bottom: 10px;
        }
        .job-skills {
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="apply-container">
        <h1>Apply for Job</h1>
        <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
        <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
        <div class="job-skills">Skills: <?php echo htmlspecialchars($job['skills']); ?></div>
        <p>This is where the application form would go.</p>
    </div>
</body>
</html>
