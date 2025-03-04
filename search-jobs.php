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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Jobs | CareLocal</title>
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

        .dropdown {
            position: relative;
            display: inline-block;
            width: 100px; 
        }

        .dropdown-toggle {
            border: 1px solid #D1D79D;
            padding: 12px;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .dropdown-toggle:hover {
            background-color: #e2e6ea;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #D1D79D;
            border: 1px solid rgb(103, 161, 137);
            width: 100%;
            max-height: 220px;
            overflow-y: auto;
            border-radius: 6px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease-in-out;
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
            background-color: #f3e9b5;
        }

        .dropdown-menu input[type="checkbox"] {
            margin-right: 10px;
        }

        .show {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Search Jobs</h1>
            <p>Find your next opportunity here</p>
        </section>

        <!-- Filter Section -->
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
                    <div class="dropdown-toggle" onclick="toggleCounty()">County:</div>
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

        <!-- Job Feed -->
        <section class="job-feed">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                $jobSkills = explode(',', $row['skills']); 
                $commonSkills = array_intersect($userSkills, $jobSkills);
                $matchPercentage = (count($commonSkills) / count($jobSkills)) * 100; 
                ?>
                <div class="job-box" onclick="window.location.href='job-details.php?id=<?php echo $row['id']; ?>'">
                    <a href="job-details.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['jobtitle']); ?></a><br>
                    <?php echo htmlspecialchars($row['location']); ?><br>
                    <span style="font-size: 14px; color: #5D674C;">Match: <?php echo round($matchPercentage, 2); ?>%</span>
                </div>
            <?php endwhile; ?>
        </section>

        <div style="text-align: center; margin-top: 20px;">
            <a href="add-job.php" class="btn">Post a Job</a>
            <a href="index.php" class="btn">Back to Main Menu</a>
        </div>
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


