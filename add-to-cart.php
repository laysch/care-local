<?php
require_once 'inc/session.php';

// Check if the form is submitted
if (isset($_POST['add_to_cart'])) {
    // Get job details from the form
    $jobId = $_POST['job_id'];
    $jobTitle = $_POST['job_title'];
    $jobDescription = $_POST['job_description'];
    $jobSkills = $_POST['job_skills'];

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['job_cart'])) {
        $_SESSION['job_cart'] = [];
    }

    // Add the job to the cart
    $_SESSION['job_cart'][$jobId] = [
        'title' => $jobTitle,
        'description' => $jobDescription,
        'skills' => $jobSkills
    ];

    // Redirect to the job cart page
    header('Location: job-cart.php');
    exit;
}
?>
