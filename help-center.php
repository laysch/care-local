<?php $currentPage = 'Help Center'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>
<body>

    <!-- Include the Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Help Center Header -->
    <header>
        <h1>Help Center</h1>
        <p>How Can We Help You?</p>
    </header>

    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Search FAQs..." oninput="searchFAQs()">
        <button class="filter-btn">⚙</button>
    </div>

    <!-- FAQ Categories -->
    <div class="categories">
        <button class="category-btn active" onclick="filterFAQs('General')">General</button>
        <button class="category-btn" onclick="filterFAQs('Account')">Account</button>
        <button class="category-btn" onclick="filterFAQs('Services')">Services</button>
    </div>

    <!-- FAQ Sections (Initially Hidden, based on category) -->
    <section id="general-content" class="faq-content">
        <h3>General FAQs</h3>
        <div class="faq-item">
            <h3>What is CareLocal?</h3>
            <p>CareLocal connects community members to organize tasks and resources efficiently.</p>
        </div>
        <div class="faq-item">
            <h3>How do I use the platform?</h3>
            <p>Sign up, create a task, and collaborate with others in your local community.</p>
        </div>
    </section>

    <section id="account-content" class="faq-content" style="display:none;">
        <h3>Account FAQs</h3>
        <div class="faq-item">
            <h3>How do I update my profile?</h3>
            <p>Go to your profile page and click "Edit Profile" to update your information.</p>
        </div>
        <div class="faq-item">
            <h3>How do I change my password?</h3>
            <p>Go to the settings page and click "Change Password".</p>
        </div>
    </section>

    <section id="services-content" class="faq-content" style="display:none;">
        <h3>Services FAQs</h3>
        <div class="faq-item">
            <h3>What services do you offer?</h3>
            <p>We provide task management, job posting, and resource sharing within local communities.</p>
        </div>
        <div class="faq-item">
            <h3>How do I request a service?</h3>
            <p>Click on "Add Job" and fill out the necessary details for the service you need.</p>
        </div>
    </section>

</body>
</html>
