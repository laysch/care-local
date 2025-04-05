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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        h3 {
            color: #5D674C; /* Custom color scheme */
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tag {
            padding: 8px 15px;
            margin: 5px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            cursor: pointer;
            border-radius: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .tag:hover {
            background-color: #ddd;
        }

        .tag.selected {
            background-color: #4CAF50;
            color: white;
        }

        label {
            display: block;
            margin: 8px 0;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #5D674C; /* Custom color */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #4CAF50;
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

