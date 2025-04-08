<?php
require_once 'session.php';
require_once "database.php"; 



// Validate the job ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "Invalid job ID.";
    exit;
}

$job_id = intval($_GET['id']);

// Prepare the delete query with transaction for safety
$conn->begin_transaction();

try {
    // Check ownership
    $stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND poster_id = ?");
    $stmt->bind_param("ii", $job_id, $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $conn->rollback();
        http_response_code(403);
        echo "You do not have permission to delete this job.";
        exit;
    }

    $stmt->close();

    // Delete the job
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        $conn->commit();
        echo "Job deleted successfully.";
    } else {
        $conn->rollback();
        echo "Failed to delete job.";
    }

    $stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
