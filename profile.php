<?php
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
    <title>Profile - CareLocal</title>
    <!-- Include necessary stylesheets -->
</head>
<body>
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($row['avatar'] ? 'img/avatar/' . $row['avatar'] : 'img/default-avatar.png'); ?>" alt="User Avatar">
                <div>
                    <h1><?php echo htmlspecialchars($row['username']); ?></h1>
                    <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div id="edit-profile-form">
                <form method="POST">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" placeholder="Username" required>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Email" required>
                    <input type="password" name="password" placeholder="New Password">
                    <input type="password" name="password_confirm" placeholder="Confirm Password">
                    <button type="submit">Update Profile</button>
                </form>
            </div>

            <!-- Avatar Upload Form -->
            <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="avatar" accept="image/*">
                <button type="submit">Upload Avatar</button>
            </form>
        </div>
    </div>
</body>
</html>

