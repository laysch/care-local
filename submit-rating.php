<?php
session_start();
require_once 'inc/database.php';

$currentUserId = $_POST['rater_user_id'] ?? null; 
$ratedUserId = $_POST['rated_user_id'] ?? null;
$rating = $_POST['rating'] ?? null;

if (!$currentUserId || !$ratedUserId || !$rating) {
    die("Invalid request.");
}

// Optional: Validate rating is between 1 and 5
if ($rating < 1 || $rating > 5) {
    die("Rating must be between 1 and 5.");
}

// Check if the logged-in user has already rated the other user
$checkQuery = "SELECT id FROM ratings WHERE rater_user_id = ? AND rated_user_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $currentUserId, $ratedUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Rating exists – update it
    $updateQuery = "UPDATE ratings SET rating = ? WHERE rater_user_id = ? AND rated_user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iii", $rating, $currentUserId, $ratedUserId);
    $stmt->execute();
} else {
    // New rating
    $insertQuery = "INSERT INTO ratings (rater_user_id, rated_user_id, rating, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iii", $currentUserId, $ratedUserId, $rating);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: profile-details.php?id=" . $ratedUserId);
exit();