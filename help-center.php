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
        <div class="faq-item">
            <h3>Is CareLocal free to use?</h3>
            <p>Yes, the basic features are free. Premium options are available for additional benefits.</p>
        </div>
        <div class="faq-item">
            <h3>How can I invite friends to join?</h3>
            <p>You can send invites through email or share your referral link in the app.</p>
        </div>
        <div class="faq-item">
            <h3>What communities does CareLocal support?</h3>
            <p>We support a variety of local communities, including neighborhoods, schools, and workplaces.</p>
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
        <div class="faq-item">
            <h3>How do I delete my account?</h3>
            <p>Contact our support team through the settings page to request account deletion.</p>
        </div>
        <div class="faq-item">
            <h3>Can I recover a deleted account?</h3>
            <p>Account recovery is possible within 30 days of deletion. Contact support for assistance.</p>
        </div>
        <div class="faq-item">
            <h3>How do I manage my notification settings?</h3>
            <p>Go to Settings > Notifications to customize your alerts and email preferences.</p>
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
        <div class="faq-item">
            <h3>Are there any fees for requesting services?</h3>
            <p>Basic services are free, but premium listings may have a small fee.</p>
        </div>
        <div class="faq-item">
            <h3>How do I offer my services to others?</h3>
            <p>Go to the "Offer Services" section and create a listing describing your skills and availability.</p>
        </div>
        <div class="faq-item">
            <h3>How can I ensure safe transactions?</h3>
            <p>Always use the platform's built-in payment and messaging system to ensure secure interactions.</p>
        </div>
        <div class="faq-item">
            <h3>What job filters are available?</h3>
            <p>You can filter jobs based on job characteristics, required soft skills, and county (Nassau or Suffolk).</p>
        </div>
        <div class="faq-item">
            <h3>How can I view available jobs?</h3>
            <p>You can click on "View Jobs" in the navigation bar to see all available listings.</p>
        </div>
        <div class="faq-item">
            <h3>Can I post a job outside of New York?</h3>
            <p>No, CareLocal is specifically designed for job postings within Long Island.</p>
        </div>
    </section>

</body>
</html>
