<?php 
session_start();
require_once 'inc/database.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // avatar
    $query = "SELECT avatar FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $avatar = $user['avatar'];

    // unread message count
    $unreadCount = 0;
    $query = "SELECT COUNT(*) as cnt FROM messages WHERE receiver_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $unreadCount = (int)$row['cnt'];
    }
}
?>
<!-- navbar.php -->
<style>
    .menu-content h1, .menu-content p {
        color: black;
    }
    .avatar-container {
        position: relative; /* So the badge can be positioned absolutely within it */
        display: inline-block; /* Keep the avatar + badge together */
        margin-right: 15px;    /* Some spacing if needed */
    }
    .avatar {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid #5D674C; 
    }
    .unread-badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 2px 2px;
        font-size: 0.75rem; /* Adjust size as needed */
        font-weight: bold;
        min-width: 20px;
        text-align: center;
    }
</style>

<nav class="top-nav">
    <button class="menu-btn" onclick="toggleMenu()">
        <span id="current-page"><?php echo isset($currentPage) ? htmlspecialchars($currentPage) : 'Home'; ?></span> ☰
    </button>
    <div class="avatar-container">
    <?php
        if (!isset($_SESSION['username'])) {
            echo "<button class=\"menu-btn\" onclick=\"toggleLogin()\"><span>Login</span></button>";            
        } else {
            echo "<button class=\"menu-btn\" ><span onclick=\"location.href='logout.php'\"><img src=\"img/avatar/" .htmlspecialchars($avatar) ."\" alt=\"User Avatar\" class=\"avatar\"></span></button>";
            if ($unreadCount > 0){
                echo "<span class=\"unread-badge\">" . $unreadCount . "</span>";
            }
        }
    ?>
    </div>
</nav>

<div class="fullscreen-menu" id="menu">
    <div class="menu-content">
        <div class="menu-left">
            <h1>Menu</h1>
            <ul>
                <li><a href="index.php" onclick="setPage('Home')">Home</a></li>
                <li><a href="add-job.php" onclick="setPage('Add Job')">Add Job</a></li>
                <li><a href="search-jobs.php" onclick="setPage('Search Jobs')">Search Jobs</a></li>
                <li><a href="calendar.php" onclick="setPage('Calendar')">Calendar</a></li>
                <li><a href="help-center.php" onclick="setPage('Help Center')">Help Center</a></li>
                    <?php
                        if (!isset($_SESSION['username'])) {
                            echo "<li><a href=\"#\" onclick=\"toggleMenu();toggleLogin();return false;\">Login</a></span></li>";
                        } else {
                            echo "<li><a href=\"profile.php\">My Profile</a></li>";
                            echo "<li><a href=\"logout.php\">Logout : " . $_SESSION['username'] . "</a></li>";
                        }
                    ?>
            </ul>
        </div>
        <div class="menu-right">
            <h1>Contact Us</h1>
            <p>Email: <a href="mailto:support@carelocal.com">support@carelocal.com</a></p>
        </div>
    </div>
    <button class="close-btn" onclick="toggleMenu()">✖</button>
</div>

<div class="fullscreen-menu" id="login">
    <embed src="login.php" style="height: 100vh; width: 100vw; display:flex;" frameborder="0"></embed>
    <button class="close-btn" onclick="toggleLogin()">✖</button>
</div>
