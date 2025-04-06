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
require_once 'inc/func.php';  // Use require_once to avoid redeclaration of functions

// Initialize query to get all users by default
$query = "SELECT * FROM users WHERE 1=1 ";
$params = [];
$types = "";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$usersPerPage = 3; // Number of users per page
$offset = ($page - 1) * $usersPerPage;

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

$query .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $usersPerPage;
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

$totalQuery = "SELECT COUNT(*) AS total FROM users WHERE 1=1";
$totalResult = $conn->query($totalQuery);
$totalUsers = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $usersPerPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Search | CareLocal</title>
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
            border: 1px solid rgb(103, 161, 137);
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
            opacity: 1;
            z-index: 1000;
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
            display: inline-block; 
            padding: 10px 20px;
            background-color: #efac9a; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            width: fit-content;
        }
    </style>
</head>
<body>

    <div id="main-body-wrapper">
        <section class="hero">
            <h1>User Search</h1>
            <p>Find users based on their skills.</p>
        </section>

        <div class="filter-section">
            <form action="search-user.php" method="GET">    
                <b>Filter:</b>
                <div class="dropdown">
                    <div class="dropdown-toggle" onclick="toggleSkills()">Skills</div>
                    <div class="dropdown-menu" id="dropdown-skills">
                        <!-- List of skills checkboxes -->
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
                    </div>
                </div>
                <button type="submit" class="btn">Apply Filter</button>
                <button type="button" class="btn" onclick="removeFilters()">Remove Filters</button>
            </form>
        </div>

        <div class="user-list">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="user-box">
                    <h3>Username: <?php echo htmlspecialchars($row['username']); ?></h3>
                    <p>Skills: <?php echo htmlspecialchars($row['skills']); ?></p>
                    <a href="user-details.php?id=<?php echo $row['id']; ?>">View Profile</a>
                </div>
            <?php } ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="search-user.php?page=<?php echo $page - 1; ?>">Previous</a>
            <?php } ?>
            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            <?php if ($page < $totalPages) { ?>
                <a href="search-user.php?page=<?php echo $page + 1; ?>">Next</a>
            <?php } ?>
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
            window.location.href = 'search-user.php'; // Simply refresh the page without filters
        }
    </script>
</body>
</html>
