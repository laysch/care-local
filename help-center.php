<?php $currentPage = 'Help Center'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #f9eedd;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
        }

        body {
            background-image: url('https://example.com/background.jpg');
            background-attachment: fixed;
            background-repeat: repeat;
        }

        #main-body-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hero {
            text-align: center;
            padding: 50px 20px;
        }

        .hero h1 {
            font-size: 2.5em;
            color: var(--headingsColor);
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            color: var(--bodyTextColor);
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .cta-buttons a {
            background-color: var(--accent1BgColor);
            color: var(--accent1TextColor);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .categories {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .category-btn {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .category-btn.active {
            background-color: #efac9a;
        }

        .faq-content {
            display: none;
            margin-top: 30px;
        }

        .faq-item {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .faq-item h3 {
            font-size: 1.5em;
            color: var(--headingsColor);
            margin-bottom: 10px;
        }

        .faq-item p {
            font-size: 1em;
            color: var(--bodyTextColor);
        }
    </style>
</head>
<body class="has--boxshadow" data-shape="circle" data-body-font-family="Share Tech Mono" data-body-font-size="14px" data-sidebar-position="left" data-pagination-display="mssg">
    <div id="container">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Body -->
        <div id="main-body-wrapper">
            <section class="hero">
                <h1>Help Center</h1>
                <p>How Can We Help You?</p>
            </section>

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

            <!-- FAQ Sections -->
            <section id="general-content" class="faq-content" style="display:block;">
                <h3>General FAQs</h3>
                <div class="faq-item">
                    <h3>What is CareLocal?</h3>
                    <p>CareLocal connects community members to organize tasks and resources efficiently.</p>
                </div>
                <!-- More FAQs... -->
            </section>

            <section id="account-content" class="faq-content">
                <h3>Account FAQs</h3>
                <div class="faq-item">
                    <h3>How do I update my profile?</h3>
                    <p>Go to your profile page and click "Edit Profile" to update your information.</p>
                </div>
                <!-- More FAQs... -->
            </section>

            <section id="services-content" class="faq-content">
                <h3>Services FAQs</h3>
                <div class="faq-item">
                    <h3>What services do you offer?</h3>
                    <p>We provide task management, job posting, and resource sharing within local communities.</p>
                </div>
                <!-- More FAQs... -->
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>
</html>

