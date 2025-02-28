<?php
require_once '/inc/database.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

$query = "SELECT 
            m.id,
            m.sender_id,
            m.receiver_id,
            m.message,
            m.timestamp,
            u1.username AS sender_username,
            u2.username AS receiver_username
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id
        JOIN users u2 ON m.receiver_id = u2.id
        WHERE receiver_id = ? 
        OR m.receiver_ID = ?
        ORDER BY timestamp DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'message_id'        => $row['id'],
        'sender_id'         => $row['sender_id'],
        'sender_username'   => $row['sender_username'],
        'receiver_id'       => $row['receiver_id'],
        'receiver_username' => $row['receiver_username'],
        'message'           => $row['message'],
        'timestamp'         => $row['timestamp']
    ];
}
echo json_encode($messages);
?>