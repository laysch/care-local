<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'inc/database.php';

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $defaultAvatar = 'default_avatar.png';

    // no empty fields
    if (empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirmPassword) {
        // Check if passwords match
        $error = "Passwords do not match.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // verify if email or username already exists
        $stmt = $conn->prepare("SELECT email, username FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $existingUser = $result->fetch_assoc();
            if ($existingUser['email'] === $email) {
                $error = "That email is already in use.";
            } elseif ($existingUser['username'] === $username) {
                $error = "That username is already taken.";
            }
        } else {
            // process registration
            $stmt = $conn->prepare("INSERT INTO users (email, username, password, avatar) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $username, $passwordHash, $defaultAvatar);
            if ($stmt->execute()) {
                $success = "Registration successful. Please login.";
                header("Location: profile.php");
                exit();
            } else {
                $error = "Error registering. Try again.";
            }
        }
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .container { display: flex; width: 70%; background: white; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .left { width: 50%; padding: 40px; color: black; }
        .right { width: 50%; background-color: #cdd8c4; color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px; }
        .right h1 { margin: 0; font-size: 48px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); }
        .right p { font-size: 18px; margin-top: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; }
        .form-group input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { width: 50%; padding: 8px; background-color: #cdd8c4; color: white; border: none; border-radius: 5px; cursor: pointer; display: block; margin: 10px auto; text-align: center; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h2>Sign up for CareLocal</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
            <hr>
            <p>Already have an account? <a href="login.php">Login Here!</a></p>
        </div>
        <div class="right">
            <img src="img/favicon.png" alt="CareLocal Logo" style="width: 150px; max-width: 100%; margin-bottom: 15px;">
            <h1>CareLocal</h1>
            <p>Where Local Talent Meets Local Needs</p>
        </div>
    </div>
</body>
</html>
