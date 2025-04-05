<?php
require_once 'inc/database.php';
session_start();

// for demo/testing: manually set logged-in user ID
$_SESSION['user_id'] = 1; // replace with real login session if needed

$userId = $_SESSION['user_id'];
$skillsOptions = ['Gardening', 'Tutoring', 'Tech Support', 'Cooking', 'Babysitting'];
$counties = ['Nassau', 'Suffolk'];

$notify_preferences = [
    'skills' => [],
    'county' => []
];

// load existing preferences
$query = "SELECT notify_preferences FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($prefs);
if ($stmt->fetch() && $prefs) {
    $notify_preferences = json_decode($prefs, true);
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notification Preferences</title>
</head>
<body>
    <h2>Choose What You Want to Be Notified About</h2>
    <form method="POST" action="save_preferences.php">
        <h3>Skills:</h3>
        <?php foreach ($skillsOptions as $skill): ?>
            <label>
                <input type="checkbox" name="skills[]" value="<?= $skill ?>"
                    <?= in_array($skill, $notify_preferences['skills']) ? 'checked' : '' ?>>
                <?= $skill ?>
            </label><br>
        <?php endforeach; ?>

        <h3>Counties:</h3>
        <?php foreach ($counties as $county): ?>
            <label>
                <input type="checkbox" name="county[]" value="<?= $county ?>"
                    <?= in_array($county, $notify_preferences['county']) ? 'checked' : '' ?>>
                <?= $county ?>
            </label><br>
        <?php endforeach; ?>

        <br>
        <button type="submit">Save Preferences</button>
    </form>
</body>
</html>
