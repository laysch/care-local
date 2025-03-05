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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $receiverId = intval($_POST['receiver_id']);
    $messageContent = trim($_POST['message']);

    if (!empty($messageContent)) { 
        $status = sendMessage($conn, $userId, $receiverId, $messageContent);
        echo json_encode(['status' => $status ? 'success' : 'error']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid message']);
        exit();
    }
}
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
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("receiverSearch");
            const suggestionsContainer = document.getElementById("userSuggestions");
            const receiverIdInput = document.getElementById("receiverId");

            searchInput.addEventListener("input", function () {
                let query = searchInput.value.trim();
                if (query.length < 2) {
                    suggestionsContainer.innerHTML = "";
                    return;
                }

                fetch("../inc/searchUsers.php?search=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(users => {
                        suggestionsContainer.innerHTML = "";
                        users.forEach(user => {
                            let suggestion = document.createElement("div");
                            suggestion.classList.add("user-suggestion");
                            suggestion.textContent = user.username;
                            suggestion.dataset.userId = user.id;

                            suggestion.addEventListener("click", function () {
                                searchInput.value = this.textContent;
                                receiverIdInput.value = this.dataset.userId;
                                suggestionsContainer.innerHTML = "";
                            });

                            suggestionsContainer.appendChild(suggestion);
                        });
                    })
                    .catch(error => console.error("Error fetching users:", error));
            });

            
            const form = document.getElementById("sendMessageForm");

                form.addEventListener("submit", function (event) {
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
                        console.log("Server Response:", data); 
                        if (data.status === "success") {
                            alert("Message sent successfully!");
                            window.location.reload(); 
                        } else {
                            alert("Failed to send message: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Failed to send message. Network error.");
                    });
                });
            });

    </script>
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #ffffff; /* White background */
            --bordersColor: #e0e0e0; /* Light gray borders */
            --bodyTextColor: #333333; /* Dark gray text */
            --linksColor: #222222;
            --linksHoverColor: #cdd8c4;
        }

        body {
            background-color: var(--backgroundColor); /* White background */
            font-family: var(--bodyFontFamily);
            color: var(--bodyTextColor);
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background-color: #fff; /* Light gray background */
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        #sidebar a {
            color: #000000; /* Black text for sidebar links */
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        #sidebar a:hover {
            background-color: #fff; 
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
                    <a href="#sendMessageForm" class="jump-to-send">Send Message</a>
                </div>
                <div class="messages-section">
                    <div class="messages-header">Received Messages</div>
                    <?php if (empty($receivedMessages)) {
                        echo "<p>Lets connect with your community members today!</p>";
                    } else {
                        echo "<ul class=\"messages-list\">";
                        foreach ($receivedMessages as $msg) {
                            $messageClass = $msg['is_read'] == 0 ? "message-item-unread" : "message-item-read";
                            echo "<i class=\"message-item $messageClass\" data-message-id=\"" . htmlspecialchars($msg['message_id']) . "\">";
                            echo "<strong>" . htmlspecialchars($msg['sender_username']) . "</strong> to 
                                <strong>" . htmlspecialchars($msg['receiver_username']) . "</strong>";
                            echo "<p>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
                            echo "<small>" . date("F j, Y, g:i a", strtotime($msg['timestamp'])) . "</small>";
                            echo "</i>";
                        }
                        echo "</ul>";
                    } ?>              
                </div>
                <div class="messages-section">
                    <div class="messages-header">Sent Messages</div>
                    <?php if (empty($sentMessages)) {
                        echo "<p>Lets connect with your community members today!</p>";
                    } else {
                        echo "<ul class=\"messages-list\">";
                        foreach ($sentMessages as $msg) {
                            echo "<i class=\"message-item-read\">";
                            echo "<strong>You</strong> to <strong>" . htmlspecialchars($msg['receiver_username']) . "</strong>";
                            echo "<p>" . nl2br(htmlspecialchars($msg['message'])) . "</p>";
                            echo "<small>" . date("F j, Y, g:i a", strtotime($msg['timestamp'])) . "</small>";
                            echo "</i>";
                        }
                        echo "</ul>";
                    } ?>
                </div>
                <div class="messages-section">
                    <div class="messages-header">Send a Message</div>
                    <form id="sendMessageForm">
                        <label for="receiverSearch">Send to:</label>
                        <input type="text" id="receiverSearch" placeholder="Search user..." autocomplete="off">
                        <input type="hidden" id="receiverId" name="receiver_id"> <!-- Stores selected user ID -->
                        <div id="userSuggestions"></div> <!-- Autocomplete results will appear here -->

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
