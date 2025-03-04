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
        // Update username if provided
        if (!empty($_POST['username'])) {
            $updates[] = "username = ?";
            $params[] = sanitizeInput($_POST['username']);
            $types .= 's';
        }

        // Update email if provided
        if (!empty($_POST['email'])) {
            $updates[] = "email = ?";
            $params[] = sanitizeInput($_POST['email']);
            $types .= 's';
        }

        // Update password if provided
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

        // Update name, bio, location, and skills
        $name = sanitizeInput($_POST['name']);
        $bio = sanitizeInput($_POST['bio']);
        $location = sanitizeInput($_POST['location']);
        $skills = isset($_POST['skills']) ? $_POST['skills'] : [];

        $updates[] = "name = ?";
        $params[] = $name;
        $types .= 's';

        $updates[] = "bio = ?";
        $params[] = $bio;
        $types .= 's';

        $updates[] = "location = ?";
        $params[] = $location;
        $types .= 's';

        $updates[] = "skills = ?";
        $params[] = implode(', ', $skills);
        $types .= 's';

        // Handle avatar upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);

            // Check if file is an image
            if (getimagesize($_FILES['avatar']['tmp_name'])) {
                move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
                $updates[] = "profile_picture = ?";
                $params[] = $uploadFile;
                $types .= 's';
            } else {
                throw new Exception("Uploaded file is not a valid image.");
            }
        }

        // Update the database
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
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        /* Your CSS styles here */
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
                <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
                <div>
                    <h1><?php echo htmlspecialchars($row['name']); ?></h1>
                    <p>Location: <?php echo htmlspecialchars($row['location'] ? $row['location'] : "Not specified"); ?></p>
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
                    <?php foreach (explode(', ', $row['skills']) as $skill): ?>
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
                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" placeholder="Name" required>
                    <textarea name="bio" placeholder="About Me" required><?php echo htmlspecialchars($row['bio']); ?></textarea>

                    <!-- Location Dropdown -->
                    <select name="location">
                        <option value="">Select a location</option>
                        <option value="Nassau" <?php echo $row['location'] === 'Nassau' ? 'selected' : ''; ?>>Nassau</option>
                        <option value="Suffolk" <?php echo $row['location'] === 'Suffolk' ? 'selected' : ''; ?>>Suffolk</option>
                        <option value="Not Specified" <?php echo $row['location'] === 'Not Specified' ? 'selected' : ''; ?>>Not Specified</option>
                    </select>

                    <label for="skills">Skills (check all that apply):</label>
                    <div class="checkbox-group">
                        <?php
                        $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                        foreach ($allSkills as $skill):
                            $checked = in_array($skill, explode(', ', $row['skills'])) ? 'checked' : '';
                        ?>
                            <label>
                                <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Avatar Upload -->
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
