<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Care Local Messaging</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="messaging-container">
    <aside class="inbox">
      <h2>Messages</h2>
      <ul id="conversation-list">
        <!-- Conversations dynamically inserted here -->
      </ul>
    </aside>
    <section class="chat-window">
      <header class="chat-header">
        <span id="chat-user">Select a conversation</span>
        <span id="user-status" class="status offline"></span>
      </header>
      <div id="message-thread" class="messages">
        <!-- Messages dynamically inserted here -->
      </div>
      <form id="message-form">
        <input type="text" id="message-input" placeholder="Type a message..." required />
        <button type="submit">Send</button>
      </form>
    </section>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const conversationList = document.getElementById("conversation-list");
      const messageThread = document.getElementById("message-thread");
      const chatUser = document.getElementById("chat-user");
      const userStatus = document.getElementById("user-status");
      const messageForm = document.getElementById("message-form");
      const messageInput = document.getElementById("message-input");

      let currentUser = null;

      function loadConversations() {
        fetch("get_conversations.php")
          .then((res) => res.json())
          .then((data) => {
            conversationList.innerHTML = "";
            data.forEach((user) => {
              const li = document.createElement("li");
              li.textContent = user.username;
              li.dataset.user = user.username;
              li.dataset.userid = user.userid;
              li.addEventListener("click", () => {
                currentUser = user;
                loadMessages(user.userid);
              });
              conversationList.appendChild(li);
            });
          });
      }

      function loadMessages(userId) {
        fetch(`get_messages.php?user_id=${userId}`)
          .then((res) => res.json())
          .then((data) => {
            chatUser.textContent = currentUser.username;
            userStatus.className = `status ${currentUser.status}`;
            messageThread.innerHTML = "";
            data.forEach((msg) => {
              const div = document.createElement("div");
              div.className = `message ${msg.sender === "me" ? "sent" : "received"}`;
              div.textContent = msg.text + (msg.read ? " ✅" : " ❌");
              messageThread.appendChild(div);
            });
          });
      }

      messageForm.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!currentUser) return;
        const msg = messageInput.value.trim();
        if (!msg) return;
        fetch("send_message.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ to: currentUser.userid, text: msg }),
        })
          .then((res) => res.json())
          .then((response) => {
            if (response.success) {
              loadMessages(currentUser.userid);
              messageInput.value = "";
            }
          });
      });

      loadConversations();
    });
  </script>
</body>
</html>
