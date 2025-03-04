<?php
$currentPage = 'My Profile';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

require_once 'inc/database.php'; // Database connection

// Fetch user data from the database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Sanitize input function
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Profile update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';

    try {
        if (!empty($_POST['name'])) {
            $updates[] = "name = ?";
            $params[] = sanitizeInput($_POST['name']);
            $types .= 's';
        }
        if (!empty($_POST['bio'])) {
            $updates[] = "bio = ?";
            $params[] = sanitizeInput($_POST['bio']);
            $types .= 's';
        }
        if (!empty($_POST['skills'])) {
            $updates[] = "skills = ?";
            $params[] = implode(",", $_POST['skills']); // Skills as comma-separated string
            $types .= 's';
        }

        // Avatar update handling
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
            
            // Check if the uploaded file is an image
            if (getimagesize($_FILES['avatar']['tmp_name'])) {
                move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
                $updates[] = "profile_picture = ?";
                $params[] = $uploadFile; // Updated avatar path
                $types .= 's';
            }
        }

        // If there are updates, execute the query
        if (!empty($updates)) {
            $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $userId;
            $types .= 'i';

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            header("Location: profile.php"); // Redirect after update
            exit();
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Profile Section -->
    <div id="container">
        <div id="sidebar">
            <!-- Sidebar with navigation (optional) -->
            <img src="<?php echo "img/avatar/" . htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
            <p><?php echo htmlspecialchars($row['name']); ?></p>
            <nav>
                <a href="profile.php">My Profile</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Log Out</a>
            </nav>
        </div>

        <div id="main-body-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo "img/avatar/" . htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
                <div>
                    <h1><?php echo htmlspecialchars($row['name']); ?></h1>
                    <p><?php echo htmlspecialchars($row['bio']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="bio">
                <h2>About Me</h2>
                <p><?php echo htmlspecialchars($row['bio']); ?></p>
            </div>

            <!-- Skills Section -->
            <div class="skills">
                <h2>Skills</h2>
                <ul>
                    <?php
                    $skills = explode(",", $row['skills']); // Split skills string into an array
                    foreach ($skills as $skill):
                    ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Edit Profile Form -->
            <div class="edit-profile-form">
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>

                    <label for="bio">Bio:</label>
                    <textarea name="bio" required><?php echo htmlspecialchars($row['bio']); ?></textarea>

                    <label for="skills">Skills (select all that apply):</label>
                    <div class="checkbox-group">
                        <?php
                        $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                        foreach ($allSkills as $skill):
                            $checked = in_array($skill, $skills) ? 'checked' : '';
                        ?>
                            <label>
                                <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Avatar Upload -->
                    <label for="avatar">Upload Avatar:</label>
                    <input type="file" name="avatar" accept="image/*">

                    <button type="submit">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

