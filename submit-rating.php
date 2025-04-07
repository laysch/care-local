<?php
session_start();
require_once 'inc/database.php';

$currentUserId = $_SESSION['user_id'] ?? null;
$ratedUserId = $_POST['rated_user_id'] ?? null;
$rating = $_POST['rating'] ?? null;

if (!$currentUserId || !$ratedUserId || !$rating) {
    die("Invalid request.");
}

// Optional: Validate rating is between 1 and 5
if ($rating < 1 || $rating > 5) {
    die("Rating must be between 1 and 5.");
}

// Insert or update rating
$stmt = $conn->prepare("REPLACE INTO user_ratings (rater_id, rated_id, rating) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $currentUserId, $ratedUserId, $rating);

if ($stmt->execute()) {
    header("Location: profile-details.php?id=" . $ratedUserId . "&rated=1");
    exit();
} else {
    echo "Failed to submit rating: " . $conn->error;
}
?>