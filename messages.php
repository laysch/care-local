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
    <script src="script.js" defer></script>
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

        body {
            font-family: var(--bodyFontFamily);
            font-size: var(--bodyFontSize);
            color: var(--bodyTextColor);
            background-attachment: fixed;
            background-repeat: repeat;
        }

        #main-body-wrapper {
            width: 80vw;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Messages container */
        .messages-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        .messages-header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--bordersColor);
        }

        .messages-header-container h1 {
            font-size: 1.5rem;
            margin: 0;
            color: var(--text-color);
        }

        .jump-to-send {
            padding: 6px 15px;
            background-color: var(--primary-color);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .jump-to-send:hover {
            background-color: var(--highlight-color);
        }

        /* Message sections */
        .messages-section {
            margin-bottom: 20px;
            border: 1px solid var(--bordersColor);
            border-radius: 8px;
            overflow: hidden;
        }

        .messages-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: var(--text-color);
            font-weight: bold;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 1.2rem;
        }

        .messages-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* Message items */
        .message-item, .message-item-read, .message-item-unread {
            display: block;
            padding: 15px;
            border-bottom: 1px solid var(--bordersColor);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .message-item:last-child, .message-item-read:last-child, .message-item-unread:last-child {
            border-bottom: none;
        }

        .message-item-unread {
            background-color: var(--unread-bg);
        }

        .message-item:hover, .message-item-read:hover, .message-item-unread:hover {
            background-color: var(--secondary-color);
        }

        /* Conversation tabs */
        .conversation-tabs {
            display: flex;
            border-bottom: 1px solid var(--bordersColor);
            margin-bottom: 10px;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .tab.active {
            border-bottom: 2px solid var(--highlight-color);
            color: var(--highlight-color);
        }

        .tab:hover {
            background-color: var(--secondary-color);
        }

        /* Send message form */
        #sendMessageForm {
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px var(--shadow-color);
        }

        #sendMessageForm label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--text-color);
        }

        #sendMessageForm input,
        #sendMessageForm textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            font-family: 'PT Sans', sans-serif;
        }

        #sendMessageForm button {
            padding: 8px 20px;
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #sendMessageForm button:hover {
            background-color: var(--highlight-color);
        }

        /* User suggestions */
        .user-suggestions {
            background-color: white;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            max-height: 150px;
            overflow-y: auto;
            margin-top: -15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px var(--shadow-color);
        }

        .user-suggestion {
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .user-suggestion:hover {
            background-color: var(--secondary-color);
        }

        /* Modal styles */
        .message-modal {
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
            border-radius: 8px;
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
            font-size: 1.2rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--light-text);
        }

        /* Message view */
        .message-view {
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 10px var(--shadow-color);
            display: none;
        }

        .message-view-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--bordersColor);
        }

        .message-view-title {
            font-size: 1.3rem;
            margin: 0 0 10px 0;
            color: var(--text-color);
        }

        .message-view-meta {
            font-size: 0.9rem;
            color: var(--light-text);
        }

        .message-view-content {
            line-height: 1.6;
            color: var(--text-color);
            font-family: 'PT Sans', sans-serif;
        }

        .message-view-actions {
            margin-top: 20px;
            text-align: right;
        }

        .message-view-actions button {
            padding: 8px 20px;
            background-color: var(--primary-color);
            color: var(--text-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .message-view-actions button:hover {
            background-color: var(--highlight-color);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            #main-body-wrapper {
                width: 95vw;
                padding: 10px;
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
            <div class="messages-container">
                <div class="messages-header-container">
                    <h1>Inbox</h1>
                    <a href="#sendMessageForm" class="jump-to-send">New Message</a>
                </div>

                <div class="conversation-tabs">
                    <div class="tab active" data-tab="received">Received</div>
                    <div class="tab" data-tab="sent">Sent</div>
                </div>

                <!-- Received messages tab content -->
                <div class="tab-content" id="received-tab">
                    <div class="messages-section">
                        <?php if (empty($receivedMessages)): ?>
                            <p style="padding: 20px; text-align: center;">Let's connect with your community members today!</p>
                        <?php else: ?>
                            <ul class="messages-list">
                                <?php foreach ($receivedMessages as $msg): ?>
                                    <li class="<?php echo $msg['is_read'] == 0 ? 'message-item-unread' : 'message-item-read'; ?>"
                                        data-message-id="<?php echo htmlspecialchars($msg['message_id']); ?>"
                                        data-title="<?php echo htmlspecialchars($msg['title']); ?>"
                                        data-sender="<?php echo htmlspecialchars($msg['sender_username']); ?>"
                                        data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                        data-time="<?php echo date('F j, Y, g:i a', strtotime($msg['timestamp'])); ?>">
                                        <strong><?php echo htmlspecialchars($msg['title']); ?></strong><br>
                                        From: <strong><?php echo htmlspecialchars($msg['sender_username']); ?></strong><br>
                                        <span class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></span><br>
                                        <small><?php echo date('F j, Y, g:i a', strtotime($msg['timestamp'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sent messages tab content -->
                <div class="tab-content" id="sent-tab" style="display: none;">
                    <div class="messages-section">
                        <?php if (empty($sentMessages)): ?>
                            <p style="padding: 20px; text-align: center;">Let's connect with your community members today!</p>
                        <?php else: ?>
                            <ul class="messages-list">
                                <?php foreach ($sentMessages as $msg): ?>
                                    <li class="message-item-read"
                                        data-message-id="<?php echo htmlspecialchars($msg['message_id']); ?>"
                                        data-title="<?php echo htmlspecialchars($msg['title']); ?>"
                                        data-sender="You"
                                        data-receiver="<?php echo htmlspecialchars($msg['receiver_username']); ?>"
                                        data-message="<?php echo htmlspecialchars($msg['message']); ?>"
                                        data-time="<?php echo date('F j, Y, g:i a', strtotime($msg['timestamp'])); ?>">
                                        <strong><?php echo htmlspecialchars($msg['title']); ?></strong><br>
                                        To: <strong><?php echo htmlspecialchars($msg['receiver_username']); ?></strong><br>
                                        <span class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></span><br>
                                        <small><?php echo date('F j, Y, g:i a', strtotime($msg['timestamp'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Message detail view -->
                <div class="message-view" id="messageView">
                    <div class="message-view-header">
                        <h2 class="message-view-title" id="viewTitle"></h2>
                        <div class="message-view-meta" id="viewMeta"></div>
                    </div>
                    <div class="message-view-content" id="viewContent"></div>
                    <div class="message-view-actions">
                        <button id="closeView">Close</button>
                        <button id="replyBtn">Reply</button>
                    </div>
                </div>

                <!-- Send message form -->
                <div class="messages-section" id="sendMessageSection">
                    <div class="messages-header">Send a Message</div>
                    <form id="sendMessageForm">
                        <label for="receiverSearch">Send to:</label>
                        <input type="text" id="receiverSearch" placeholder="Search user..." autocomplete="off"
                            value="<?php echo $prefilledRecipientName; ?>">
                        <input type="hidden" id="receiverId" name="receiver_id" value="<?php echo $prefilledRecipientId; ?>">
                        <div id="userSuggestions" class="user-suggestions"></div>

                        <label for="messageTitle">Title:</label>
                        <input type="text" id="messageTitle" name="title" value="<?php echo $prefilledMessageTitle; ?>" required>

                        <label for="messageContent">Message:</label>
                        <textarea id="messageContent" name="message" rows="4" required></textarea>

                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Toggle tabs
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                    });

                    // Show the corresponding tab content
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId + '-tab').style.display = 'block';

                    // Hide message view when switching tabs
                    document.getElementById('messageView').style.display = 'none';
                });
            });

            // Message item click
            const messageItems = document.querySelectorAll('.message-item-read, .message-item-unread');
            messageItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Mark as read if unread
                    if (this.classList.contains('message-item-unread')) {
                        const messageId = this.dataset.messageId;

                        fetch("messages.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "message_id=" + encodeURIComponent(messageId)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                this.classList.remove('message-item-unread');
                                this.classList.add('message-item-read');
                            }
                        })
                        .catch(error => console.error("Error:", error));
                    }

                    // Show message view
                    const messageView = document.getElementById('messageView');
                    messageView.style.display = 'block';

                    // Fill in message details
                    document.getElementById('viewTitle').textContent = this.dataset.title;

                    let metaText = '';
                    if (this.dataset.sender === 'You') {
                        metaText = `To: ${this.dataset.receiver} • ${this.dataset.time}`;
                    } else {
                        metaText = `From: ${this.dataset.sender} • ${this.dataset.time}`;
                    }
                    document.getElementById('viewMeta').textContent = metaText;
                    document.getElementById('viewContent').textContent = this.dataset.message;

                    // Setup reply button
                    const replyBtn = document.getElementById('replyBtn');
                    replyBtn.onclick = function() {
                        const receiverSearch = document.getElementById('receiverSearch');
                        const receiverId = document.getElementById('receiverId');
                        const messageTitle = document.getElementById('messageTitle');

                        // If we're viewing a received message, set the receiver to the sender
                        if (item.dataset.sender !== 'You') {
                            receiverSearch.value = item.dataset.sender;
                            // You'll need to have the sender's ID available in your data
                            // This is just a placeholder assuming you have it
                            // receiverId.value = item.dataset.senderId;
                        }

                        // Set title as "Re: Original Title"
                        messageTitle.value = "Re: " + item.dataset.title;

                        // Scroll to the form
                        document.getElementById('sendMessageSection').scrollIntoView({
                            behavior: 'smooth'
                        });
                    };

                    // Close button
                    document.getElementById('closeView').onclick = function() {
                        messageView.style.display = 'none';
                    };

                    // Scroll to message view
                    messageView.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // User search functionality
            const searchInput = document.getElementById("receiverSearch");
            const receiverIdInput = document.getElementById("receiverId");
            const suggestionsContainer = document.getElementById("userSuggestions");

            searchInput.addEventListener("input", function() {
                let query = searchInput.value.trim();
                if (query.length < 2) {
                    suggestionsContainer.innerHTML = "";
                    suggestionsContainer.style.display = "none";
                    return;
                }

                fetch("inc/searchUsers.php?search=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(users => {
                        suggestionsContainer.innerHTML = ""; // Clear old suggestions

                        if (users.length > 0) {
                            suggestionsContainer.style.display = "block";

                            users.forEach(user => {
                                let suggestion = document.createElement("div");
                                suggestion.classList.add("user-suggestion");
                                suggestion.textContent = user.username;
                                suggestion.dataset.userId = user.id;

                                suggestion.addEventListener("click", function() {
                                    searchInput.value = this.textContent;
                                    receiverIdInput.value = this.dataset.userId;
                                    suggestionsContainer.innerHTML = "";
                                    suggestionsContainer.style.display = "none";
                                });

                                suggestionsContainer.appendChild(suggestion);
                            });
                        } else {
                            suggestionsContainer.style.display = "block";
                            suggestionsContainer.innerHTML = "<div class='user-suggestion'>No users found</div>";
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching users:", error);
                        suggestionsContainer.style.display = "none";
                    });
            });

            // Send message form submission
            const form = document.getElementById("sendMessageForm");
            form.addEventListener("submit", function(event) {
                event.preventDefault();

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
                        // Reset form
                        form.reset();
                        // Reload page to show new message
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

        // Function to toggle sections
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section.style.display === "none") {
                section.style.display = "block";
            } else {
                section.style.display = "none";
            }
        }
    </script>
</body>
</html>
