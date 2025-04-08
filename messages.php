<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include_once 'inc/func.php';
$userId = $_SESSION['user_id'];

// Get messages
$messages = getUserMessages($conn, $userId);
$receivedMessages = [];
$sentMessages = [];

foreach ($messages as $msg) {
    if ($msg['receiver_id'] == $userId) {
        $receivedMessages[] = $msg; 
    } 
}
foreach ($messages as $msg) {
    if ($msg['sender_id'] == $userId) {
        $sentMessages[] = $msg; 
    } 
}

// Toggle message read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);
    $status = toggleMessageReadStatus($conn, $messageId, $userId);
    echo json_encode(['status' => $status ? 'success' : 'error']);
    exit();
}

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['title'], $_POST['message'])) {
    $receiverId = intval($_POST['receiver_id']);
    $messageTitle = trim($_POST['title']);
    $messageContent = trim($_POST['message']);

    if (!empty($messageTitle) && !empty($messageContent)) { 
        $status = sendMessage($conn, $userId, $receiverId, $messageTitle, $messageContent);
        echo json_encode(['status' => $status ? 'success' : 'error']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid message']);
        exit();
    }
}

// Send message info from other page
$prefilledRecipientId = isset($_GET['recipient_id']) ? intval($_GET['recipient_id']) : '';
$prefilledRecipientName = isset($_GET['recipient_name']) ? htmlspecialchars($_GET['recipient_name']) : '';
$prefilledMessageTitle = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <link rel="stylesheet" href="style/messages.css">
    <script src="script.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".message-item").forEach(item => {
                item.addEventListener("click", function () {
                    let messageId = this.dataset.messageId;

                    fetch("messages.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "message_id=" + encodeURIComponent(messageId)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            // Toggle class based on read/unread state
                            if (this.classList.contains("message-item-unread")) {
                                this.classList.remove("message-item-unread");
                                this.classList.add("message-item-read");
                            } else {
                                this.classList.remove("message-item-read");
                                this.classList.add("message-item-unread");
                            }
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
            });
        });
    </script>
</head>
<body>
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <div class="messages-container">
                <div class="messages-header-container">
                    <h1>Inbox</h1>
                    <a href="#sendMessageForm" class="jump-to-send">Send Message</a>
                </div>

                <!-- Received Messages Section -->
                <div class="messages-section">
                    <div class="messages-header">
                        <span>Received Messages</span>
                        <button class="toggle-btn" onclick="toggleSection('receivedMessages')">=</button>
                    </div>
                    <div id="receivedMessages">
                        <?php if (empty($receivedMessages)) {
                            echo "<p>No messages yet! Start connecting with your community.</p>";
                        } else {
                            echo "<ul class=\"messages-list\">";
                            foreach ($receivedMessages as $msg) {
                                $messageClass = $msg['is_read'] == 0 ? "message-item-unread" : "message-item-read";
                                echo "<li class=\"$messageClass\" data-message-id=\"" . htmlspecialchars($msg['message_id']) . "\">";
                                echo "<strong>" . htmlspecialchars($msg['sender_username']) . "</strong> to <strong>" . htmlspecialchars($msg['receiver_username']) . "</strong><br>";
                                echo "<p>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
                                echo "<small>" . date("F j, Y, g:i a", strtotime($msg['timestamp'])) . "</small>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } ?>       
                    </div>       
                </div>

                <!-- Sent Messages Section -->
                <div class="messages-section">
                    <div class="messages-header">
                        <span>Sent Messages</span>
                        <button class="toggle-btn" onclick="toggleSection('sentMessages')">=</button>
                    </div>
                    <div id="sentMessages">
                        <?php if (empty($sentMessages)) {
                            echo "<p>No sent messages yet. Send a message to start a conversation.</p>";
                        } else {
                            echo "<ul class=\"messages-list\">";
                            foreach ($sentMessages as $msg) {
                                echo "<li class=\"message-item-read\">";
                                echo "<strong>You</strong> to <strong>" . htmlspecialchars($msg['receiver_username']) . "</strong><br>";
                                echo "<p>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
                                echo "<small>" . date("F j, Y, g:i a", strtotime($msg['timestamp'])) . "</small>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } ?>
                    </div>
                </div>

                <!-- Send Message Form -->
                <div class="messages-section">
                    <div class="messages-header">Send a Message</div>
                    <form id="sendMessageForm">
                        <label for="receiverSearch">Send to:</label>
                        <input type="text" id="receiverSearch" placeholder="Search user..." autocomplete="off" value="<?php echo $prefilledRecipientName; ?>">
                        <input type="hidden" id="receiverId" name="receiver_id" value="<?php echo $prefilledRecipientId; ?>">
                        <div id="userSuggestions"></div> 
                        <label for="messageTitle">Title:</label>
                        <input type="text" id="messageTitle" name="title" value="<?php echo $prefilledMessageTitle; ?>" required>

                        <label for="messageContent">Message:</label>
                        <textarea id="messageContent" name="message" rows="3" required></textarea>

                        <button type="submit">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

