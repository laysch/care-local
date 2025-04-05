<?php
require_once 'inc/database.php';
include_once 'inc/func.php';

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Preferences</title>
    <style>
        .tags-container {
            display: flex;
            flex-wrap: wrap;
        }
        .tag {
            padding: 5px 10px;
            margin: 5px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            cursor: pointer;
        }
        .tag.selected {
            background-color: #4CAF50;
            color: white;
        }
    </style>
    <script>
        function toggleSkillSelection(button, skill) {
            button.classList.toggle('selected');
            
            let skills = [];
            document.querySelectorAll('.tag.selected').forEach(selectedButton => {
                skills.push(selectedButton.textContent);
            });
            
            // Set the hidden input field's value as a JSON string of selected skills
            document.getElementById('skills_input').value = JSON.stringify(skills);
        }
    </script>
</head>
<body>
    <h2>Choose What You Want to Be Notified About</h2>

    <!-- Form for updating preferences -->
    <form method="POST" action="save_preferences.php">
        <h3>Skills:</h3>
        <div class="tags-container">
            <?php foreach ($skillsOptions as $skill): ?>
                <button type="button" class="tag <?= in_array($skill, $notify_preferences['skills']) ? 'selected' : '' ?>" onclick="toggleSkillSelection(this, '<?= $skill ?>')"><?= $skill ?></button>
            <?php endforeach; ?>
        </div>
        <!-- Hidden field to store selected skills as JSON -->
        <input type="hidden" name="skills" id="skills_input" value="<?= json_encode($notify_preferences['skills']) ?>">

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

