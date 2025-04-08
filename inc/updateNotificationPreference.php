<?php
require_once 'database.php';
require_once 'session.php';

$skills = explode(',', $_POST['skills']);
$counties = explode(',', $_POST['county']);

$notify_preferences = json_encode([
    'skills' => $skills,
    'county' => $counties
]);

$query = "UPDATE users SET notify_preferences = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $notify_preferences, $userId);
$stmt->execute();

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'preferences.php'));
exit;
?>