<?php
require_once 'inc/database.php';
session_start();

// for demo/testing: manually set logged-in user ID
$_SESSION['user_id'] = 1; // replace with real login session
$userId = $_SESSION['user_id'];

$skills = $_POST['skills'] ?? [];
$counties = $_POST['county'] ?? [];

$notify_preferences = json_encode([
    'skills' => $skills,
    'county' => $counties
]);

$query = "UPDATE users SET notify_preferences = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $notify_preferences, $userId);

if ($stmt->execute()) {
    echo "✅ Preferences saved! <a href='preferences.php'>Go back</a>";
} else {
    echo "❌ Error saving preferences.";
}

$stmt->close();
$conn->close();
?>
