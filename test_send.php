<?php
require_once 'inc/database.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<html>
<body>
    <form action="inc/sendMessage.php" method="POST">
        <input type="hidden" name="sender_id" value="<?php echo $_SESSION['user_id']; ?>">
        <input type="hidden" id="receiver_id" name="receiver_id" value="">
        <labl for="search">Recipient:</label>
        <input type="text" id="search" autocomplete="off">
        <div id="autocomplete-list"></div>
        <textarea name="message" required></textarea>
        <button type="submit">Send</button>
    </form>
    <script>
        const userSearchInput = document.getElementById('search');
        const autocompleteList = document.getElementById('autocomplete-list');
        const receiverIdHidden  = document.getElementById('receiver_id');
        let debounceTimer = null;
        userSearchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = userSearchInput.value.trim();
            if (query.length < 1) {
            autocompleteList.innerHTML = '';
            return;
            }
            fetch(`inc/searchUsers.php?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                autocompleteList.innerHTML = '';

                data.forEach(user => {
                const item = document.createElement('div');
                item.classList.add('autocomplete-item');
                item.textContent = user.username;
                
                item.addEventListener('click', () => {
                    userSearchInput.value = user.username;
                    receiverIdHidden.value = user.id;
                    autocompleteList.innerHTML = '';
                });

                autocompleteList.appendChild(item);
                });
            })
            });
        }, 300); 

        document.addEventListener('click', function(e) {
        if (e.target !== userSearchInput) {
            autocompleteList.innerHTML = '';
        }
        });
    </script>
</body>
</html>