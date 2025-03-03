<?php
$currentPage = 'My Profile';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

require_once 'inc/database.php';
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc()
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <table>
        <tr>
            <td>Username:</td>
            <td><?php echo $row['username']?></td>
        </tr>
        <tr>
            <td>Email Address:</td>
            <td><?php echo $row['email']?></td>
        </tr>
    </table>
</body>
</html