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
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #f9eedd;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
        }

        body {
            font-family: 'Share Tech Mono', monospace;
            background-color: #fff;
            color: #5D674C;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            
        }

        /* Main Content */
        #main-body-wrapper {
            flex: 1;
            padding: 20px;
        }

        .apply-container {
            max-width: 800px;
            margin: 0 auto;
            background: #cdd8c4;
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

        .return-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #5D674C;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }

        .return-button:hover {
            background-color: #efac9a;
            color: white !important;
        }
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <div class="apply-container">
                <h1>Apply for Job</h1>
                <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                <div class="job-skills">Skills: <?php echo htmlspecialchars($job['skills']); ?></div>
                <p>This is where the application form would go.</p>

                <!-- Return to Job Cart Button -->
                <div style="text-align: center; margin-top: 20px;">
                    <a href="job-cart.php" class="return-button">Return to Job Cart</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://static.tumblr.com/kmw8hta/1WKpaiuda/tooltipster.main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/js/pixelution.js"></script>
</body>
</html>
