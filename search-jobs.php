<?php $currentPage = 'Search Jobs'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Jobs - CareLocal</title>
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
            --accent1BgColor: #5D674C;
            --accent1TextColor: white;
            --headingsColor: #222222;
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
            background-color: var(--accent1BgColor);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .category-btn.active {
            background-color: #efac9a;
        }

        .search-bar {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .search-bar input {
            padding: 10px;
            width: 60%;
            border-radius: 5px;
            border: 1px solid var(--bordersColor);
        }

        .search-bar button {
            background-color: var(--accent1BgColor);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .job-listing {
            margin-top: 30px;
        }

        .job-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }

        .job-item h3 {
            font-size: 1.5em;
            color: var(--headingsColor);
            margin-bottom: 10px;
        }

        .job-item p {
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
                <h1>Search Jobs</h1>
                <p>Find Your Next Opportunity</p>
            </section>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Search jobs..." oninput="searchJobs()">
                <button class="filter-btn">⚙</button>
            </div>

            <!-- Job Listings -->
            <section class="job-listing">
                <div class="job-item">
                    <h3>Software Engineer</h3>
                    <p>Location: Remote | Full-time</p>
                    <p>We are looking for a talented software engineer to join our growing team.</p>
                </div>
                <div class="job-item">
                    <h3>Data Analyst</h3>
                    <p>Location: New York | Part-time</p>
                    <p>Join our team to help us make data-driven decisions and improve our systems.</p>
                </div>
                <!-- More job listings... -->
            </section>
        </div>
    </div>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>
</html>


