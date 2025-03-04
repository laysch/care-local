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

?>
