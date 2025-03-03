<?php session_start(); ?>
<!-- navbar.php -->
<style>
    .menu-content h1, .menu-content p {
        color: black;
    }
</style>

<nav class="top-nav">
    <button class="menu-btn" onclick="toggleMenu()">
        <span id="current-page"><?php echo isset($currentPage) ? htmlspecialchars($currentPage) : 'Home'; ?></span> ☰
    </button>
    
    <?php
        if (!isset($_SESSION['username'])) {
            echo "<button class=\"menu-btn\" onclick=\"toggleLogin()\"><span>Login</span></button>";
        } else {
            echo "<button class=\"menu-btn\" ><span onclick=\"location.href='logout.php'\">Logout : " . htmlspecialchars($_SESSION['username']) . "</span></button>";
        }
    ?>
    
    <!--
    <button class="menu-btn">
        <span id="login">
            #<?php
            #    if (!isset($_SESSION['username'])) {
            #         echo "<span onclick=\"location.href='login.php'\">Login</span";
            #     } else {
            #        echo "<span onclick=\"location.href='logout.php'\">Logout : " . $_SESSION['username'] . "</span>";
            #    }
            #?>
        </span>
    </button>
    -->
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
