<?php
session_start();
include 'db.php';

$current_user_id = $_SESSION['user_id']; // Set this during login

$sql = "SELECT u.id as userid, u.username, u.online 
        FROM users u
        WHERE u.id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
  $conversations[] = [
    'userid' => $row['userid'],
    'username' => $row['username'],
    'status' => $row['online'] ? 'online' : 'offline'
  ];
}

echo json_encode($conversations);
?>
