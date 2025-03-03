<?php
require_once 'database.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['upload'])) {
    $userId = $_SESSION['user_id'];
    $file = "../img/avatar" . basename($_FILES["avatar"]["name"]);
    $allowedFiles = ['jpg', 'jpeg', 'png', 'gif'];
    $fileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    try {
        if (!in_array($fileType, $allowedFiles)) {
            throw new Exception("Please use one of the following file types: " . print_r($allowedFiles));
        } else {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $file)) {
                $query = "UPDATE users SET avatar = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", basename($file), $userId);
                $stmt->execute();
                header("Location: /profile.php");
            } else {
                throw new Exception("Avatar upload failed");
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
?>