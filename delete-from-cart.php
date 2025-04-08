<?php
require_once 'inc/session.php';

// Check if the job ID is provided
if (!isset($_POST['job_id'])) {
    header('Location: job-cart.php');
    exit;
}

$jobId = $_POST['job_id'];

// Check if the job exists in the cart
if (isset($_SESSION['job_cart'][$jobId])) {
    // Remove the job from the cart
    unset($_SESSION['job_cart'][$jobId]);
}

// Redirect back to the job cart page
header('Location: job-cart.php');
exit;
?>
