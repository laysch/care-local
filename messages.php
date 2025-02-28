<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
ob_start();
include 'inc/getMessages.php';
$jsonResults = ob_get_clean();
$messages = json_decode($jsonResults, true);

if (is_array($messages)) {
    foreach ($messages as $msg) {
        echo "<p>";
        echo "<strong>From:</strong> " . htmlspecialchars($msg['sender_username']) . "<br>";
        echo "<strong>To:</strong> " . htmlspecialchars($msg['receiver_username']) . "<br>";
        echo "<strong>Message:</strong> " . htmlspecialchars($msg['message']) . "<br>";
        echo "<strong>Timestamp:</strong> " . htmlspecialchars($msg['timestamp']) . "<br>";
        echo "</p><hr>";
    }
} else echo "No messages";

?>
