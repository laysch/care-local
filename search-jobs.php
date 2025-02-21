<?php   
// Start session
session_start();

// Connect to the database
require_once 'inc/database.php';
// Initialize query to get all jobs by default
$query = "SELECT * FROM jobs";

// Check if there are skills selected for filtering
if (isset($_GET['skills']) && !empty($_GET['skills'])) {
    $skills = $_GET['skills'];
    $skillFilter = implode("','", $skills); // Convert array to a comma-separated string for SQL query
    // Modify query to filter by selected skills
    $query = "SELECT * FROM jobs WHERE skills LIKE '%" . $skills[0] . "%'"; // Start with the first skill
    for ($i = 1; $i < count($skills); $i++) {
        $query .= " OR skills LIKE '%" . $skills[$i] . "%'"; // Add more skills as OR conditions
    }
}

// Execute the query
$result = $db->query($query);
if (!$result) {
    die("Query failed: " . $db->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Search</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .job-box {
            background-color: #F3E9B5; /* Light yellow background */
            color: #5D674C; /* Olive green text */
            padding: 15px;
            border-radius: 5px;
            margin: 10px;
            border: 2px solid #D1D79D;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }

        .job-box:hover {
            background-color: #FCEADE; /* Soft peach on hover */
        }

        .job-box a {
            text-decoration: none;
            color: #5D674C; /* Olive green text */
            font-weight: bold;
        }

        .job-box a:hover {
            color: #FCEADE; /* Soft peach on hover for contrast */
        }
    </style>
</head>
<body>
    <fieldset>
        <legend>Job Listings</legend>

        <div class="filter-section">
            <form action="search-jobs.php" method="GET">
                <label for="skills[]">Filter by Skills:</label><br>

                <div class="checkbox-container">
                    <label><input type="checkbox" name="skills[]" value="Communication"> Communication</label>
                    <label><input type="checkbox" name="skills[]" value="Teamwork"> Teamwork</label>
                    <label><input type="checkbox" name="skills[]" value="Problem-Solving"> Problem-Solving</label>
                    <label><input type="checkbox" name="skills[]" value="Leadership"> Leadership</label>
                    <label><input type="checkbox" name="skills[]" value="Technical Skills"> Technical Skills</label>
                    <label><input type="checkbox" name="skills[]" value="Time Management"> Time Management</label>
                </div>

                <br>
                <input type="submit" value="Filter" class="btn">
                <button type="button" class="btn" onclick="removeFilters()">Remove Filters</button>
            </form>
        </div>

        <div class="job-listings">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='job-box' onclick='window.location.href=\"jobdetails.php?id=" . $row['id'] . "\"'>";
                echo "<a href='jobdetails.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['jobtitle']) . "</a><br>";
                echo htmlspecialchars($row['location']);
                echo "</div>";
            }
            ?>
        </div>
    </fieldset>

    <div style="text-align: center; margin-top: 20px;">
        <a href="add-job.php" class="btn">Post a Job</a>
        <a href="index.php" class="btn">Back to Main Menu</a>
    </div>

    <script>
        function removeFilters() {
            window.location.href = "search-jobs.php"; // Reload page to reset filters
        }
    </script>
</body>
</html>