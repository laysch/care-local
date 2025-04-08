<?php
require_once "../inc/database.php";
require_once 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senderId = $_POST['sender_id'];
    $receiverId = $_POST['receiver_id'];
    $message = $_POST['message'];

    try{

        if ($senderId != $_SESSION['user_id']) {
            throw new Exception("Sender ID does not match session ID.");
        }

        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->bind_param("i", $receiverId);
        $stmt->execute();
        $results = $stmt->get_result();

        if ($results->num_rows === 0) throw new Exception("Recipient ID not found.");

        
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $senderId, $receiverId, $message);
        $stmt->execute();
        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
}
?>