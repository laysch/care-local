<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'inc/session.php';
require_once 'inc/database.php';

if (isset($_POST['login'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$email) {
        $error = "Invalid email format.";
    } else {    
        $stmt = $conn->prepare("SELECT id, password, username FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password. Please try again.";
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CareLocal</title>
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
        .register-link { margin-top: 15px; text-align: center; }
        .register-link a { text-decoration: none; color: #cdd8c4; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <h2>Sign in to CareLocal</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register Here!</a></p>
            </div>
        </div>
        <div class="right">
            <img src="img/favicon.png" alt="CareLocal Logo" style="width: 150px; max-width: 100%; margin-bottom: 15px;">
            <h1>CareLocal</h1>
            <p>Where Local Talent Meets Local Needs</p>
        </div>
    </div>
</body>
</html>
