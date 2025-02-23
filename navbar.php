<?php session_start(); ?>
<!-- navbar.php -->
<style>
    .menu-content h1, .menu-content p {
        color: black;
    }
</style>

<nav class="top-nav">
    <button class="menu-btn" onclick="toggleMenu()">
        <span id="current-page"><?php echo isset($currentPage) ? $currentPage : 'Home'; ?></span> ☰
    </button>
    <button class="menu-btn">
        <span id="login">
            <?php
                if (!isset($_SESSION['username'])) {
                    echo "<span onclick=\"location.href='login.php'\">Login</span";
                } else {
                    echo "<span onclick=\"location.href='logout.php'\">Logout : " . $_SESSION['username'] . "</span>";
                }
            ?>
        </span>
    </button>
</nav>

<div class="fullscreen-menu" id="menu">
    <div class="menu-content">
        <div class="menu-left">
            <h1>Menu</h1>
            <ul>
                <li><a href="index.php" onclick="setPage('Home')">Home</a></li>
                <li><a href="calendar.php" onclick="setPage('Calendar')">Calendar</a></li>
                <li><a href="add-job.php" onclick="setPage('Add Job')">Add Job</a></li>
                <li><a href="profile.php" onclick="setPage('Profile')">Profile</a></li>
                <li><a href="help-center.php" onclick="setPage('Help Center')">Help Center</a></li>
                <li>
                    <?php
                        if (!isset($_SESSION['username'])) {
                            echo "<a href=\"login.php\" onclick=\"setPage('Login'\")>Login</a>";
                        } else {
                            echo "<a href=\"logout.php\" onclick=\"setPage('Logout')\">Logout : " . $_SESSION['username'] . "</a>";
                        }
                    ?>
                </li>
            </ul>
        </div>
        <div class="menu-right">
            <h1>Contact Us</h1>
            <p>Email: <a href="mailto:support@carelocal.com">support@carelocal.com</a></p>
        </div>
    </div>
    <button class="close-btn" onclick="toggleMenu()">✖</button>
</div>
