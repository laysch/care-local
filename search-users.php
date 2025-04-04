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
</head>
<body>

    <div id="main-body-wrapper">
        <section class="hero">
            <h1>User Search</h1>
            <p>Find users by their skills.</p>
        </section>

        <div class="filter-section">
            <form action="search-users.php" method="GET">    
                <b>Filter by Skills:</b>
                <div class="dropdown">
                    <div class="dropdown-toggle" onclick="toggleSkills()">Skills</div>
                    <div class="dropdown-menu" id="dropdown-skills">
                        <label><input type="checkbox" name="skills[]" value="PHP"> PHP</label>
                        <label><input type="checkbox" name="skills[]" value="JavaScript"> JavaScript</label>
                        <label><input type="checkbox" name="skills[]" value="HTML/CSS"> HTML/CSS</label>
                        <label><input type="checkbox" name="skills[]" value="MySQL"> MySQL</label>
                    </div>
                </div>
                <input type="submit" value="Apply Filters" class="btn">
            </form>
        </div>

        <div class="user-listings">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='user-box' onclick='window.location.href=\"user-profile.php?id=" . $row['id'] . "\"'>";
                echo "<a href='user-profile.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['username']) . "</a><br>";
                echo "<span style='font-size: 14px; color: #5D674C;'>Skills: " . htmlspecialchars($row['skills']) . "</span>";
                echo "</div>";
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <?php
            if ($page > 1) {
                echo '<a href="search-users.php?page=' . ($page - 1) . '" class="btn">Previous</a>';
            }
            if ($page < $totalPages) {
                echo '<a href="search-users.php?page=' . ($page + 1) . '" class="btn">Next</a>';
            }
            ?>
        </div>
    </div>

    <script>
        function toggleSkills() {
            document.getElementById('dropdown-skills').classList.toggle('show');
        }
    </script>

</body>
</html>
