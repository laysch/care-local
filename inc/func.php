<?php
require_once 'database.php';


function getUnreadMessagesCount($conn, $userId) {
    $query = "SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $results = $stmt->get_result();
    
    if ($results) {
        $row = $results->fetch_assoc();
        return $row['cnt'];
    }
    
    return 0; 
}

function getUserSkills($conn, $userId) {
    $query = "SELECT skills FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $results = $stmt->get_result();

    $userSkills = [];

    if ($results) {
        $row = $results->fetch_assoc();
        $userSkills = explode(',', $row['skills']); 
        $userSkills = array_map('trim', $userSkills);
    }
    
    return $userSkills;
}

function getUserMessages($conn, $userId) {
    $query = "SELECT 
                m.id,
                m.sender_id,
                m.receiver_id,
                m.message,
                m.timestamp,
                u1.username AS sender_username,
                u2.username AS receiver_username,
                is_read
              FROM messages m
              JOIN users u1 ON m.sender_id = u1.id
              JOIN users u2 ON m.receiver_id = u2.id
              WHERE receiver_id = ? OR sender_id = ?
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
            'timestamp'         => $row['timestamp'],
            'is_read'           => $row['is_read']
        ];
    }
    return !empty($messages) ? $messages : [];
}

function toggleMessageReadStatus($conn, $messageId, $userId) {
    $query = "SELECT is_read FROM messages WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $messageId, $userId);
    $stmt->execute();
    $stmt->bind_result($currentStatus);
    $stmt->fetch();
    $stmt->close();

    if ($currentStatus === null) {
        return false;
    }

    $newStatus = $currentStatus == 1 ? 0 : 1;

    $query = "UPDATE messages SET is_read = ? WHERE id = ? AND receiver_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $newStatus, $messageId, $userId);
    return $stmt->execute(); 
}

?>
