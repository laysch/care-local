<?php
require_once 'inc/database.php';
session_start();

// Get the logged-in user ID
$userId = $_SESSION['user_id'];

// Get selected skills and counties from the form submission
$skills = isset($_POST['skills']) && !empty($_POST['skills']) ? json_decode($_POST['skills']) : [];
$county = isset($_POST['county']) && !empty($_POST['county']) ? $_POST['county'] : [];

// Create the preferences array to save
$preferences = [
    'skills' => $skills,
    'county' => $county
];

// Convert preferences array to JSON for storage
$preferencesJson = json_encode($preferences);

// Update the user's notification preferences in the database
$query = "UPDATE users SET notify_preferences = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $preferencesJson, $userId);
$stmt->execute();
$stmt->close();

// Redirect to a confirmation page or back to preferences page
header("Location: preferences.php?success=true");
exit;
?>

