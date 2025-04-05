<?php
require_once '../inc/database.php';

try {
    $query = "ALTER TABLE users ADD notify_preferences TEXT";
    $conn->query($query);
    echo "Column 'notify_preferences' added to users table.";
} catch (Exception $e) {
    echo $e->getMessage();
}

$conn->close();
?>
