<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

require_once 'inc/database.php';

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}
$firstDayOfMonth = strtotime("$year-$month-01");
$totalDays = date('t', $firstDayOfMonth);
$monthName = date('F', $firstDayOfMonth);
$firstDayWeek = date('w', $firstDayOfMonth);

$currentDay = (int)date('d');
$currentMonth = (int)date('m');
$currentYear = (int)date('Y');

$events = [];
$sql = "SELECT date, title FROM events WHERE MONTH(date) = ? AND YEAR(date) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $day = (int)date('d', strtotime($row['date']));
    $events[$day][] = $row['title'];
}

$stmt->close();
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
    <link rel="stylesheet" href="style/calendar.css">
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

        #main-body-wrapper {
            width: 80vw;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
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
            <div id="calendar-container">
                <h1>            
                    <span id="month-display" onclick="toggleMonthDropdown()"><?php echo $monthName; ?></span>
                    <span id="year-display" onclick="toggleYearDropdown()"><?php echo $year; ?></span>
                </h1>            
                <div class="calendar-navigation">
                    <button onclick="changeMonth(<?php echo $month - 1; ?>, <?php echo $year; ?>)">Previous</button>
                    <button onclick="resetToCurrentMonth()">Today</button>
                    <button onclick="changeMonth(<?php echo $month + 1; ?>, <?php echo $year; ?>)">Next</button>
                </div>
                <select id="month-dropdown" class="hidden" onchange="updateMonthYear()">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $month) ? "selected" : "";
                        echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>
                <select id="year-dropdown" class="hidden" onchange="updateMonthYear()">
                    <?php
                    for ($i = date('Y') - 10; $i <= date('Y') + 10; $i++) {
                        $selected = ($i == $year) ? "selected" : "";
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
                <table class="calendar-table">
                    <thread>
                        <tr>
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $dayCounter = 1;
                            for ($i = 0; $i < $firstDayWeek; $i++) {
                                echo "<td></td>";
                            }

                            for ($day = 1; $day <= $totalDays; $day++) {
                                $isToday = ($day == $currentDay && $month == $currentMonth && $year == $currentYear);
                                $highlightClass = $isToday ? "highlight-day" : "";
            
                                echo "<td class='$highlightClass'>";
                                echo "<span class='day-number'>$day</span>";
            
                                if (isset($events[$day])) {
                                    foreach ($events[$day] as $event) {
                                        echo "<div class='event'>$event</div>";
                                    }
                                }
            
                                echo "</td>";
            
                                if (($dayCounter + $firstDayWeek) % 7 == 0) {
                                    echo "</tr><tr>";
                                }
                                $dayCounter++;
                            }
            
                            // Print empty cells for the last week
                            while (($dayCounter + $firstDayWeek - 1) % 7 != 0) {
                                echo "<td></td>";
                                $dayCounter++;
                            }
            
                            echo "</tr>";
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://static.tumblr.com/kmw8hta/1WKpaiuda/tooltipster.main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/js/pixelution.js"></script>
    <script src="scripts/calendar.js"></script>
</body>
</html>
