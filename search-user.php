<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include 'sidebar.php'; ?>
<?php   
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

// Connect to the database
require_once 'inc/database.php';

$order = "DESC";
if (isset($_GET['sort']) && $_GET['sort'] === 'asc') {
    $order = "ASC";
}

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

$query .= " ORDER BY created_at $order";

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

// Get total number of users
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
</head>
<body>

    <div id="main-body-wrapper">
        <section class="hero">
            <h1>User Search</h1>
            <p>Find users with the skills you need.</p>
        </section>

        <div class="filter-section">
            <form action="search-user.php" method="GET">    
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
                <br>
                <input type="submit" value="Apply Filters" class="btn">
                <button type="button" class="btn" onclick="removeFilters()">Remove Filters</button>
            </form>
        </div>

        <div class="user-listings">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='job-box'>";
                echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
                echo "Skills: " . htmlspecialchars($row['skills']) . "<br>";
                echo "</div>";
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <?php
            if ($page > 1) {
                echo '<a href="search-user.php?page=' . ($page - 1) . '" class="btn">Previous</a>';
            }
            if ($page < $totalPages) {
                echo '<a href="search-user.php?page=' . ($page + 1) . '" class="btn">Next</a>';
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn">Back to Main Menu</a>
        </div>
        
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function toggleDropdown(menuId) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== menuId) {
                        menu.classList.remove('show');
                    }
                });

                const dropdown = document.getElementById(menuId);
                dropdown.classList.toggle('show');
            }

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

            document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                toggle.addEventListener("click", function (event) {
                    event.stopPropagation();
                    toggleDropdown(this.nextElementSibling.id);
                });
            });
        });

        function removeFilters() {
            window.location.href = 'search-user.php';
        }
    </script>
</body>
</html>
