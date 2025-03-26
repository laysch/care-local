<?php
session_start();
require_once "database.php"; // Ensure this connects to $conn

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

// Validate the ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "Invalid ID.";
    exit;
}

$event_id = intval($_GET['id']);
$user_id = $_SESSION["user_id"];

// Start transaction for safety
$conn->begin_transaction();

try {
    // Check ownership via join with jobs table
    $stmt = $conn->prepare("
        SELECT events.id 
        FROM events 
        JOIN jobs ON events.job_id = jobs.id 
        WHERE events.id = ? AND jobs.poster_id = ?
    ");
    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        $conn->rollback();
        http_response_code(403);
        echo "You do not have permission to delete this event.";
        exit;
    }

    $stmt->close();

    // Proceed with delete
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        $conn->commit();
        echo "Event deleted successfully.";
    } else {
        $conn->rollback();
        echo "Failed to delete event.";
    }

    $stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
