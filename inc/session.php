<?php
$timeout = 900; // 15 minutes

// Only set session config if session isn't active
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', $timeout); 
    session_set_cookie_params($timeout);
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Session timeout check
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    if (isset($_SESSION['user_id'])) {
        require_once 'database.php';
        $stmt = $conn->prepare("UPDATE users SET status = 'offline' WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
    }
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

$userId = $_SESSION['user_id'];
$userName = $_SESSION['username'];

