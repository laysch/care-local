<?php
require_once 'inc/database.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<html>
    <body>
    <form action="inc/sendMessage.php" method="POST">
        <input type="hidden" name="sender_id" value="<?php echo $_SESSION['user_id']; ?>">
        <input type="hidden" name="receiver_id" value="RECEIVER_USER_ID" >
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>
</body>
</html>