<?php
session_start();
require_once 'inc/database.php';
include_once 'inc/func.php';

$userId = $_SESSION['user_id'];

$jobId = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT jobtitle, poster_id FROM jobs WHERE id = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();
$jobResult = $stmt->get_result();
$job = $jobResult->fetch_assoc();
$stmt->close();

if (!$job || $job['poster_id'] !== $userId) {
    echo "<p style='color:red; text-align:center;'>You are not authorized to view applications for this job.</p>";
    exit;
}

$stmt = $conn->prepare("
    SELECT ja.*, u.username, u.email
    FROM job_applications ja
    JOIN users u ON ja.user_id = u.id
    WHERE ja.job_id = ?
    ORDER BY ja.applied_at DESC
");
$stmt->bind_param("i", $jobId);
$stmt->execute();
$appResult = $stmt->get_result();
$applications = $appResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$posterId = $job['poster_id'];
$posterUsername = getUsernameById($conn, $posterId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Applications | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <link rel="stylesheet" href="style/messages.css">
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
            background-color: #fff;
            font-family: 'Share Tech Mono', monospace;
            color: #5D674C;
        }

        #main-body-wrapper {
            max-width: 1000px;
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
            justify-content: center;
            align-items: center; /* Center buttons horizontally */
            gap: 20px; /* Space between buttons */
            padding-bottom: 20px;
        }

        /* Original Button */
        .btn,
        .btn:link,
        .btn:visited {
            display: inline-block; /* Keep as inline-block */
            padding: 10px 20px;
            background-color: #efac9a; /* Olive green background for button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            width: fit-content; /* Ensures the button only takes up as much width as its content */
            cursor: pointer;
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
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Job Applications for: <?php echo htmlspecialchars($job['jobtitle']) ?></h1>
        </section>
        <div class="messages-container">
            <?php if (empty($applications)) {
                echo "<p style=\"text-align:center;\">No applications have been submitted for this job yet.</p>";
            } else {
                foreach ($applications as $app) {
                    echo '
                        <div class="messages-section">
                            <div class="messages-header">
                                <span>' . htmlspecialchars($app['username']) . '</span>
                                <button class="toggle-btn">
                                    <a href="messages.php?recipient_id=' . $posterId . '&recipient_name=' . urlencode($posterUsername). '&title=RE+'. urlencode($job['jobtitle']) .'#sendMessageForm">Send Message</a>
                                </button>
                                <form method="POST" action="inc/deleteApplication.php">
                                    <input type="hidden" name="application_id" value="' . $app['id'] . '">
                                    <input type="hidden" name="job_id" value="' . $jobId . '">
                                    <button type="submit" class="toggle-btn">Delete Application</button>
                                </form>
                            </div>
                        <div id="receivedMessages">
                            <strong>Email:</strong> ' . htmlspecialchars($app['email']) . '<br>
                            <strong>Interest:</strong> ' . nl2br(htmlspecialchars($app['interest'])) . '<br>
                            <strong>Qualification:</strong> ' . nl2br(htmlspecialchars($app['qualified'])) . '<br>
                            <strong>Questions:</strong> ' . nl2br(htmlspecialchars($app['questions'])) . '
                        </div>
                    ';
                };
            };
            ?>
        </div>
    </div>
</body>
</html>