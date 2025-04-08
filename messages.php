<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include_once 'inc/func.php';
$userId = $_SESSION['user_id'];

// get messages
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

// toggle message read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);
    $status = toggleMessageReadStatus($conn, $messageId, $userId);
    echo json_encode(['status' => $status ? 'success' : 'error']);
    exit();
}

// send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['title'], $_POST['message'])) {
    $receiverId = intval($_POST['receiver_id']);
    $messageTitle = trim($_POST['title']);
    $messageContent = trim($_POST['message']);

    if (!empty($messageTitle)) { 
        if (!empty($messageContent)) { 
            $status = sendMessage($conn, $userId, $receiverId, $messageTitle, $messageContent);
            echo json_encode(['status' => $status ? 'success' : 'error']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid message']);
        exit();
    }

}

// send message info from other page
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
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <script src="script.js" defer></script>
    <script>
        // Fetching new message statuses (read/unread toggle)
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
    <style>
        /* Base styles */
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f4f5f7;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row;
        }

        /* Sidebar with profile picture and contacts */
        #sidebar {
            width: 300px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            border-right: 1px solid #e0e0e0;
            overflow-y: auto;
        }

        .sidebar-header {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .contact-list {
            list-style: none;
            padding: 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .contact-item img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }

        .contact-item .name {
            font-weight: 600;
            font-size: 14px;
        }

        .contact-item .status {
            color: #8c8c8c;
            font-size: 12px;
        }

        .contact-item.online .status {
            color: #4caf50;
        }

        .contact-item.offline .status {
            color: #f44336;
        }

        /* Main conversation area */
        #main-body-wrapper {
            flex-grow: 1;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .messages-container {
            display: flex;
            flex-direction: column;
        }

        .messages-header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .messages-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .message-item {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .message-item-unread {
            background-color: #e1f5fe;
        }

        .message-item-read {
            background-color: #f5f5f5;
        }

        .message-item .sender-info {
            font-weight: bold;
        }

        .message-item .timestamp {
            font-size: 12px;
            color: #888;
        }

        /* Styling the message form */
        .message-form {
            display: flex;
            flex-direction: column;
            padding-top: 20px;
        }

        .message-form input, .message-form textarea {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .message-form button {
            padding: 10px;
            background-color: #0073b1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message-form button:hover {
            background-color: #005a8e;
        }
    </style>
</head>
<body>
    <div id="container">
        <!-- Sidebar -->
        <div id="sidebar">
            <div class="sidebar-header">Contacts</div>
            <ul class="contact-list">
                <!-- Dynamically load contacts from your database -->
                <li class="contact-item online">
                    <img src="profile-pic.jpg" alt="Contact Image">
                    <div class="contact-info">
                        <div class="name">John Doe</div>
                        <div class="status">Online</div>
                    </div>
                </li>
                <li class="contact-item offline">
                    <img src="profile-pic2.jpg" alt="Contact Image">
                    <div class="contact-info">
                        <div class="name">Jane Smith</div>
                        <div class="status">Offline</div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <div class="messages-container">
                <div class="messages-header-container">
                    <h1>Inbox</h1>
                </div>

                <div class="messages-section">
                    <div class="messages-header">
                        <span>Received Messages</span>
                    </div>
                    <div id="receivedMessages">
                        <?php if (empty($receivedMessages)) {
                            echo "<p>No messages yet. Start a conversation!</p>";
                        } else {
                            echo "<ul class=\"messages-list\">";
                            foreach ($receivedMessages as $msg) {
                                $messageClass = $msg['is_read'] == 0 ? "message-item-unread" : "message-item-read";
                                echo "<li class=\"message-item $messageClass\" data-message-id=\"" . htmlspecialchars($msg['message_id']) . "\">";
                                echo "<div class=\"sender-info\">" . htmlspecialchars($msg['sender_username']) . "</div>";
                                echo "<p>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
                                echo "<small class=\"timestamp\">" . date("F j, Y, g:i a", strtotime($msg['timestamp'])) . "</small>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } ?>       
                    </div>
                </div>

                <div class="message-form">
                    <form id="sendMessageForm">
                        <input type="text" id="receiverSearch" placeholder="Search user..." autocomplete="off" value="<?php echo $prefilledRecipientName; ?>" required>
                        <input type="hidden" id="receiverId" name="receiver_id" value="<?php echo $prefilledRecipientId; ?>" required>

                        <input type="text" name="title" placeholder="Message Title" value="<?php echo $prefilledMessageTitle; ?>" required>

                        <textarea name="message" placeholder="Write your message..." required></textarea>

                        <button type="submit">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
