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
?>
