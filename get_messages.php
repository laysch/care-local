<?php
session_start();
include 'db.php';

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

$sql = "SELECT sender_id, receiver_id, message, read_status, timestamp 
        FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY timestamp ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
  $messages[] = [
    'sender' => $row['sender_id'] == $current_user_id ? 'me' : 'them',
    'text' => $row['message'],
    'read' => $row['read_status'] == 1
  ];
}

// Mark messages as read
$update = $conn->prepare("UPDATE messages SET read_status = 1 WHERE sender_id = ? AND receiver_id = ?");
$update->bind_param("ii", $other_user_id, $current_user_id);
$update->execute();

echo json_encode($messages);
?>
