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

// Fetch the user's skills
$userSkills = getUserSkills($conn, $userId);

// Fetch all jobs and calculate match percentage
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobSkills = explode(',', $row['skills']); // Split job skills into an array
    $commonSkills = array_intersect($userSkills, $jobSkills); // Find common skills
    $matchPercentage = (count($commonSkills) / count($jobSkills)) * 100; // Calculate match percentage

    $jobs[] = [
        'id' => $row['id'],
        'jobtitle' => $row['jobtitle'],
        'location' => $row['location'],
        'skills' => $row['skills'],
        'matchPercentage' => $matchPercentage
    ];
}

// Sort jobs by match percentage in descending order
usort($jobs, function($a, $b) {
    return $b['matchPercentage'] <=> $a['matchPercentage'];
});

// Get top 3 recommended jobs
$recommendedJobs = array_slice($jobs, 0, 3);

// Get the remaining jobs for the regular feed
$regularJobs = array_slice($jobs, 3);
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
        /* Your existing CSS styles */
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

        <!-- Recommended Jobs Section -->
        <div class="job-listings">
            <h2>Recommended Jobs for <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <?php foreach ($recommendedJobs as $job): ?>
                <div class='job-box' onclick='window.location.href="job-details.php?id=<?php echo $job['id']; ?>"'>
                    <a href='job-details.php?id=<?php echo $job['id']; ?>'><?php echo htmlspecialchars($job['jobtitle']); ?></a><br>
                    <?php echo htmlspecialchars($job['location']); ?><br>
                    <span style='font-size: 14px; color: #5D674C;'>Match: <?php echo round($job['matchPercentage'], 2); ?>%</span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Regular Job Feed -->
        <div class="job-listings">
            <h2>Job Feed</h2>
            <?php foreach ($regularJobs as $job): ?>
                <div class='job-box' onclick='window.location.href="job-details.php?id=<?php echo $job['id']; ?>"'>
                    <a href='job-details.php?id=<?php echo $job['id']; ?>'><?php echo htmlspecialchars($job['jobtitle']); ?></a><br>
                    <?php echo htmlspecialchars($job['location']); ?><br>
                    <span style='font-size: 14px; color: #5D674C;'>Match: <?php echo round($job['matchPercentage'], 2); ?>%</span>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="add-job.php" class="btn">Post a Job</a>
            <a href="index.php" class="btn">Back to Main Menu</a>
        </div>
    </div>

    <script>
        function toggleSkills() {
            var dropdown = document.getElementById('dropdown-skills');
            dropdown.classList.toggle('show');
        }

        function toggleCounty() {
            var dropdown = document.getElementById('dropdown-county');
            dropdown.classList.toggle('show');
        }

        function removeFilters() {
            window.location.href = 'search-jobs.php'; // Simply refresh the page without filters
        }
    </script>
</body>
</html>

