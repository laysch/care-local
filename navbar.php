<!-- navbar.php -->
<nav class="top-nav">
    <button class="menu-btn" onclick="toggleMenu()">
        <span id="current-page"><?php echo isset($currentPage) ? $currentPage : 'Home'; ?></span> ☰
    </button>
</nav>

<div class="fullscreen-menu" id="menu">
    <div class="menu-content">
        <div class="menu-left">
            <h3>Menu</h3>
            <ul>
                <li><a href="index.php" onclick="setPage('Home')">Home</a></li>
                <li><a href="calendar.php" onclick="setPage('My Calendar')">My Calendar</a></li>
                <li><a href="cart.php" onclick="setPage('Cart')">Cart</a></li>
                <li><a href="add-job.php" onclick="setPage('Add Job')">Add Job</a></li>
                <li><a href="profile.php" onclick="setPage('Profile')">Profile</a></li>
                <li><a href="help-center.php" onclick="setPage('Help Center')">Help Center</a></li>
            </ul>
        </div>
        <div class="menu-right">
            <h3>Contact Us</h3>
            <p>Email: <a href="mailto:support@carelocal.com">support@carelocal.com</a></p>
            <p>Phone: <a href="tel:+1234567890">+1 (234) 567-890</a></p>
        </div>
    </div>
    <button class="close-btn" onclick="toggleMenu()">✖</button>
</div>
