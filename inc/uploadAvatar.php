<?php
require_once 'database.php';
require_once 'session.php';

if (isset($_POST['upload'])) {
    $userId = $_SESSION['user_id'];
    $uploadDir = "../img/avatar/";
    $allowedFiles = ['jpg', 'jpeg', 'png', 'gif'];
    $fileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $newFile = uniqid("avatar_") . "." . $fileType;

    try {
        if (!in_array($fileType, $allowedFiles)) {
            throw new Exception("Please use one of the following file types: " . implode(", ", $allowedFiles));
        }

        if ($_FILES["avatar"]["size"] > $maxFileSize) {
            throw new Exception("Please use an image under 5MB.");
        }

        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $uploadDir . $newFile)) {
            $query = "UPDATE users SET avatar = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", basename($newFile), $userId);
            $stmt->execute();
            header("Location: /profile.php");
            exit();
        } else {
            throw new Exception("Avatar upload failed.");
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
