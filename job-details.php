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
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css" rel="stylesheet">
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
            background-color: #f9eedd;
            font-family: 'Share Tech Mono', monospace;
            color: #5D674C;
        }

        #main-body-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hero {
            text-align: center;
            padding: 50px 20px;
        }

        .hero h1 {
            font-size: 2.5em;
            color: #5D674C;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            color: #839c99;
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .cta-buttons a {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .cta-buttons a:hover {
            background-color: #efac9a;
        }

        .job-details {
            margin-top: 20px;
            text-align: center;
        }

        .job-details h1 {
            font-size: 2em;
            color: #5D674C;
            margin-bottom: 20px;
        }

        .job-details p {
            font-size: 1.2em;
            color: #5D674C;
            margin-bottom: 15px;
        }

        
/* Button Container */
.button-container {
    display: flex;
    flex-direction: column; /* Stack buttons vertically */
    align-items: center; /* Center buttons horizontally */
    gap: 20px; /* Space between buttons */
}

/* Original Button */
.btn {
    display: inline-block; /* Keep as inline-block */
    padding: 10px 20px;
    background-color: #efac9a; /* Olive green background for button */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    width: fit-content; /* Ensures the button only takes up as much width as its content */
}

/* Smaller Button */
.btn-small {
    display: inline-block; /* Keep as inline-block */
    padding: 6px 12px; /* Smaller padding */
    background-color: #efac9a; /* Same background color */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.9em; /* Smaller font size */
    width: fit-content; /* Ensures the button only takes up as much width as its content */
}

        .btn:hover {
            background-color: #efac9a; /* Light peach on hover */
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Job Details</h1>
            <p>Below are the details for the selected job posting.</p>
        </section>

       <div class="button-container">
                <!-- Add to Job Cart Form -->
                <form action="add-to-cart.php" method="POST" style="display: inline;">
                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                    <input type="hidden" name="job_title" value="<?php echo htmlspecialchars($job['jobtitle']); ?>">
                    <input type="hidden" name="job_description" value="<?php echo htmlspecialchars($job['description']); ?>">
                    <input type="hidden" name="job_skills" value="<?php echo htmlspecialchars($job['skills']); ?>">
                    <button type="submit" name="add_to_cart" class="btn-small">Add to Job Cart</button>
                </form>

                <!-- Back to Job Listings Button -->
                <a href="search-jobs.php" class="btn">Back to Job Listings</a>
            </div>
        </div>
    </div>
</body>
</html>
