<?php
require_once 'database.php';
require_once 'session.php';


function getUnreadMessagesCount($conn, $userId) {
    $query = "SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $results = $stmt->get_result();
    
    if ($results) {
        $row = $results->fetch_assoc();
        // Check if $row is null
        if ($row && isset($row['cnt'])) {
            return $row['cnt'];
        }
    }
    
    return 0; // Return 0 if no results or issue with fetching
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
        // Check if 'skills' is set and not null
        if (isset($row['skills']) && $row['skills'] !== null) {
            $userSkills = explode(',', $row['skills']); 
            $userSkills = array_map('trim', $userSkills); // Remove any extra spaces from skills
        }
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
                m.is_read,
                m.title
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
            'is_read'           => $row['is_read'],
            'title'             => $row['title']
        ];
    }
    return !empty($messages) ? $messages : [];
}

function toggleMessageReadStatus($conn, $messageId, $userId) {
    global $currentStatus;
    
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

function sendMessage($conn, $senderId, $receiverId, $title, $message) {
    $query = "INSERT INTO messages (sender_id, receiver_id, title, message) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $senderId, $receiverId, $title, $message);
    return $stmt->execute();
}

function getUsernameByID($conn, $userId) {
    $query = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['username'] ?? 'Unknown';
} 

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data); // Removes whitespace from the beginning and end of string
    $data = stripslashes($data); // Removes quotes from a quoted string
    $data = htmlspecialchars($data); // Converts special characters to HTML entities
    return $data;
}
?>
