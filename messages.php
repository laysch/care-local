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
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #f9eedd;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
            --primary-color: #cdd8c4;
            --secondary-color: #f0f2ee;
            --highlight-color: #86a377;
            --text-color: #333333;
            --light-text: #666666;
            --unread-bg: #e6f9e6;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--bodyFontFamily);
            font-size: var(--bodyFontSize);
            color: var(--bodyTextColor);
            background-color: var(--backgroundColor);
            min-height: 100vh;
        }

        #container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #main-body-wrapper {
            flex: 1;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        /* Main messaging container - LinkedIn style with CareLocal colors */
        .messaging-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            height: calc(100vh - 40px);
        }

        /* Left panel - Conversations list */
        .conversations-list {
            width: 350px;
            border-right: 1px solid var(--bordersColor);
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        .conversations-header {
            padding: 20px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .conversations-header h2 {
            font-size: 18px;
            color: var(--text-color);
            margin: 0;
        }

        .compose-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .compose-btn:hover {
            background-color: var(--highlight-color);
        }

        .search-container {
            padding: 10px 15px;
            border-bottom: 1px solid var(--bordersColor);
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--bordersColor);
            border-radius: 20px;
            font-family: 'PT Sans', sans-serif;
            font-size: 14px;
        }

        .conversation-tabs {
            display: flex;
            border-bottom: 1px solid var(--bordersColor);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            cursor: pointer;
            font-weight: bold;
            color: var(--text-color);
            transition: all 0.3s;
        }

        .tab.active {
            border-bottom: 2px solid var(--highlight-color);
            color: var(--highlight-color);
        }

        .tab-content {
            flex: 1;
            overflow-y: auto;
        }

        /* Conversation items */
        .conversation-item {
            padding: 15px;
            border-bottom: 1px solid var(--bordersColor);
            cursor: pointer;
            display: flex;
            align-items: flex-start;
            transition: background-color 0.3s;
        }

        .conversation-item:hover {
            background-color: var(--secondary-color);
        }

        .conversation-item.unread {
            background-color: var(--unread-bg);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            font-family: 'PT Sans', sans-serif;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .sender-name {
            font-weight: bold;
            color: var(--text-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-time {
            color: var(--light-text);
            font-size: 12px;
            white-space: nowrap;
        }

        .message-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .message-preview {
            color: var(--light-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: 'PT Sans', sans-serif;
        }

        /* Right panel - Message view */
        .message-view {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        .no-message-selected {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: var(--secondary-color);
            color: var(--light-text);
            text-align: center;
            padding: 20px;
        }

        .no-message-selected h2 {
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .no-message-selected p {
            margin-bottom: 20px;
            font-family: 'PT Sans', sans-serif;
        }

        .no-message-selected button {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .no-message-selected button:hover {
            background-color: var(--highlight-color);
        }

        .selected-message {
            flex: 1;
            display: flex;
            flex-direction: column;
            display: none;
        }

        .message-view-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            align-items: center;
        }

        .sender-info {
            margin-left: 15px;
        }

        .sender-info h3 {
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .sender-info p {
            color: var(--light-text);
            font-size: 14px;
            font-family: 'PT Sans', sans-serif;
        }

        .message-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message-title-view {
            font-size: 20px;
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 15px;
        }

        .message-body {
            line-height: 1.6;
            color: var(--text-color);
            margin-bottom: 20px;
            font-family: 'PT Sans', sans-serif;
        }

        .message-timestamp {
            color: var(--light-text);
            font-size: 14px;
        }

        .compose-message {
            padding: 20px;
            border-top: 1px solid var(--bordersColor);
        }

        .compose-message textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            resize: none;
            font-size: 14px;
            height: 80px;
            font-family: 'PT Sans', sans-serif;
        }

        .compose-message textarea:focus {
            outline: none;
            border-color: var(--highlight-color);
        }

        .compose-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .send-btn {
            padding: 8px 20px;
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .send-btn:hover {
            background-color: var(--highlight-color);
        }

        /* New message modal */
        .new-message-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 5px 15px var(--shadow-color);
        }

        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: var(--text-color);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--light-text);
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-color);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            font-size: 14px;
            font-family: 'PT Sans', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--highlight-color);
        }

        .user-suggestions {
            background-color: white;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            margin-top: 5px;
            box-shadow: 0 3px 10px var(--shadow-color);
        }

        .user-suggestion {
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'PT Sans', sans-serif;
        }

        .user-suggestion:hover {
            background-color: var(--secondary-color);
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--bordersColor);
            display: flex;
            justify-content: flex-end;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .messaging-container {
                flex-direction: column;
                height: calc(100vh - 20px);
            }

            .conversations-list {
                width: 100%;
                max-height: 40vh;
            }

            .message-view {
                flex: 1;
            }

            .modal-content {
                width: 95%;
            }
        }
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <div class="messaging-container">
                <!-- Left panel - Conversations list -->
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h2>Messaging</h2>
                        <button class="compose-btn" id="newMessageBtn" title="New Message">
                            <i class="fi fi-rr-pencil"></i>
                        </button>
                    </div>
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search messages...">
                    </div>
                    <div class="conversation-tabs">
                        <div class="tab active" data-tab="received">Inbox</div>
                        <div class="tab" data-tab="sent">Sent</div>
                    </div>

                    <!-- Received messages tab content -->
                    <div class="tab-content" id="received-tab">
                        <?php if (empty($receivedMessages)): ?>
                            <div class="empty-state" style="padding: 20px; text-align: center;">
                                <p>Let's connect with your community members today!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($receivedMessages as $msg): ?>
                                <div class="conversation-item <?php echo ($msg['is_read'] == 0 ? 'unread' : ''); ?>"
                                     data-message-id="<?php echo htmlspecialchars($msg['message_id']); ?>"
                                     data-title="<?php echo htmlspecialchars($msg['title']); ?>"
                                     data-sender="<?php echo htmlspecialchars($msg['sender_username']); ?>"
                                     data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                     data-time="<?php echo date("F j, Y, g:i a", strtotime($msg['timestamp'])); ?>">
                                    <div class="avatar">
                                        <?php echo strtoupper(substr($msg['sender_username'], 0, 1)); ?>
                                    </div>
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <span class="sender-name"><?php echo htmlspecialchars($msg['sender_username']); ?></span>
                                            <span class="message-time"><?php echo date("M j", strtotime($msg['timestamp'])); ?></span>
                                        </div>
                                        <div class="message-title"><?php echo htmlspecialchars($msg['title']); ?></div>
                                        <div class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Sent messages tab content -->
                    <div class="tab-content" id="sent-tab" style="display: none;">
                        <?php if (empty($sentMessages)): ?>
                            <div class="empty-state" style="padding: 20px; text-align: center;">
                                <p>Send a message to connect with community members!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($sentMessages as $msg): ?>
                                <div class="conversation-item"
                                     data-message-id="<?php echo htmlspecialchars($msg['message_id']); ?>"
                                     data-title="<?php echo htmlspecialchars($msg['title']); ?>"
                                     data-sender="You"
                                     data-receiver="<?php echo htmlspecialchars($msg['receiver_username']); ?>"
                                     data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                     data-time="<?php echo date("F j, Y, g:i a", strtotime($msg['timestamp'])); ?>">
                                    <div class="avatar">
                                        <?php echo strtoupper(substr($msg['receiver_username'], 0, 1)); ?>
                                    </div>
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <span class="sender-name">To: <?php echo htmlspecialchars($msg['receiver_username']); ?></span>
                                            <span class="message-time"><?php echo date("M j", strtotime($msg['timestamp'])); ?></span>
                                        </div>
                                        <div class="message-title"><?php echo htmlspecialchars($msg['title']); ?></div>
                                        <div class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="message-view">
                    <!-- No message selected state -->
                    <div class="no-message-selected">
                        <h2>Select a message</h2>
                        <p>Choose a conversation from the list or start a new one</p>
                        <button id="newMessageBtnAlt">New Message</button>
                    </div>

                    <!-- Selected message view -->
                    <div class="selected-message">
                        <div class="message-view-header">
                            <div class="avatar" id="message-avatar"></div>
                            <div class="sender-info">
                                <h3 id="message-sender-name"></h3>
                                <p id="message-sender-info"></p>
                            </div>
                        </div>
                        <div class="message-content">
                            <h2 class="message-title-view" id="message-title"></h2>
                            <div class="message-body" id="message-body"></div>
                            <div class="message-timestamp" id="message-timestamp"></div>
                        </div>
                        <div class="compose-message">
                            <textarea placeholder="Type a reply..."></textarea>
                            <div class="compose-actions">
                                <button class="send-btn">Reply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New message modal -->
    <div class="new-message-modal" id="newMessageModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>New Message</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="sendMessageForm">
                    <div class="form-group">
                        <label for="receiverSearch">To:</label>
                        <input type="text" id="receiverSearch" placeholder="Search users..."
                               value="<?php echo $prefilledRecipientName; ?>" autocomplete="off">
                        <input type="hidden" id="receiverId" name="receiver_id"
                               value="<?php echo $prefilledRecipientId; ?>">
                        <div id="userSuggestions" class="user-suggestions"></div>
                    </div>
                    <div class="form-group">
                        <label for="messageTitle">Subject:</label>
                        <input type="text" id="messageTitle" name="title"
                               value="<?php echo $prefilledMessageTitle; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="messageContent">Message:</label>
                        <textarea id="messageContent" name="message" rows="5" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="send-btn" id="sendMessageBtn">Send</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tab switching
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-content').forEach(content => {
                        content.style.display = 'none';
                    });
                    document.getElementById(tabId + '-tab').style.display = 'block';

                    // Reset message view when switching tabs
                    document.querySelector('.no-message-selected').style.display = 'flex';
                    document.querySelector('.selected-message').style.display = 'none';
                });
            });

            // Handle clicking on conversation items
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Mark as read if unread
                    if (this.classList.contains('unread')) {
                        const messageId = this.dataset.messageId;

                        fetch("messages.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "message_id=" + encodeURIComponent(messageId)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                this.classList.remove('unread');
                            }
                        })
                        .catch(error => console.error("Error:", error));
                    }

                    // Display message in the message view
                    document.querySelector('.no-message-selected').style.display = 'none';
                    document.querySelector('.selected-message').style.display = 'flex';

                    // Populate message data
                    document.getElementById('message-avatar').textContent = this.querySelector('.avatar').textContent;
                    document.getElementById('message-sender-name').textContent = this.dataset.sender;

                    if (this.dataset.receiver) {
                        document.getElementById('message-sender-info').textContent = `To: ${this.dataset.receiver}`;
                    } else {
                        document.getElementById('message-sender-info').textContent = '';
                    }

                    document.getElementById('message-title').textContent = this.dataset.title;
                    document.getElementById('message-body').textContent = this.dataset.message;
                    document.getElementById('message-timestamp').textContent = this.dataset.time;
                });
            });

            // New message modal
            const modal = document.getElementById('newMessageModal');
            const openModalBtn = document.getElementById('newMessageBtn');
            const openModalBtnAlt = document.getElementById('newMessageBtnAlt');
            const closeModalBtn = document.querySelector('.close-modal');
            const sendMessageBtn = document.getElementById('sendMessageBtn');

            function openModal() {
                modal.style.display = 'flex';
            }

            function closeModal() {
                modal.style.display = 'none';
            }

            openModalBtn.addEventListener('click', openModal);
            openModalBtnAlt?.addEventListener('click', openModal);
            closeModalBtn.addEventListener('click', closeModal);

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // User search functionality
            const searchInput = document.getElementById("receiverSearch");
            const receiverIdInput = document.getElementById("receiverId");
            const suggestionsContainer = document.getElementById("userSuggestions");

            if (receiverIdInput.value) {
                searchInput.value = searchInput.value.trim();
            }

            searchInput.addEventListener("input", function() {
                let query = searchInput.value.trim();
                if (query.length < 2) {
                    suggestionsContainer.innerHTML = "";
                    return;
                }

                fetch("inc/searchUsers.php?search=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(users => {
                        suggestionsContainer.innerHTML = ""; // Clear old suggestions
                        users.forEach(user => {
                            let suggestion = document.createElement("div");
                            suggestion.classList.add("user-suggestion");
                            suggestion.textContent = user.username;
                            suggestion.dataset.userId = user.id;

                            suggestion.addEventListener("click", function() {
                                searchInput.value = this.textContent;
                                receiverIdInput.value = this.dataset.userId;
                                suggestionsContainer.innerHTML = "";
                            });

                            suggestionsContainer.appendChild(suggestion);
                        });

                        if (users.length === 0) {
                            suggestionsContainer.innerHTML = "<div class='user-suggestion'>No users found</div>";
                        }
                    })
                    .catch(error => console.error("Error fetching users:", error));
            });

            // Send message form submission
            sendMessageBtn.addEventListener("click", function() {
                const form = document.getElementById("sendMessageForm");
                const receiverIdInput = document.getElementById("receiverId");

                if (!receiverIdInput.value) {
                    alert("Please select a user from the search results.");
                    return;
                }

                const formData = new FormData(form);
                fetch("messages.php", {
                    method: "POST",
                    body: new URLSearchParams(formData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Message sent successfully!");
                        window.location.reload();
                    } else {
                        alert("Failed to send message: " + (data.message || "Unknown error"));
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Failed to send message. Network error.");
                });
            });
        });
    </script>
</body>
</html>
