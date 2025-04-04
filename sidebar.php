<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'sidebar.php';
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'inc/database.php';
include_once 'inc/func.php';

$unreadMessageCount = 0;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userName = $_SESSION['username'];

    $unreadMessageCount = getUnreadMessagesCount($conn, $userId);
}
?>
<style>
.search-results {
    position: absolute;
    background: white;
    width: 100%;
    max-width: 400px;
    border: 1px solid #ccc;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    margin-top: 5px; /* Adds spacing below search bar */
    z-index: 1000;
    padding: 5px;
    top: 40px; /* Positions it right below the search box */
    left: 0;
    display: none;
}

.search-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    white-space: nowrap;  
}

.search-item:last-child {
    border-bottom: none;
}

.search-item a {
    text-decoration: none;
    color: #333;
    display: block;
}

.search-item:hover {
    background: #f3f3f3;
}

#searchbar {
    position: relative;
    display: block;
    width: 100%;
}
</style>

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
            <form action="/search.php" method="get" id="searchbar">
                <input type="text" name="q" class="searchquery" placeholder="Search..." autocomplete="off">
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
            <a href="/job-cart.php">Job Cart</a>
            <a href="/calendar.php">Calendar</a>
            <a href="/help-center.php">Help Center</a>
            <?php 
                if (isset($_SESSION['username'])) {
                    echo "<a href=\"/profile.php\">" . $userName ."'s Profile</a>";
                    echo "<a href=\"/messages.php\">Inbox</a>";
                    echo "<a href=\"/logout.php\">log out</a>";
                } else {
                    echo "<a href=\"/login.php\">Login</a>";
                }
            ?>            
        </nav>
    </div>
</aside>
<script src="scripts/search.js" defer></script>