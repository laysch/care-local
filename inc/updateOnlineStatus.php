<?php
require_once 'session.php';
require 'database.php';

if (isset($_SESSION['user_id']) && isset($_POST['status'])) {
    $status = $_POST['status'];
    $userId = intval($_SESSION['user_id']);

    // Validate input
    $validStatuses = ['online', 'away', 'offline'];
    if (in_array($status, $validStatuses)) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $userId);
        $stmt->execute();
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'profile.php'));
exit;
?>