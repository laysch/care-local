<?php
$currentPage = "Calendar";

$year = date('Y');
$monthNum = date('n');
$monthName = date('F');
$monthDays = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="script.js" defer></script>
    <style>
        table {
            margin: 0px;
            border-collapse: collapse;
        }
        tr {
            line-height: 100px;
        }
        th {
            border-bottom:2px dashed;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="hero-section">
        <h1><?php echo $monthName . " " . $year; ?></h1>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <table width="100%">
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
                $firstDay = date('w', strtotime("$year-$monthNum-01"));
                $totalCells = ceil(($firstDay + $monthDays) / 7) * 7;
                $counter = 1;
                echo "<tr>";
                for ($i = 0; $i < $totalCells; $i++) {
                    if ($i < $firstDay || $counter > $monthDays) {
                        echo "<td></td>";
                    } else {
                        echo "<td>" . $counter . "</td>";
                        $counter++;
                    }
                    if (($i + 1) % 7 ==0) {
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
</body>
</html>
