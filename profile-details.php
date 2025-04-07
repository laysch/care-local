<?php
session_start();

require_once 'inc/database.php';
include_once 'inc/func.php';
$currentUserId = $_SESSION['user_id'] ?? null;



$user_id = $_GET['id'];


$query = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}


$user = $result->fetch_assoc();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | CareLocal</title>
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
            --backgroundColor: #fff;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
            --accentColor: #cdd8c4;
            --profileBgColor: #fff5e6;
            --cardBgColor: #f4f8f4;
            --buttonColor: #cdd8c4;
            --buttonHoverColor: #b9cfa6;
        }

        body {
            background-color: var(--backgroundColor);
            font-family: var(--bodyFontFamily);
            margin: 0;
            padding: 0;
        }

        #container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        #sidebar {
            width: 250px;
            margin-right: 20px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
        }

        #sidebar img {
            width: 100%;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        #sidebar .title-text {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        #sidebar nav a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #sidebar nav a:hover {
            background-color: var(--accentColor);
            color: white;
        }

        #main-body-wrapper {
            width: 80vw;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .profile-header h1 {
            font-size: 2em;
            margin: 0;
            color: #333;
        }

        .profile-header p {
            font-size: 1.1em;
            color: var(--bodyTextColor);
            margin-top: 5px;
        }

        .bio, .skills {
            background-color: var(--cardBgColor);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .bio h2, .skills h2 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
        }

        .bio p, .skills ul li {
            font-size: 1em;
            color: var(--bodyTextColor);
            line-height: 1.6;
        }

        .skills ul {
            list-style: none;
            padding: 0;
        }

        .skills ul li {
            font-size: 1.1em;
            color: var(--bodyTextColor);
            margin-bottom: 10px;
        }
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center; /* Center buttons horizontally */
            gap: 20px; /* Space between buttons */
            padding-bottom: 20px;
        }
    
        
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>User Details</h1>
            <p>Below are the details for the selected user.</p>
        </section>

        <div class="user-details">
            
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <!-- Skills Section -->
            <div class="skills">
                <h2>Skills</h2>
                <ul>
                    <?php if (!empty($user['skills'])): ?>
                        <?php foreach ($skills as $skill): ?>
                            <li><?php echo htmlspecialchars($skill); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No skills added yet.</li>
                    <?php endif; ?>
                </ul>
            </div>



        
            

            <div class="button-container">
                

              
                <button class="btn"><a href="search-user.php" >Back to Users</a></button>
                <button class="btn"><a href="messages.php?recipient_id=<?php echo $username; ?>&recipient_name=<?php echo urlencode($username); ?>&title=RE+<?php echo urlencode($user['username']); ?>#sendMessageForm">
                    Send a message to <?php echo htmlspecialchars($username); ?>
                </a></button>
            </div>