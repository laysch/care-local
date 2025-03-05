<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

require_once 'inc/database.php';

$year = date('Y');
$monthNum = date('n');
$monthName = date('F');
$monthDays = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);
$firstDate = "$year-$monthNum-01";
$lastDate = "$year-$monthNum-$monthDays";
$firstDay = date('w', strtotime($firstDate));

$stmt = $conn->prepare("SELECT * FROM events WHERE DATE(datetime) BETWEEN ? AND ?");
$stmt->bind_param("ss", $firstDate, $lastDate);
$stmt->execute();
$result = $stmt->get_result();
$events = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$eventsByDay = [];
foreach ($events as $event) {
    $day = date('j', strtotime($event['datetime']));
    $eventsByDay[$day][] = $event;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="script.js" defer></script>
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #ffffff; /* White background */
            --bordersColor: #e0e0e0; /* Light gray borders */
            --bodyTextColor: #333333; /* Dark gray text */
            --linksColor: #222222;
            --linksHoverColor: #cdd8c4;
        }

        body {
            background-color: var(--backgroundColor); /* White background */
            font-family: var(--bodyFontFamily);
            color: var(--bodyTextColor);
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background-color: #fff; /* Light gray background */
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        #sidebar a {
            color: #000000; /* Black text for sidebar links */
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        #sidebar a:hover {
            background-color: #fff; 
        }

        /* Main Body */
        #main-body-wrapper {
            flex: 1;
            padding: 20px;
        }

        .hero-section {
            background-color: #cdd8c4;
            text-align: center;
            padding: 50px 20px;
        }

        .hero-section h1 {
            font-size: 2.5em;
            color: var(--bodyTextColor);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff; /* White background */
            border: 1px solid var(--bordersColor);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid var(--bordersColor);
            width: 14%; /* Equal width for each day (100% / 7 days) */
            height: 120px; /* Increased height for calendar boxes */
        }

        th {
            background-color: #cdd8c4;
            font-size: 1.2em;
            color: var(--bodyTextColor);
            height: 50px; /* Height for the header row */
            white-space: nowrap; /* Prevent day names from wrapping */
        }

        td {
            vertical-align: top;
        }

        .dayNumber {
            font-weight: bold;
            color: var(--bodyTextColor);
            margin-bottom: 10px;
        }

        .event {
            font-size: 0.9em;
            color: #666666; /* Gray event text */
            margin-top: 5px;
        }

        .features-grid {
            margin-top: 30px;
        }

        .feature-card {
            background-color: #ffffff; /* White background */
            border: 1px solid var(--bordersColor);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <section class="hero-section">
                <h1><?php echo $monthName . " " . $year; ?></h1>
            </section>

            <div class="features-grid">
                <div class="feature-card">
                    <table>
                        <tr style="line-height: 40px">
                            <th>Sunday</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                        </tr>
                        <?php                
                        $totalCells = ceil(($firstDay + $monthDays) / 7) * 7;
                        $counter = 1;
                        echo "<tr>";
                        for ($i = 0; $i < $totalCells; $i++) {
                            if ($i < $firstDay || $counter > $monthDays) {
                                echo "<td></td>";
                            } else {
                                echo "<td><div class='dayNumber'>" . $counter . "</div>";
                                if (isset($eventsByDay[$counter])) {
                                    foreach ($eventsByDay[$counter] as $event) {
                                        echo "<div class='event'>" . htmlspecialchars($event['title']) . "</div>";
                                    }
                                }
                                echo "</td>";
                                $counter++;
                            }
                            if (($i + 1) % 7 == 0) {
                                echo "</tr>";
                                if ($i + 1 < $totalCells) {
                                    echo "<tr>";
                                }
                            }
                        }
                        ?>
                    </table>
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
