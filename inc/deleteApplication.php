<?php
require_once 'session.php';
require_once 'database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['job_id'])) {
    $applicationId = (int) $_POST['application_id'];
    $jobId = (int) $_POST['job_id'];

    // Check if user is the poster of the job
    $stmt = $conn->prepare("SELECT poster_id FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    $stmt->close();

    if (!$job || $job['poster_id'] !== $userId) {
        echo "Unauthorized access.";
        exit;
    }

    // Delete the application
    $delStmt = $conn->prepare("DELETE FROM job_applications WHERE id = ? AND job_id = ?");
    $delStmt->bind_param("ii", $applicationId, $jobId);
    $delStmt->execute();
    $delStmt->close();

    header("Location: /view-applications.php?id=" . $jobId);
    exit;
}

echo "Invalid request.";
?>