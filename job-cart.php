<?php
session_start();
require_once 'inc/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Cart</title>
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
            background-color: #f9eedd;
            color: #5D674C;
            margin: 0;
            padding: 0;
        }

        #container {
            display: flex;
        }

        #main-body-wrapper {
            flex: 1;
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

        .apply-button, .delete-button {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }

        .apply-button {
            background-color: #efac9a;
        }

        .apply-button:hover {
            background-color: #e89a87;
        }

        .delete-button {
            background-color: #ff4d4d; /* Red color for delete button */
        }

        .delete-button:hover {
            background-color: #cc0000; /* Darker red on hover */
        }

     .return-button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #5D674C; /* Button background color */
    color: white !important; /* Force text color to white */
    text-decoration: none; /* Remove underline */
    border-radius: 5px; /* Rounded corners */
    font-weight: bold; /* Bold text */
    margin-top: 20px; /* Space above the button */
}

.return-button:hover {
    background-color: #efac9a; /* Change background color on hover */
    color: white !important; /* Ensure text stays white on hover */
}
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <div class="job-cart-container">
                <h1>Your Job Cart</h1>
                <?php if (isset($_SESSION['job_cart']) && !empty($_SESSION['job_cart'])): ?>
                    <?php foreach ($_SESSION['job_cart'] as $jobId => $job): ?>
                        <div class="job-item">
                            <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                            <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                            <div class="job-skills">Skills: <?php echo htmlspecialchars($job['skills']); ?></div>
                            <a href="apply.php?job_id=<?php echo $jobId; ?>" class="apply-button">Apply</a>
                            <!-- Delete Button -->
                            <form action="delete-from-cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="job_id" value="<?php echo $jobId; ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your job cart is empty.</p>
                <?php endif; ?>

                <!-- Return to Job Search Button -->
                <div style="text-align: center; margin-top: 20px;">
                    <a href="search-jobs.php" class="return-button">Return to Job Search</a>
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
