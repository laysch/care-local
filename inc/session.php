<?php
$timeout = 900; // 15 minutes
ini_set('session.gc_maxlifetime', $timeout); 
session_set_cookie_params($timeout);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>