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

// Handle profile updates
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

        // Handle location update
        if (isset($_POST['location'])) {
            $location = sanitizeInput($_POST['location']);
            if ($location == 'Nassau' || $location == 'Suffolk') {
                $updates[] = "location = ?";
                $params[] = $location;
                $types .= 's';
            } else {
                $updates[] = "location = ?";
                $params[] = "Not Specified";
                $types .= 's';
            }
        }

        // Handle bio update
        if (!empty($_POST['bio'])) {
            $bio = sanitizeInput($_POST['bio']);
            $updates[] = "bio = ?";
            $params[] = $bio;
            $types .= 's';
        }

        // Handle skills update
        if (isset($_POST['skills'])) {
            $skills = implode(", ", $_POST['skills']);
            $updates[] = "skills = ?";
            $params[] = $skills;
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

        #sidebar {
            width: 250px;
            margin-right: 20px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #sidebar img {
            width: 100%;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        #sidebar .title-text {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        #sidebar nav a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #sidebar nav a:hover {
            background-color: var(--accentColor);
            color: white;
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

        .bio, .skills {
            background-color: var(--cardBgColor);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .bio h2, .skills h2 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
        }

        .bio p, .skills ul li {
            font-size: 1em;
            color: var(--bodyTextColor);
            line-height: 1.6;
        }

        .skills ul {
            list-style: none;
            padding: 0;
        }

        .skills ul li {
            font-size: 1.1em;
            color: var(--bodyTextColor);
            margin-bottom: 10px;
        }

        .edit-button {
            background-color: var(--buttonColor);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .edit-button:hover {
            background-color: var(--buttonHoverColor);
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
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
                <div>
                    <h1><?php echo htmlspecialchars($row['username']); ?></h1>
                    <p>Location: <?php echo htmlspecialchars($row['location']) ?: 'Not Specified'; ?></p>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="bio">
                <h2>About Me</h2>
                <p><?php echo htmlspecialchars($row['bio']) ?: "<please enter bio here>"; ?></p>
            </div>

            <!-- Skills Section -->
            <div class="skills">
                <h2>Skills</h2>
                <ul>
                    <?php 
                    $skills = explode(", ", $row['skills']);
                    if (empty($skills[0])) { 
                        // No skills, set default ones
                        $skills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                    }
                    foreach ($skills as $skill): ?>
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
                <form method="POST">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" placeholder="Username" required>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Email" required>
                    
                    <label for="location">Location</label>
                    <select name="location">
                        <option value="Nassau" <?php echo $row['location'] == 'Nassau' ? 'selected' : ''; ?>>Nassau</option>
                        <option value="Suffolk" <?php echo $row['location'] == 'Suffolk' ? 'selected' : ''; ?>>Suffolk</option>
                        <option value="Not Specified" <?php echo empty($row['location']) || $row['location'] == 'Not Specified' ? 'selected' : ''; ?>>Not Specified</option>
                    </select>

                    <label for="bio">About Me</label>
                    <textarea name="bio"><?php echo htmlspecialchars($row['bio']); ?></textarea>

                    <label for="skills">Skills (check all that apply):</label>
                    <div class="checkbox-group">
                        <?php
                        $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                        foreach ($allSkills as $skill): 
                            $checked = in_array($skill, $skills) ? 'checked' : '';
                        ?>
                            <label><input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?></label>
                        <?php endforeach; ?>
                    </div>

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

