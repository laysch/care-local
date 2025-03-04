<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['username'];
}

require_once 'inc/database.php';
$query = "SELECT * FROM jobs ORDER BY created_at DESC LIMIT 3";
$results = $conn->query($query);
$jobs = array();
if ($results->num_rows > 0) {
    while ($row = $results->fetch_assoc()) {
        $jobs[] = ["title" => $row['jobtitle'],
                "description" => $row['description'],
                "skills" => $row['skills']
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLocal</title>
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
            background-image: url('https://example.com/background.jpg');
            background-attachment: fixed;
            background-repeat: repeat;
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
            color: var(--headingsColor);
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            color: var(--bodyTextColor);
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .cta-buttons a {
            background-color: var(--accent1BgColor);
            color: var(--accent1TextColor);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .job-feed {
            margin-top: 30px;
        }

        .job-box {
            background-color: var(--postBgColor);
            border: 1px solid var(--bordersColor);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .job-box h3 {
            font-size: 1.5em;
            color: var(--headingsColor);
            margin-bottom: 10px;
        }

        .job-box p {
            font-size: 1em;
            color: var(--bodyTextColor);
            margin-bottom: 10px;
        }

        .job-box .skills {
            font-style: italic;
            color: var(--italicTextColor);
        }
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <section class="hero">
                <h1>Welcome to CareLocal</h1>
                <p>Where Local Talent Meets Local Needs</p>
                <div class="cta-buttons">
                    <a href="#">Find Jobs</a>
                    <a href="#">Post Jobs</a>
                    <a href="#">Build Community</a>
                </div>
            </section>

            <!-- Job Feed -->
            <section class="job-feed">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-box">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <p class="skills">Skills: <?php echo htmlspecialchars($job['skills']); ?></p>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://static.tumblr.com/kmw8hta/1WKpaiuda/tooltipster.main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/js/pixelution.js"></script>
</body>
</html>

