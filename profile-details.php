<?php
require_once 'inc/session.php';
require_once 'inc/database.php';
include_once 'inc/func.php';


$user_id = $_GET['id'];

if ($userId == $user_id) {
    header("Location: profile.php");
    exit();
}

$query = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$user = $result->fetch_assoc();

$avgRatingQuery = "SELECT AVG(rating) AS avg_rating, COUNT(DISTINCT rater_user_id) AS total_raters FROM ratings WHERE rated_user_id = ?";
$stmt = $conn->prepare($avgRatingQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$avgResult = $stmt->get_result();
$avgRatingRow = $avgResult->fetch_assoc();
$averageRating = $avgRatingRow['avg_rating'] ?? 0;
$totalRaters = $avgRatingRow['total_raters'] ?? 0;
$averageRating = round($averageRating, 2);
$stmt->close();

$countQuery = "SELECT COUNT(DISTINCT rater_user_id) as total_ratings FROM ratings WHERE rated_user_id = ?";
$stmt = $conn->prepare($countQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$countResult = $stmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalRatings = $countRow['total_ratings'] ?? 0;
$stmt->close();
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
        .rating {
    background-color: var(--cardBgColor);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.star-rating {
    font-size: 2em;
    color: #ccc;
    cursor: pointer;
    user-select: none;
}

.star-rating .star.selected,
.star-rating .star.locked {
    color: gold;
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
        <p><strong>Skills Required:</strong> <?php echo htmlspecialchars($job['skills']); ?></p>
        <h1>
    <?php echo htmlspecialchars($user['username']); ?>
    <small style="font-size: 0.6em; color: #666;">
        (Avg. Rating: <?php echo $averageRating; ?> ★)
        <p>Total Ratings: <?php echo $totalRatings; ?> people rated this user.</p>
    </small>
</h1>
            
            <div class="rating">
    <h2>Rate this User</h2>
    <form action="submit-rating.php" method="post">
        <div class="star-rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <input type="hidden" name="rating" id="rating-input" value="">
        <input type="hidden" name="rated_user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
        <input type="hidden" name="rater_user_id" value="<?php echo htmlspecialchars($userId); ?>">
        <button type="submit" class="btn">Submit Rating</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating-input');

    stars.forEach(star => {
        star.addEventListener('mouseover', function () {
            if (!document.querySelector('.star.locked')) {
                resetStars();
                highlightStars(this.dataset.value);
            }
        });

        star.addEventListener('mouseout', function () {
            if (!document.querySelector('.star.locked')) {
                resetStars();
            }
        });

        star.addEventListener('click', function () {
            ratingInput.value = this.dataset.value;
            lockStars(this.dataset.value);
        });
    });

    function highlightStars(rating) {
        stars.forEach(star => {
            if (parseInt(star.dataset.value) <= parseInt(rating)) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }

    function resetStars() {
        stars.forEach(star => {
            if (!star.classList.contains('locked')) {
                star.classList.remove('selected');
            }
        });
    }

    function lockStars(rating) {
        stars.forEach(star => {
            star.classList.remove('locked'); // remove previous locks
            if (parseInt(star.dataset.value) <= parseInt(rating)) {
                star.classList.add('selected', 'locked');
            } else {
                star.classList.remove('selected', 'locked');
            }
        });
    }
});
</script>
        
            

            <div class="button-container">
                

              
                <button class="btn"><a href="search-user.php" >Back to Users</a></button>
                <button class="btn"><a href="messages.php?recipient_id=<?php echo $user['username']; ?>&recipient_name=<?php echo urlencode($user['username']); ?>&title=RE+<?php echo urlencode($user['username']); ?>#sendMessageForm">
                    Send a message to <?php echo htmlspecialchars($user['username']); ?>
                </a></button>
            </div>

            </body>
