<?php
session_start();

require_once 'inc/database.php';
include_once 'inc/func.php';
$currentUserId = $_SESSION['user_id'] ?? null;

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    echo "Invalid event ID.";
    exit;
}

$stmt = $conn->prepare("
    SELECT events.*, jobs.jobtitle, jobs.poster_id
    FROM events 
    JOIN jobs ON events.job_id = jobs.id 
    WHERE events.id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$posterId = $event['poster_id'];
$posterUsername = getUsernameById($conn, $posterId);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($event['title']) ?> - Event Details | CareLocal</title>
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

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Event Details</h1>
        </section>

         <div class="job-details">
            <h1><?php htmlspecialchars($event['title']) ?></h1>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            <p><strong>When:</strong> <?= date("F j, Y \\a\\t g:i A", strtotime($event['date'])) ?></p>
            <p><small>Posted by <?php echo $posterUsername; ?> at <?php echo date("F j, Y, g:i a", strtotime($event['created_at'])); ?></small</p>

            <div class="button-container">            
                <button class="btn"><a href="job-details.php?id=<?php echo $event['job_id']; ?>" >See related Job</a></button>
                <button class="btn"><a href="messages.php?recipient_id=<?php echo $posterId; ?>&recipient_name=<?php echo urlencode($posterUsername); ?>&title=RE+<?php echo urlencode($job['jobtitle']); ?>#sendMessageForm">
                    Send a message to <?php echo htmlspecialchars($posterUsername); ?>
                </a></button>
            </div>
            <?php if ($currentUserId && $currentUserId == $posterId): ?>
                <div class="button-container">
                    <button class="btn" onclick="return confirm('Are you sure you want to delete this posting?');"><a href="inc/deleteEvent.php?id=<?= $event['id'] ?>">Delete Event</a></button>                  
                </div>
            <?php endif; ?>          
        </div>
    </div>
</body>
</html>
