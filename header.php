<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="top-nav">
    <button class="menu-btn" onclick="toggleMenu()">
        <span id="current-page">Help Center</span> ☰
    </button>
</nav>

<!-- Fullscreen Menu -->
<div class="fullscreen-menu" id="menu">
    <div class="menu-content">
        <div class="menu-left">
            <h3>Menu</h3>
            <ul>
                <li><a href="index.php" onclick="setPage('Home')">Home</a></li>
                <li><a href="cart.php" onclick="setPage('Cart')">Cart</a></li>
                <li><a href="add-job.php" onclick="setPage('Add Job')">Add Job</a></li>
                <li><a href="search-jobs.php" onclick="setPage('Search Jobs')">Search Jobs</a></li>
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

