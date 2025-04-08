<?php
require_once 'inc/session.php';
require_once 'inc/database.php';
include_once 'inc/func.php';

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$job_title = '';

if ($job_id > 0) {
    $stmt = $conn->prepare("SELECT jobtitle FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $stmt->bind_result($job_title);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $job_id = $_POST['job_id'];

    $stmt = $conn->prepare("INSERT INTO events (job_id, title, date, location, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $job_id, $title, $date, $location, $description);

    if ($stmt->execute()) {
        echo "Event created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event | CareLocal</title>
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
            background-color: #ffffff;
            background-image: url('https://example.com/background.jpg');
            background-attachment: fixed;
            background-repeat: repeat;
            font-family: 'Share Tech Mono', monospace;
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

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #839c99;
            border-radius: 5px;
        }

        .category-btn {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .category-btn.active {
            background-color: #efac9a;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tag {
            background-color: #D1D79D;
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tag.selected {
            background-color: #5D674C;
        }

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
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <div class="form-container">
            <form action="create-event.php" method="post">
                <h2>Create Event</h2>
                <label>Title:</label>
                <input type="text" name="title" required><br>
                <label>Date & Time:</label>
                <input type="datetime-local" name="date" id="event-date" required><br>
                <label>Location:</label>
                <input type="text" name="location" required><br>
                <label>Description:</label>
                <textarea name="description" required></textarea><br>
                <label>For Job:</label>
                <input type="text" value="<?= htmlspecialchars($job_title) ?>" disabled><br>
                <input type="hidden" name="job_id" value="<?= $job_id ?>">
                <button type="submit" class="btn">Create Event</button>
            </form>
        </div>
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const input = document.getElementById("event-date");
        const now = new Date();
        const localISOTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        input.min = localISOTime;
    });
</script>
</html>