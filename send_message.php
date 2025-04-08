<?php
session_start();
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$sender_id = $_SESSION['user_id'];
$receiver_id = $data['to'];
$message = $data['text'];
$timestamp = date("Y-m-d H:i:s");

$sql = "INSERT INTO messages (sender_id, receiver_id, message, read_status, timestamp) 
        VALUES (?, ?, ?, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $timestamp);

$response = ['success' => false];
if ($stmt->execute()) {
  $response['success'] = true;
}

echo json_encode($response);
?>
