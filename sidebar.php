<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'inc/database.php';
include 'inc/func.php';

$unreadMessageCount = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['username'];

    $unreadMessageCount = getUnreadMessagesCount($conn, $userId);
}
?>

<aside id="sidebar" style="text-align: center;">
    <div id="sb-image">
        <svg id="svg_circle_text" viewBox="0 0 180 180">
            <text><textPath xlink:href="#circleTextPath">CareLocal</textPath></text>
        </svg>
        <svg id="svg_circle_line" viewBox="0 0 180 180">
            <use xlink:href="#circleLinePath"/>
        </svg>
        <a href="/" class="sbimage">
            <img src="/img/favicon.png" alt="CareLocal">
        </a>
    </div>
    <div id="sb-title">
        <div class="title-text"><b>Welcome to CareLocal</b></div>
        <div class="title-tail"></div>
    </div>
    <div id="sb-infobox" class="bothdisplay has--desc">
        <input type="checkbox" id="menutoggle" name="menutoggle">
        <div class="ib-toolbar">
            <a href="/" class="home_button">
                <i class="fi fi-rr-home"></i>
            </a>
            <a href="/messages.php" class="mail_button">
                <?php if ($unreadMessageCount > 0) {
                    echo "<i class=\"fi fi-rr-envelope-dot\" style=\"color: red;\"></i>";
                } else {
                    echo "<i class=\"fi fi-rr-envelope\"></i>";
                } ?>
            </a>
            <form action="/search" method="get" id="searchbar">
                <input type="text" name="q" class="searchquery" placeholder="Search...">
            </form>
            <label for="menutoggle" class="menu_button">
                <i class="fi fi-rr-menu-burger"></i>
                <i class="fi fi-rr-user"></i>
            </label>
        </div>
        <div id="desc">
            <div class="desc-inner">Where Local Talent Meets Local Needs</div>
        </div>
        <nav id="menu">
            <a href="/">Home</a>            
            <a href="/add-job.php">Add Job</a>
            <a href="/search-jobs.php">Search Jobs</a>
            <a href="/calendar.php">Calendar</a>
            <a href="/help-center.php">Help Center</a>
            <?php 
                if (isset($_SESSION['username'])) {
                    echo "<a href=\"/profile.php\">" . $userName ."'s Profile</a>";
                    echo "<a href=\"/logout.php\">log out</a>";
                } else {
                    echo "<a href=\"/login.php\">Login</a>";
                }
            ?>
            
        </nav>
    </div>
</aside>
