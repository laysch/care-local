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
    <title>My Profile | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        /* Styling based on the second provided design */
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #f9eedd;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
            --accentColor: #efac9a;
            --profileBgColor: #fff5e6;
            --cardBgColor: #f4f8f4;
            --buttonColor: #ff9a8b;
            --buttonHoverColor: #ff6f61;
        }

        body {
            background-color: var(--backgroundColor);
            font-family: var(--bodyFontFamily);
            margin: 0;
            padding: 0;
        }

        #container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        #main-body-wrapper {
            flex: 1;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .profile-header h1 {
            font-size: 2em;
            margin: 0;
            color: #333;
        }

        .profile-header p {
            font-size: 1.1em;
            color: var(--bodyTextColor);
            margin-top: 5px;
        }

        .edit-profile-form input,
        .edit-profile-form textarea,
        .edit-profile-form label {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid var(--bordersColor);
        }

        .edit-profile-form button {
            background-color: var(--buttonColor);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
        }

        .edit-profile-form .checkbox-group {
            display: flex;
            flex-wrap: wrap;
        }

        .edit-profile-form .checkbox-group label {
            margin-right: 20px;
            font-size: 1em;
            color: var(--bodyTextColor);
        }
    </style>
</head>
<body>
    <div id="container">
        <?php include('sidebar.php'); ?>
        <!-- Main Body -->
        <div id="main-body-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
                    <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
                <?php else: ?>
                    <img src="img/default-avatar.png" alt="Default User Avatar">
                <?php endif; ?>
                <div>
                    <h1><?php echo htmlspecialchars($row['username']); ?></h1>
                    <p>Location: <?php echo htmlspecialchars($row['location']); ?></p>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div id="edit-profile-form" class="edit-profile-form">
                <form action="profile.php" method="POST">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" placeholder="Username" required>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Email" required>

                    <label for="password">New Password:</label>
                    <input type="password" name="password" placeholder="New Password">

                    <label for="password_confirm">Confirm Password:</label>
                    <input type="password" name="password_confirm" placeholder="Confirm Password">

                    <button type="submit" name="update_profile">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add any required JS for toggling edit form or other functionalities
    </script>
</body>
</html>

