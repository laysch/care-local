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

// Initialize form data
$skills = [];
$county = [];
$sort = "desc"; // Default sorting order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['skills'])) {
        $skills = $_POST['skills'];
    }
    if (isset($_POST['county'])) {
        $county = $_POST['county'];
    }
    if (isset($_POST['sort'])) {
        $sort = $_POST['sort'];
    }
}

// Query to get jobs with selected filters
$query = "SELECT * FROM jobs WHERE 1=1";
$params = [];
$types = "";

if (!empty($county)) {
    $countyFilters = [];
    foreach ($county as $c) {
        $countyFilters[] = "county = ?";
        $params[] = $c;
        $types .= "s";
    }
    $query .= " AND (" . implode(" OR ", $countyFilters) . ")";
}

if (!empty($skills)) {
    $skillFilters = [];
    foreach ($skills as $skill) {
        $skillFilters[] = "skills LIKE ?";
        $params[] = "%$skill%";
        $types .= "s";
    }
    $query .= " AND (" . implode(" OR ", $skillFilters) . ")";
}

$query .= " ORDER BY created_at $sort";
$query .= " LIMIT ?, ?";
$params[] = 0; // Offset (pagination)
$params[] = 10; // Limit (number of records per page)
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preferences | CareLocal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
</head>
<body>

<div id="main-body-wrapper">
    <section class="hero">
        <h1>User Preferences</h1>
        <p>Customize your preferences to improve your experience.</p>
    </section>

    <form action="preferences.php" method="POST">
        <div class="filter-section">
            <b>Filter by:</b>
            <!-- Skills Dropdown -->
            <div class="dropdown">
                <div class="dropdown-toggle" onclick="toggleSkills()">Skills</div>
                <div class="dropdown-menu" id="dropdown-skills">
                    <label><input type="checkbox" name="skills[]" value="Communication" <?php echo in_array('Communication', $skills) ? 'checked' : ''; ?>> Communication</label>
                    <label><input type="checkbox" name="skills[]" value="Teamwork" <?php echo in_array('Teamwork', $skills) ? 'checked' : ''; ?>> Teamwork</label>
                    <label><input type="checkbox" name="skills[]" value="Problem-Solving" <?php echo in_array('Problem-Solving', $skills) ? 'checked' : ''; ?>> Problem-Solving</label>
                    <label><input type="checkbox" name="skills[]" value="Leadership" <?php echo in_array('Leadership', $skills) ? 'checked' : ''; ?>> Leadership</label>
                    <label><input type="checkbox" name="skills[]" value="Technical Skills" <?php echo in_array('Technical Skills', $skills) ? 'checked' : ''; ?>> Technical Skills</label>
                    <!-- Add more skills as needed -->
                </div>
            </div>
            <!-- County Dropdown -->
            <div class="dropdown">
                <div class="dropdown-toggle" onclick="toggleCounty()">County</div>
                <div class="dropdown-menu" id="dropdown-county">
                    <label><input type="checkbox" name="county[]" value="Nassau" <?php echo in_array('Nassau', $county) ? 'checked' : ''; ?>> Nassau</label>
                    <label><input type="checkbox" name="county[]" value="Suffolk" <?php echo in_array('Suffolk', $county) ? 'checked' : ''; ?>> Suffolk</label>
                    <!-- Add more counties as needed -->
                </div>
            </div>
            <!-- Sort Dropdown -->
            <div class="dropdown">
                <div class="dropdown-toggle" onclick="toggleSort()">Sort by</div>
                <div class="dropdown-menu" id="dropdown-sort">
                    <label><input type="radio" name="sort" value="desc" <?php echo $sort === 'desc' ? 'checked' : ''; ?>> Newest First</label>
                    <label><input type="radio" name="sort" value="asc" <?php echo $sort === 'asc' ? 'checked' : ''; ?>> Oldest First</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <input type="submit" value="Apply Filters" class="btn">
            <button type="button" class="btn" onclick="removeFilters()">Remove Filters</button>
        </div>
    </form>

    <div class="preferences-list">
        <h2>Your Preferences:</h2>
        <ul>
            <li><strong>Skills:</strong> <?php echo implode(', ', $skills); ?></li>
            <li><strong>County:</strong> <?php echo implode(', ', $county); ?></li>
            <li><strong>Sort Order:</strong> <?php echo ucfirst($sort); ?></li>
        </ul>
    </div>

    <div class="job-listings">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="job-box">
                <a href="job-details.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['jobtitle']); ?></a><br>
                <?php echo htmlspecialchars($row['location']); ?>
            </div>
        <?php } ?>
    </div>

</div>

<script>
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

    function removeFilters() {
        window.location.href = 'preferences.php'; // Simply refresh the page without filters
    }
</script>

</body>
</html>

