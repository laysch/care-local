<?php
$currentPage = 'My Profile';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Include database connection
require_once 'inc/database.php';

// Fetch user data from the database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle profile update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';

    try {
        // Check and update username if provided
        if (!empty($_POST['username'])) {
            $updates[] = "username = ?";
            $params[] = sanitizeInput($_POST['username']);
            $types .= 's';
        }

        // Check and update email if provided
        if (!empty($_POST['email'])) {
            $updates[] = "email = ?";
            $params[] = sanitizeInput($_POST['email']);
            $types .= 's';
        }

        // Check and update password if provided
        if (!empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['password_confirm']) {
                throw new Exception("Password and Confirmation do not match!");
            } else {
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $updates[] = "password = ?";
                $params[] = $hashed_password;
                $types .= 's';
            }
        }

        // Update bio and skills if provided
        if (!empty($_POST['bio'])) {
            $updates[] = "bio = ?";
            $params[] = sanitizeInput($_POST['bio']);
            $types .= 's';
        }

        if (!empty($_POST['skills'])) {
            $skills = implode(", ", $_POST['skills']);
            $updates[] = "skills = ?";
            $params[] = $skills;
            $types .= 's';
        }

        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $uploadDir = 'img/avatar/';
            $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                $updates[] = "avatar = ?";
                $params[] = $_FILES['avatar']['name'];
                $types .= 's';
            }
        }

        // If there are any updates, update the database
        if (!empty($updates)) {
            $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $userId;
            $types .= 'i';

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            // Refresh the page to reflect the changes
            header("Refresh:0");
            exit();
        }
    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<html>
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

    <!-- Display Avatar (If Exists) -->
    <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
        <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
    <?php else: ?>
        <img src="img/default-avatar.png" alt="Default User Avatar">
    <?php endif; ?>

    <!-- Avatar Upload Form -->
    <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="avatar" accept="image/*">
        <button type="submit" name="upload">Upload</button>
    </form>

    <!-- Profile Update Form -->
    <form action="profile.php" method="POST">
        <!-- Username Field -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo isset($row['username']) ? htmlspecialchars($row['username']) : ''; ?>" required>

        <!-- Email Field -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($row['email']) ? htmlspecialchars($row['email']) : ''; ?>" required>

        <!-- New Password Fields -->
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" placeholder="***">

        <label for="password_confirm">Confirm Password:</label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="***">

        <!-- Bio Field -->
        <label for="bio">About Me:</label>
        <textarea id="bio" name="bio"><?php echo isset($row['bio']) ? htmlspecialchars($row['bio']) : ''; ?></textarea>

        <!-- Skills Field -->
        <label for="skills">Skills:</label>
        <div class="checkbox-group">
            <?php
            $skillsList = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
            $userSkills = isset($row['skills']) ? explode(", ", $row['skills']) : [];
            foreach ($skillsList as $skill):
                $checked = in_array($skill, $userSkills) ? 'checked' : '';
            ?>
                <label>
                    <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</body>
</html>


