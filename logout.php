<?php
session_start();
require 'inc/database.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("UPDATE users SET status = 'offline' WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

session_unset();
session_destroy();
header("Location: index.php"); // Redirect to login page
exit;
?>
