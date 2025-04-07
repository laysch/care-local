<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php");

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
} else {
    echo "No user ID provided.";
    exit();
}

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle rating
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rating'])) {
    $rating = $_POST['rating'];

    $stmt = $conn->prepare("INSERT INTO ratings (rater_id, rated_id, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $_SESSION['user_id'], $user_id, $rating);
    $stmt->execute();
}

// Get average rating
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE rated_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rating_result = $stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$avg_rating = $rating_data['avg_rating'];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #cdd8c4;
            font-family: 'Share Tech Mono', monospace;
            margin: 0;
            padding: 0;
        }

        #main-body-wrapper {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            background-color: #e9f0e6;
            border-radius: 15px;
            padding: 30px;
        }

        .status-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .status-bar h1 {
            margin: 0;
            color: #1e1e1e;
        }

        .status-bar .online {
            color: green;
            font-weight: bold;
        }

        .status-bar select {
            font-family: 'Share Tech Mono', monospace;
        }

        .section-box {
            background: #f6f9f6;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .section-box h2 {
            margin-top: 0;
            font-size: 1.3em;
            color: #1e1e1e;
        }

        .section-box p, .section-box ul li {
            color: #5e6d64;
            font-size: 1em;
        }

        .skills ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .skills ul li {
            margin-bottom: 10px;
        }

        .rating, .message {
            margin-top: 30px;
            background: #f6f9f6;
            padding: 20px;
            border-radius: 15px;
        }

        .rating h2, .message h2 {
            margin-top: 0;
            color: #1e1e1e;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="number"], textarea {
            font-family: 'Share Tech Mono', monospace;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        input[type="submit"] {
            background-color: #92bfa2;
            border: none;
            color: white;
            padding: 10px;
            font-family: 'Share Tech Mono', monospace;
            border-radius: 8px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #7fa88f;
        }

        .user-email {
            color: #5e6d64;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div id="main-body-wrapper">

    <div class="status-bar">
        <h1>
            <?php echo htmlspecialchars($user['username']); ?> 
            <span class="online">Online</span>
        </h1>
        <select>
            <option>Select Status</option>
            <option>Online</option>
            <option>Offline</option>
            <option>Away</option>
        </select>
    </div>

    <div class="user-email">
        <?php echo htmlspecialchars($user['email']); ?>
    </div>

    <div class="section-box bio">
        <h2>About Me</h2>
        <p>Hello, I have a cat</p>
    </div>

    <div class="section-box skills">
        <h2>Skills</h2>
        <ul>
            <li>Teamwork</li>
            <li>Problem-Solving</li>
            <li>Leadership</li>
            <li>Technical Skills</li>
            <li>Time Management</li>
            <li>PHP</li>
            <li>HTML/CSS</li>
            <li>JavaScript</li>
            <li>MySQL</li>
        </ul>
    </div>

    <div class="rating">
        <h2>Rate this User</h2>
        <form method="POST">
            <label for="rating">Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>
            <input type="submit" value="Submit Rating">
        </form>
        <p>Average Rating: <?php echo number_format($avg_rating, 2); ?></p>
    </div>

    <div class="message">
        <h2>Send a Message</h2>
        <form action="send_message.php" method="POST">
            <input type="hidden" name="recipient_id" value="<?php echo $user_id; ?>">
            <textarea name="message" rows="4" cols="50" placeholder="Type your message here..."></textarea>
            <input type="submit" value="Send">
        </form>
    </div>

</div>

</body>
</html>
