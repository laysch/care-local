<?php
// Sample user data for profile page
$user = [
    "first_name" => "John",
    "last_name" => "Doe",
    "bio" => "<please enter a bio>.",
    "location" => "<please enter a location>",
    "skills" => ["Communication", "Teamwork", "Problem-Solving"], 
    "profile_picture" => "https://example.com/profile.jpg"
];

// Check if the form is submitted to update the user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the first and last name from the POST data
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    
    // Combine first and last names to form the full name
    $fullName = $firstName . ' ' . $lastName;

    // Update user profile based on submitted form data
    $user['first_name'] = $firstName;
    $user['last_name'] = $lastName;
    $user['full_name'] = $fullName; // Store the full name
    $user['bio'] = $_POST['bio'];
    $user['skills'] = isset($_POST['skills']) ? $_POST['skills'] : [];


    // Handle the file upload for the avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);

        // Check if file is an image
        if (getimagesize($_FILES['avatar']['tmp_name'])) {
            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
            $user['profile_picture'] = $uploadFile; // Update the avatar path
        }
    }

    // Assuming $conn is your database connection
    // You would update the database with the new first name, last name, and full name:
    $query = "UPDATE users SET first_name = ?, last_name = ?, name = ?, bio = ?, skills = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $firstName, $lastName, $fullName, $_POST['bio'], implode(',', $_POST['skills']), $userId);
    $stmt->execute();
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
                    <?php foreach ($user['skills'] as $skill): ?>
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
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" placeholder="First Name" required>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" placeholder="Last Name" required>
                    <textarea name="bio" placeholder="About Me" required><?php echo htmlspecialchars($user['bio']); ?></textarea>

                    <label for="skills">Skills (check all that apply):</label>
                    <div class="checkbox-group">
                        <?php
                        $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
                        foreach ($allSkills as $skill):
                            $checked = in_array($skill, $user['skills']) ? 'checked' : '';
                        ?>
                            <label>
                                <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Check if avatar exists -->
                    <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
                        <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
                    <?php else: ?>
                        <img src="img/default-avatar.png" alt="Default User Avatar">
                    <?php endif; ?>

                    <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="avatar" accept="image/*">
                        <button type="submit" name="submit">Upload Avatar</button>
                    </form>

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

