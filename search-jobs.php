<?php include 'sidebar.php'; ?>
<?php   
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Connect to the database
require_once 'inc/database.php';

$order = "DESC";
if (isset($_GET['sort']) && $_GET['sort'] === 'asc') {
    $order = "ASC";
}

// Initialize query to get all jobs by default
$query = "SELECT * FROM jobs WHERE 1=1 ";
$params = [];
$types = "";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$jobsPerPage = 3; // Number of jobs per page
$offset = ($page - 1) * $jobsPerPage;

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

$query .= " ORDER BY created_at $order";

$query .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $jobsPerPage;
$types .= "ii";

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

// Fetch the user's skills 
$userSkills = getUserSkills($conn, $userId);

$totalQuery = "SELECT COUNT(*) AS total FROM jobs WHERE 1=1";
$totalResult = $conn->query($totalQuery);
$totalJobs = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalJobs / $jobsPerPage);

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
            background-color: white;
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
            padding: 25px 20px;
        }

        .hero h1 {
            font-size: 1.8em;
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

        .categories {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
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

        .job-box {
            background-color: #fff;
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
            background-color: #cdd8c4;
        }

        .job-box a {
            text-decoration: none;
            color: #5D674C;
            font-weight: bold;
        }

        .job-box a:hover {
            color: #FCEADE;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            width: auto;
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
            border: 1px solidrgb(103, 161, 137);
            min-width: 150px;
            max-width: 300px;
            width: auto;
            max-height: 300px;
            overflow-y: auto;
            border-radius: 6px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease-in-out;
            padding: 8px;
            white-space: nowrap;
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

        .dropdown-menu input[type="checkbox"],
        .dropdown-menu input[type="radio"] {
            margin-right: 10px;
        }

        .show {
            display: block;
            opacity: 1;
        }
        .btn {
            display: inline-block; /* Keep as inline-block */
            padding: 10px 20px;
            background-color: #efac9a; /* Olive green background for button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            width: fit-content; /* Ensures the button only takes up as much width as its content */
        }
    </style>
</head>
<body>

    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Job Search</h1>
            <p>Find the job that suits your skills and location.</p>
        </section>

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
                        <label><input type="checkbox" name="skills[]" value="PHP"> PHP</label>
                        <label><input type="checkbox" name="skills[]" value="HTML/CSS"> HTML/CSS</label>
                        <label><input type="checkbox" name="skills[]" value="JavaScript"> JavaScript</label>
                        <label><input type="checkbox" name="skills[]" value="MySQL"> MySQL</label>
                        <label><input type="checkbox" name="skills[]" value="Painting"> Painting</label>
                        <label><input type="checkbox" name="skills[]" value="Carpentry"> Carpentry</label>
                        <label><input type="checkbox" name="skills[]" value="Plumbing"> Plumbing</label>
                        <label><input type="checkbox" name="skills[]" value="Electrical Work"> Electrical Work</label>
                        <label><input type="checkbox" name="skills[]" value="CPR Certified"> CPR Certified</label>
                        <label><input type="checkbox" name="skills[]" value="Coaching"> Coaching</label>
                        <label><input type="checkbox" name="skills[]" value="Multitasking"> Multitasking</label>
                        <label><input type="checkbox" name="skills[]" value="Patience"> Patience</label>
                    </div>
                </div>
                <div class="dropdown">
                    <div class="dropdown-toggle" onclick="toggleCounty()">County</div>
                    <div class="dropdown-menu" id="dropdown-county">
                        <label><input type="checkbox" name="county[]" value="Nassau">Nassau</label>
                        <label><input type="checkbox" name="county[]" value="Suffolk">Suffolk</label>
                    </div>                             
                </div>
                <div class="dropdown">
                    <div class="dropdown-toggle" onclick="toggleSort()">Date Created</div>
                    <div class="dropdown-menu" id="dropdown-sort">
                        <label><input type="radio" name="sort" value="desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'desc') ? 'checked' : ''; ?>>Newest First</label>
                        <label><input type="radio" name="sort" value="asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'asc') ? 'checked' : ''; ?>>Oldest First</label>
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
        <div style="text-align: center; margin-top: 20px;">
            <?php
            // Display pagination buttons
            if ($page > 1) {
                echo '<a href="search-jobs.php?page=' . ($page - 1) . '" class="btn">Previous</a>';
            }
            if ($page < $totalPages) {
                echo '<a href="search-jobs.php?page=' . ($page + 1) . '" class="btn">Next</a>';
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="preferences.php" class="btn">Set Job Notifications</a>
            <a href="add-job.php" class="btn">Post a Job</a>
            <a href="index.php" class="btn">Back to Main Menu</a>
        </div>
        
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function toggleDropdown(menuId) {
                // Close any other open dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== menuId) {
                        menu.classList.remove('show');
                    }
                });

                // Toggle the clicked dropdown
                const dropdown = document.getElementById(menuId);
                dropdown.classList.toggle('show');
            }

            // Event listener for clicking outside the dropdowns to close them
            document.addEventListener("click", function (event) {
                const dropdowns = document.querySelectorAll('.dropdown');
                let clickedInside = false;

                dropdowns.forEach(dropdown => {
                    if (dropdown.contains(event.target)) {
                        clickedInside = true;
                    }
                });

                if (!clickedInside) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Attach event listeners to the dropdown toggles
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.addEventListener("click", function (event) {
                    event.stopPropagation(); // Prevent click from closing immediately
                    toggleDropdown(this.nextElementSibling.id);
                });
            });
        });

        function removeFilters() {
            window.location.href = 'search-jobs.php'; // Simply refresh the page without filters
        }
    </script>
</body>
</html>



