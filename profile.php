<?php
// Sample user data for profile page
$user = [
    "first_name" => "John",  // Default name, can be updated from POST
    "last_name" => "Doe",    // Default name, can be updated from POST
    "bio" => "A passionate developer with a love for creating innovative solutions. I enjoy working on web and mobile applications.",
    "location" => "New York, USA",
    "skills" => ["Communication", "Teamwork", "Problem-Solving"], // Example skills
    "profile_picture" => "https://example.com/profile.jpg"
];

// Check if the form is submitted to update the user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure 'first_name' and 'last_name' are set before accessing them
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    
    // Combine first and last names to form the full name
    $fullName = $firstName . ' ' . $lastName;

    // Update user profile based on submitted form data
    $user['first_name'] = $firstName;
    $user['last_name'] = $lastName;
    $user['full_name'] = $fullName; // Store the full name
    $user['bio'] = $_POST['bio'] ?? '';  // Safe fallback for bio
    $user['skills'] = isset($_POST['skills']) ? $_POST['skills'] : []; // Safe fallback for skills

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
    // Make sure you have $userId defined (e.g., from session or login)
    $query = "UPDATE users SET first_name = ?, last_name = ?, full_name = ?, bio = ?, skills = ? WHERE user_id = ?";
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
    <!-- Your stylesheets here -->
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
                    <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>  <!-- Display full name -->
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
                    <?php if (isset($user['profile_picture']) && !empty($user['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="User Avatar">
                    <?php else: ?>
                        <img src="img/default-avatar.png" alt="Default User Avatar">
                    <?php endif; ?>

                    <input type="file" name="avatar" accept="image/*">
                    <button type="submit" name="upload">Save Changes</button>
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


