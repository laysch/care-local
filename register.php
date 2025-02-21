<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'inc/database.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        $success = "Registration successful. Please login.";
        header("Location: login.php");
        exit();
    } else {
        $error = "Error registering. Try again.";
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
        .right { width: 50%; background-color: #5d674c; color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px; }
        .right h1 { margin: 0; font-size: 48px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); }
        .right p { font-size: 18px; margin-top: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; }
        .form-group input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { width: 50%; padding: 8px; background-color: #5d674c; color: white; border: none; border-radius: 5px; cursor: pointer; display: block; margin: 10px auto; text-align: center; font-size: 18px; font-weight: bold; }
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
                <button type="submit" name="register" class="btn">Register</button>
            </form>
            <hr>
            <p>Already have an account? <a href="login.php">Login Here!</a></p>
        </div>
        <div class="right">
            <h1>CareLocal</h1>
            <p>Where Local Talent Meets Local Needs</p>
        </div>
    </div>
</body>
</html>
