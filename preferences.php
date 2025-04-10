<?php   
require_once 'inc/session.php';
require_once 'inc/database.php';
include_once 'inc/func.php';

// Skills and counties options 
$skillsOptions = ['Communication', 'Teamwork', 'Problem-Solving', 'Leadership', 'Technical Skills', 'Time Management', 'Painting', 'Carpentry', 'Plumbing', 'Electrical Work', 'PHP', 'HTML/CSS', 'JavaScript', 'MySQL', 'CPR Certified', 'Coaching', 'Multitasking', 'Patience'];
$counties = ['Nassau', 'Suffolk'];

// Initialize notify_preferences array
$notify_preferences = [
    'skills' => [],
    'county' => []
];

// Load existing preferences from the database
$query = "SELECT notify_preferences FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($prefs);
if ($stmt->fetch() && $prefs) {
    $notify_preferences = json_decode($prefs, true); // Decode the preferences stored in JSON format
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Preferences | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        /* Styles adapted for preferences page */
        body {
            background-color: #ffffff;
            background-image: url('https://example.com/background.jpg');
            background-attachment: fixed;
            background-repeat: repeat;
            font-family: 'Share Tech Mono', monospace;
        }

        #main-body-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hero {
            text-align: center;
            padding: 50px 20px;
        }

        .hero h1 {
            font-size: 2.5em;
            color: #5D674C;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            color: #839c99;
            margin-bottom: 30px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #839c99;
            border-radius: 5px;
        }

        .category-btn {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .category-btn.active {
            background-color: #efac9a;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tag {
            background-color: #D1D79D;
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tag.selected {
            background-color: #5D674C;
        }

        .cta-button {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .cta-button:hover {
            background-color: #efac9a;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Select Preferences</h1>
            <p>Choose your preferred skills and location to receive relevant notifications.</p>
        </section>

        <div class="form-container">
            <form action="inc/updateNotificationPreference.php" method="POST">
                <!-- Modify the County Selection Section to be Clickable Like Skills -->
<label for="county">Select County:</label>
<div class="tags-container">
    <?php 
    $available_counties = ['Not Specified', 'Nassau', 'Suffolk'];
    $selected_counties = isset($notify_preferences['county']) ? [$notify_preferences['county']] : [];
    foreach ($available_counties as $county) {
        $isSelected = in_array($county, $selected_counties) ? 'selected' : '';
        echo "<button type='button' class='tag $isSelected' onclick='toggleCountySelection(this, \"$county\")'>$county</button>";
    }
    ?>
</div>
<input type="hidden" name="county" id="county-input">

<!-- Modify the Skills Selection Section -->
<label for="skills">Select Skills:</label>
<div class="tags-container">
    <?php 
    $available_skills = ['Communication', 'Teamwork', 'Problem-Solving', 'Leadership', 'Technical Skills', 'Time Management', 'Painting', 'Carpentry', 'Plumbing', 'Electrical Work', 'PHP', 'HTML/CSS', 'JavaScript', 'MySQL', 'CPR Certified', 'Coaching', 'Multitasking', 'Patience'];
    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : $notify_preferences['skills'];
    foreach ($available_skills as $skill) {
        $isSelected = in_array($skill, $selected_skills) ? 'selected' : '';
        echo "<button type='button' class='tag $isSelected' onclick='toggleSkillSelection(this, \"$skill\")'>$skill</button>";
    }
    ?>
</div>
<input type="hidden" name="skills" id="skills-input">

<!-- Save Button -->
<button type="submit" class="cta-button">Save Preferences</button>

<script>
    // JavaScript to handle county selection
    function toggleCountySelection(button, county) {
        button.classList.toggle('selected');
        let countyInput = document.getElementById('county-input');
        let container = button.closest('.tags-container'); // restrict to county container
        let selectedCounties = Array.from(container.querySelectorAll('button.selected')).map(el => el.textContent);
        countyInput.value = selectedCounties.join(',');
    }

    // JavaScript to handle skill selection (same as before)
    function toggleSkillSelection(button, skill) {
        button.classList.toggle('selected');
        let skillsInput = document.getElementById('skills-input');
        let container = button.closest('.tags-container'); // restrict to skills container
        let selectedSkills = Array.from(container.querySelectorAll('button.selected')).map(el => el.textContent);
        skillsInput.value = selectedSkills.join(',');
    }
</script>

</body>
</html>
