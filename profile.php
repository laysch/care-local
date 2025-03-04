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
        if (!empty($_POST['name'])) {
            $updates[] = "name = ?";
            $params[] = sanitizeInput($_POST['name']);
            $types .= 's';
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
    

  <!-- Check if avatar exists -->
    <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
        <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
    <?php else: ?>
        <img src="img/default-avatar.png" alt="Default User Avatar">
    <?php endif; ?>

    <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="avatar" accept="image/*">
        <button type="submit" name="upload">Upload</button>
    </form>

    // Optionally, update the database with the new information (e.g., name, bio, skills, avatar)
    $skills = implode(", ", $user['skills']);
    $update_sql = "UPDATE users SET name = '{$user['name']}', bio = '{$user['bio']}', skills = '$skills', profile_picture = '{$user['profile_picture']}' WHERE id = $user_id";
    $conn->query($update_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <div>
                    <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                    <p>Location: <?php echo htmlspecialchars($user['location']); ?></p>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="bio">
                <h2>About Me</h2>
                <p><?php echo htmlspecialchars($user['bio']); ?></p>
            </div>

            <!-- Skills Section -->
            <div class="skills">
                <h2>Skills</h2>
                <ul>
                    <?php foreach (explode(", ", $user['skills']) as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Edit Profile Button -->
            <div class="edit-button-wrapper">
                <button class="edit-button" onclick="toggleEditProfileForm()">Edit Profile</button>
            </div>

            <!-- Edit Profile Form (Initially hidden) -->
            <div id="edit-profile-form" class="edit-profile-form" style="display: none;">
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Name" required>
                    <textarea name="bio" placeholder="About Me" required><?php echo htmlspecialchars($user['bio']); ?></textarea>

                    <label for="skills">Skills (check all that apply):</label>
                    <div class="checkbox-group">
                        <?php
                        $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                        foreach ($allSkills as $skill):
                            $checked = in_array($skill, explode(", ", $user['skills'])) ? 'checked' : '';
                        ?>
                            <label>
                                <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Check if avatar exists -->
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="User Avatar">

                    <input type="file" name="avatar" accept="image/*">
                    <button type="submit">Save Changes</button>
                </form>
            </div>
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


