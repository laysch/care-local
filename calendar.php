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

        .hero-section {
            text-align: center;
            padding: 50px 20px;
        }

        .hero-section h1 {
            font-size: 2.5em;
            color: var(--headingsColor);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin: 0px;
            border-collapse: collapse;
            background-color: var(--postBgColor);
            border: 1px solid var(--bordersColor);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        tr {
            height: 100px;
        }

        th {
            border-bottom: 2px dashed var(--bordersColor);
            font-size: 1.2em;
            color: var(--headingsColor);
        }

        td {
            padding: 10px;
            border: 1px solid var(--bordersColor);
        }

        .dayNumber {
            font-weight: bold;
            color: var(--headingsColor);
        }

        .features-grid {
            margin-top: 30px;
        }

        .feature-card {
            background-color: var(--postBgColor);
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
                                        echo "<div>" . htmlspecialchars($event['title']) . "<div>";
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
