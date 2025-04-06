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
        /* Add your custom styles here */
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
            </form>
        </div>

        <div class="user-list">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="user-box">
                    <h3><?php echo htmlspecialchars($row['username']); ?></h3>
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

</body>
</html>
