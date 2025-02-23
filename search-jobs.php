
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
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
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
            background-color: #F3E9B5;
            color: #5D674C;
            padding: 15px;
            border-radius: 5px;
            margin: 10px;
            border: 2px solid #D1D79D;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }

        .job-box:hover {
            background-color: #FCEADE;
        }

        .job-box a {
            text-decoration: none;
            color: #5D674C;
            font-weight: bold;
        }

        .job-box a:hover {
            color: #FCEADE;
        }

        /* Dropdown styling */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-btn {
            background-color: #D1D79D;
            color: #5D674C;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            padding: 10px;
            z-index: 1;
        }

        .dropdown-content label {
            display: block;
        }

        .dropdown.active .dropdown-content {
            display: block;
        }

        .btn {
            padding: 8px 12px;
            margin-top: 10px;
            background-color: #5D674C;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <fieldset>
        <legend>Job Listings</legend>

        <div class="filter-section">
            <form action="search-jobs.php" method="GET">
                <div class="dropdown">
                    <button type="button" class="dropdown-btn" onclick="toggleDropdown()">Filter by Skills ▼</button>
                    <div class="dropdown-content" id="dropdownMenu">
                        <label><input type="checkbox" name="skills[]" value="Communication" <?php if (isset($_GET['skills']) && in_array("Communication", $_GET['skills'])) echo "checked"; ?>> Communication</label>
                        <label><input type="checkbox" name="skills[]" value="Teamwork" <?php if (isset($_GET['skills']) && in_array("Teamwork", $_GET['skills'])) echo "checked"; ?>> Teamwork</label>
                        <label><input type="checkbox" name="skills[]" value="Problem-Solving" <?php if (isset($_GET['skills']) && in_array("Problem-Solving", $_GET['skills'])) echo "checked"; ?>> Problem-Solving</label>
                        <label><input type="checkbox" name="skills[]" value="Leadership" <?php if (isset($_GET['skills']) && in_array("Leadership", $_GET['skills'])) echo "checked"; ?>> Leadership</label>
                        <label><input type="checkbox" name="skills[]" value="Technical Skills" <?php if (isset($_GET['skills']) && in_array("Technical Skills", $_GET['skills'])) echo "checked"; ?>> Technical Skills</label>
                        <label><input type="checkbox" name="skills[]" value="Time Management" <?php if (isset($_GET['skills']) && in_array("Time Management", $_GET['skills'])) echo "checked"; ?>> Time Management</label>
                    </div>
                </div>

                <br>
                <input type="submit" value="Apply Filters" class="btn">
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
        function toggleDropdown() {
            document.getElementById("dropdownMenu").classList.toggle("active");
        }

        function removeFilters() {
            window.location.href = "search-jobs.php"; // Reload page to reset filters
        }

        // Close dropdown when clicking outside
        document.addEventListener("click", function(event) {
            var dropdown = document.getElementById("dropdownMenu");
            var button = document.querySelector(".dropdown-btn");
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove("active");
            }
        });
    </script>
</body>
</html>
