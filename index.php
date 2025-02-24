<?php $currentPage = 'Home'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLocal - Home</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="hero-section">
        <h1>Welcome to CareLocal</h1>
        <p>Where Local Talent Meets Local Needs</p>
    </div>

    <div class="features-grid">
        <div class="feature-card">
            <h2>Find Jobs</h2>
            <p>Discover opportunities in your local community</p>
        </div>
        <div class="feature-card">
            <h2>Post Jobs</h2>
            <p>Share tasks and find skilled helpers nearby</p>
        </div>
        <div class="feature-card">
            <h2>Build Community</h2>
            <p>Connect with neighbors and make a difference</p>
        </div>
    </div>

    <div class="cta-section">
        <h2>Ready to Get Started?</h2>
        <p>Join our community today and start making a difference in your neighborhood</p>
        <a href="add-job.php" class="cta-button">Post a Job</a>
        <a href="search-jobs.php" class="cta-button">Search for Jobs</a>
    </div>

</body>
</html>
