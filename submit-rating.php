<?php
session_start();
require_once 'inc/database.php';

$currentUserId = $_SESSION['rater_user_id'] ?? null;
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
$query = "SELECT * FROM ratings WHERE rater_user_id = ? AND rated_user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $currentUserId, $ratedUserId);
$stmt->execute();
$result = $stmt->get_result();

// If a rating exists, update the rating
if ($result->num_rows > 0) {
    $updateStmt = $conn->prepare("UPDATE ratings SET rating = ? WHERE rater_user_id = ? AND rated_user_id = ?");
    $updateStmt->bind_param("iii", $rating, $currentUserId, $ratedUserId);
    $updateStmt->execute();
    $updateStmt->close();
} else {
    // If no rating exists, insert a new rating
    $insertStmt = $conn->prepare("INSERT INTO ratings (rater_user_id, rated_user_id, rating) VALUES (?, ?, ?)");
    $insertStmt->bind_param("iii", $currentUserId, $ratedUserId, $rating);
    $insertStmt->execute();
    $insertStmt->close();
}

// Redirect back to the profile page
header("Location: profile-details.php?id=" . $ratedUserId . "&rated=1");
exit();
?>
