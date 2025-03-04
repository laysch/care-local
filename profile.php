<?php
$currentPage = 'My Profile';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

require_once 'inc/database.php';

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

// Initialize bio and skills if not set
if (!isset($_SESSION['bio'])) {
    $_SESSION['bio'] = "<please enter bio here>";
}
if (!isset($_SESSION['skills'])) {
    $_SESSION['skills'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';

    try {
        if (!empty($_POST['username'])) {
            $updates[] = "username = ?";
            $params[] = sanitizeInput($_POST['username']);
            $types .= 's';
        }
        if (!empty($_POST['email'])) {
            $updates[] = "email = ?";
            $params[] = sanitizeInput($_POST['email']);
            $types .= 's';
        }
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

        // Update session bio & skills (not stored in DB)
        $_SESSION['bio'] = sanitizeInput($_POST['bio']);
        $_SESSION['skills'] = isset($_POST['skills']) ? $_POST['skills'] : [];

        if (!empty($updates)) {
            $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $params[] = $userId;
            $types .= 'i';

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            header("Refresh:0");
            exit();
        }
    } catch (Exception $e) {
        error_log("Profile Update Error: " . $e->getMessage());
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
    <div class="profile-container">
        <!-- Profile Avatar -->
        <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
            <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
        <?php else: ?>
            <img src="img/default-avatar.png" alt="Default User Avatar">
        <?php endif; ?>

        <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="avatar" accept="image/*">
            <button type="submit" name="upload">Upload</button>
        </form>

        <!-- Profile Info -->
        <h1><?php echo isset($row['username']) ? htmlspecialchars($row['username']) : 'User'; ?></h1>
        <p>Email: <?php echo isset($row['email']) ? htmlspecialchars($row['email']) : ''; ?></p>

        <!-- Bio Section -->
        <div class="bio">
            <h2>About Me</h2>
            <p><?php echo htmlspecialchars($_SESSION['bio']); ?></p>
        </div>

        <!-- Skills Section -->
        <div class="skills">
            <h2>Skills</h2>
            <ul>
                <?php if (!empty($_SESSION['skills'])): ?>
                    <?php foreach ($_SESSION['skills'] as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No skills selected</p>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Edit Profile Button -->
        <button class="edit-button" onclick="toggleEditProfileForm()">Edit Profile</button>

        <!-- Edit Profile Form -->
        <div id="edit-profile-form" class="edit-profile-form" style="display: none;">
            <form action="profile.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($row['username']) ? htmlspecialchars($row['username']) : ''; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($row['email']) ? htmlspecialchars($row['email']) : ''; ?>" required>

                <label for="bio">About Me:</label>
                <textarea id="bio" name="bio"><?php echo htmlspecialchars($_SESSION['bio']); ?></textarea>

                <label for="skills">Skills:</label>
                <div class="checkbox-group">
                    <?php
                    $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                    foreach ($allSkills as $skill):
                        $checked = in_array($skill, $_SESSION['skills']) ? 'checked' : '';
                    ?>
                        <label>
                            <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>>
                            <?php echo $skill; ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current">

                <label for="password_confirm">Confirm Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm new password">

                <button type="submit" name="update_profile">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function toggleEditProfileForm() {
            const form = document.getElementById('edit-profile-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>
