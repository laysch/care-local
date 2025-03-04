<?php include 'navbar.php'; ?> 
<?php   
$currentPage = 'Search Jobs';
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

// Connect to the database
require_once 'inc/database.php';

// Initialize query to get all jobs by default
$query = "SELECT * FROM jobs WHERE 1=1";
$params = [];
$types = "";

if (isset($_GET['county']) && is_array($_GET['county']) && !empty($_GET['county'])) {
    $countyFilters = [];
    foreach ($_GET['county'] as $county) {
        $countyFilters[] = "county = ?";
        $params[] = $county;
        $types .= "s";
    }
    $query .= " AND (" . implode(" OR ", $countyFilters) . ")";
}

// Check if there are skills selected for filtering
if (isset($_GET['skills']) && is_array($_GET['skills']) && !empty($_GET['skills'])) {
    $skillFilters = [];
    foreach ($_GET['skills'] as $skill) {
        $skillFilters[] = "skills LIKE ?";
        $params[] = "%".$skill."%";
        $types .= "s";
    }
    $query .= " AND (" . implode(" OR ", $skillFilters) . ")";
}

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Statement preparation failed: " . $conn->error);
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Query result retrieval failed: " . $stmt->error);
}

// Fetch the user's skills (assuming they are stored in the session)
$userSkills = isset($_SESSION['user_skills']) ? $_SESSION['user_skills'] : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Search | CareLocal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F7F7F7;
            color: #5D674C;
            margin: 0;
        }
        .features-grid {
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .feature-card {
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 80%;
            padding: 20px;
        }
        fieldset {
            border: none;
        }
        legend {
            font-size: 24px;
            color: #5D674C;
            font-weight: bold;
        }
        .filter-section {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .filter-section form {
            width: 100%;
        }
        .dropdown {
            position: relative;
            width: 100%;
        }
        .dropdown-toggle {
            padding: 12px;
            background-color: #5D674C;
            color: white;
            border: 1px solid #D1D79D;
            border-radius: 6px;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: bold;
        }
        .dropdown-toggle:hover {
            background-color: #8C7B5E;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #FCEADE;
            border: 1px solid #D1D79D;
            width: 100%;
            max-height: 220px;
            overflow-y: auto;
            border-radius: 6px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 8px;
        }
        .dropdown-menu label {
            display: flex;
            align-items: center;
            padding: 8px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .dropdown-menu label:hover {
            background-color: #F3E9B5;
        }
        .dropdown-menu input[type="checkbox"] {
            margin-right: 10px;
        }
        .job-box {
            background-color: #F3E9B5;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 2px solid #D1D79D;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
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
        .btn {
            background-color: #5D674C;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin: 10px;
        }
        .btn:hover {
            background-color: #8C7B5E;
        }
    </style>
</head>
<body>
    
    <div class="features-grid">
        <div class="feature-card">
            <fieldset>
                <legend>Job Listings</legend>

                <div class="filter-section">
                    <form action="search-jobs.php" method="GET">    
                        <b>Filter:</b>
                        <div class="dropdown">
                            <div class="dropdown-toggle" onclick="toggleSkills()">Skills</div>
                            <div class="dropdown-menu" id="dropdown-skills">
                                <label><input type="checkbox" name="skills[]" value="Communication"> Communication</label>
                                <label><input type="checkbox" name="skills[]" value="Teamwork"> Teamwork</label>
                                <label><input type="checkbox" name="skills[]" value="Problem-Solving"> Problem-Solving</label>
                                <label><input type="checkbox" name="skills[]" value="Leadership"> Leadership</label>
                                <label><input type="checkbox" name="skills[]" value="Technical Skills"> Technical Skills</label>
                                <label><input type="checkbox" name="skills[]" value="Time Management"> Time Management</label>
                            </div>
                        </div>
                        <div class="dropdown">
                            <div class="dropdown-toggle" onclick="toggleCounty()">County</div>
                            <div class="dropdown-menu" id="dropdown-county">
                                <label><input type="checkbox" name="county[]" value="Nassau">Nassau</label>
                                <label><input type="checkbox" name="county[]" value="Suffolk">Suffolk</label>
                            </div>                             
                        </div>

                        <br>
                        <input type="submit" value="Filter" class="btn">
                        <button type="button" class="btn" onclick="removeFilters()">Remove Filters</button>
                    </form>
                </div>

                <div class="job-listings">
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        $jobSkills = explode(',', $row['skills']);
                        $commonSkills = array_intersect($userSkills, $jobSkills);
                        $matchPercentage = (count($commonSkills) / count($jobSkills)) * 100;

                        echo "<div class='job-box' onclick='window.location.href=\"job-details.php?id=" . $row['id'] . "\"'>";
                        echo "<a href='job-details.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['jobtitle']) . "</a><br>";
                        echo htmlspecialchars($row['location']) . "<br>";
                        echo "<span style='font-size: 14px; color: #5D674C;'>Match: " . round($matchPercentage, 2) . "%</span>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="add-job.php" class="btn">Post a Job</a>
        <a href="index.php" class="btn">Back to Main Menu</a>
    </div>

    <script>
        function toggleSkills() {
            var dropdown = document.getElementById("dropdown-skills");
            dropdown.classList.toggle("show");
        }
        function toggleCounty() {
            var dropdown = document.getElementById("dropdown-county");
            dropdown.classList.toggle("show");
        }

        function removeFilters() {
            window.location.href = "search-jobs.php"; 
        }

        window.addEventListener("click", function (event) {
            if (!event.target.closest(".dropdown")) {
                var dropdown = document.getElementById("dropdown-skills");
                if (dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
                var dropdown = document.getElementById("dropdown-county");
                if (dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            }
        });
    </script>
</body>
</html>

