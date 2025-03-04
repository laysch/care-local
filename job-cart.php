<?php
// Start the session
session_start();

// Check if the cart is empty
if (!isset($_SESSION['job_cart']) || empty($_SESSION['job_cart'])) {
    echo "<p>Your job cart is empty.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .job-cart-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .job-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .job-item:last-child {
            border-bottom: none;
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
        .apply-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #efac9a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        .apply-button:hover {
            background-color: #e89a87;
        }
    </style>
</head>
<body>
    <div class="job-cart-container">
        <h1>Your Job Cart</h1>
        <?php foreach ($_SESSION['job_cart'] as $jobId => $job): ?>
            <div class="job-item">
                <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                <div class="job-skills">Skills: <?php echo htmlspecialchars($job['skills']); ?></div>
                <a href="apply.php?job_id=<?php echo $jobId; ?>" class="apply-button">Apply</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
