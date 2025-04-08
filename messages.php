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
            --unread-bg: #e6f9e6;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: var(--bodyFontFamily);
            font-size: var(--bodyFontSize);
            color: var(--bodyTextColor);
            margin: 0;
            padding: 0;
        }

        #container {
            display: flex;
            width: 100%;
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

        .messages-container {
            width: 100%;
            display: flex;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px var(--shadow-color);
        }

        /* Conversation list */
        .conversations-list {
            width: 340px;
            background-color: white;
            border-right: 1px solid var(--bordersColor);
            overflow-y: auto;
            max-height: 600px;
        }

        .conversations-header {
            padding: 15px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .conversations-header h2 {
            font-size: 18px;
            margin: 0;
        }

        .compose-btn {
            background-color: var(--primary-color);
            color: var(--bodyTextColor);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .compose-btn:hover {
            background-color: var(--linksHoverColor);
        }

        .search-container {
            padding: 10px 15px;
            border-bottom: 1px solid var(--bordersColor);
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--bordersColor);
            border-radius: 20px;
            font-family: 'PT Sans', sans-serif;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--linksHoverColor);
        }

        .conversation-tabs {
            display: flex;
            border-bottom: 1px solid var(--bordersColor);
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .tab.active {
            border-bottom: 2px solid var(--linksHoverColor);
            color: var(--linksHoverColor);
        }

        .tab:hover {
            background-color: var(--backgroundColor);
        }

        .conversation-item {
            padding: 12px 15px;
            border-bottom: 1px solid var(--bordersColor);
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: flex-start;
        }

        .conversation-item:hover {
            background-color: var(--backgroundColor);
        }

        .unread {
            background-color: var(--unread-bg);
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
            flex-shrink: 0;
            font-family: 'PT Sans', sans-serif;
        }

        .conversation-info {
            flex-grow: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .sender-name {
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-time {
            color: #666;
            font-size: 12px;
            white-space: nowrap;
            margin-left: 5px;
        }

        .message-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .message-preview {
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 14px;
            font-family: 'PT Sans', sans-serif;
        }

        /* Message view */
        .message-view {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            background-color: white;
        }

        .message-view-header {
            padding: 15px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            align-items: center;
        }

        .sender-info {
            margin-left: 12px;
        }

        .sender-info h3 {
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        .sender-info p {
            color: #666;
            font-size: 14px;
            margin: 0;
            font-family: 'PT Sans', sans-serif;
        }

        .message-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message-title-view {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .message-body {
            line-height: 1.6;
            margin-bottom: 20px;
            font-family: 'PT Sans', sans-serif;
        }

        .message-timestamp {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }

        .compose-message {
            padding: 15px;
            border-top: 1px solid var(--bordersColor);
        }

        .compose-message textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            resize: none;
            font-size: 14px;
            height: 80px;
            font-family: 'PT Sans', sans-serif;
        }

        .compose-message textarea:focus {
            outline: none;
            border-color: var(--linksHoverColor);
        }

        .compose-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .send-btn {
            background-color: var(--primary-color);
            color: var(--bodyTextColor);
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .send-btn:hover {
            background-color: var(--linksHoverColor);
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
            padding: 15px;
            border-bottom: 1px solid var(--bordersColor);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--bordersColor);
            border-radius: 5px;
            font-size: 14px;
            font-family: 'PT Sans', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--linksHoverColor);
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
            padding: 8px 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'PT Sans', sans-serif;
        }

        .user-suggestion:hover {
            background-color: var(--backgroundColor);
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid var(--bordersColor);
            display: flex;
            justify-content: flex-end;
        }

        /* Placeholder for no messages selected */
        .no-message-selected {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            background-color: var(--backgroundColor);
            color: var(--bodyTextColor);
            padding: 20px;
            text-align: center;
        }

        .no-message-selected h2 {
            margin-bottom: 10px;
        }

        .no-message-selected p {
            margin-bottom: 20px;
            font-family: 'PT Sans', sans-serif;
        }

        /* Empty state styles */
        .empty-state {
            padding: 30px 20px;
            text-align: center;
            color: var(--bodyTextColor);
            font-family: 'PT Sans', sans-serif;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            #main-body-wrapper {
                width: 95vw;
                padding: 10px;
            }

            .messages-container {
                flex-direction: column;
            }

            .conversations-list {
                width: 100%;
                max-height: 300px;
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
                <!-- Conversations list -->
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
                </div>
                <div class="message-view">
                    <div class="message-view-header">
                        <div class="sender-info">
                            <h3>Sender Name</h3>
                            <p>Sender Email</p>
                        </div>
                    </div>
                    <div class="message-content">
                        <div class="message-title-view">Message Title</div>
                        <div class="message-body">This is the body of the message.</div>
                        <div class="message-timestamp">Timestamp</div>
                    </div>
                    <div class="compose-message">
                        <textarea placeholder="Write your reply..."></textarea>
                        <div class="compose-actions">
                            <button class="send-btn">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
