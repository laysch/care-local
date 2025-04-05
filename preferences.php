<?php
require_once 'inc/database.php';
session_start();

// For demo/testing: manually set logged-in user ID
$_SESSION['user_id'] = 1; // replace with real login session if needed

$userId = $_SESSION['user_id'];

// Skills and counties options (you can fetch these dynamically from the database or hardcode)
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
    <title>Notification Preferences | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css" rel="stylesheet">
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
        }

        body {
            background-color: #fff;
            font-family: 'Share Tech Mono', monospace;
            color: #5D674C;
        }

        #main-body-wrapper {
            max-width: 1000px;
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

        .preferences-form {
            margin-top: 20px;
            text-align: center;
        }

        .preferences-form h2 {
            font-size: 2em;
            color: #5D674C;
            margin-bottom: 20px;
        }

        .preferences-form label {
            font-size: 1.1em;
            color: #5D674C;
            display: block;
            margin-bottom: 10px;
        }

        .preferences-form select, .preferences-form input[type="checkbox"] {
            margin: 10px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
        }

        .btn, .btn:link, .btn:visited {
            padding: 10px 20px;
            background-color: #efac9a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #efac9a;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Set Your Notification Preferences</h1>
            <p>Choose the skills you're interested in and your preferred county for notifications.</p>
        </section>

        <div class="preferences-form">
            <h2>Update Preferences</h2>

            <form action="save-preferences.php" method="POST">
                <div>
                    <label for="skills">Select Skills:</label>
                    <?php while ($skill = $skillsResult->fetch_assoc()): ?>
                        <input type="checkbox" name="skills[]" value="<?= $skill['skill_name'] ?>" <?= in_array($skill['skill_name'], explode(',', $preferences['skills'] ?? '')) ? 'checked' : '' ?>> <?= $skill['skill_name'] ?>
                    <?php endwhile; ?>
                </div>

                <div>
                    <label for="county">Select County:</label>
                    <select name="county" id="county">
                        <?php while ($county = $countiesResult->fetch_assoc()): ?>
                            <option value="<?= $county['county_name'] ?>" <?= $county['county_name'] == $preferences['county'] ? 'selected' : '' ?>><?= $county['county_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="button-container">
                    <button type="submit" class="btn">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
