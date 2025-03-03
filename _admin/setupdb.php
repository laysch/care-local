<html>
    <head>
        <title>Database Setup</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="display: flex; justify-content: center; min-height: 100vh; mid-width: 100vh;">
        <?php
            require_once '../inc/database.php';

            // Create jobs
            try {
                $query = "CREATE TABLE jobs (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    jobtitle VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    location VARCHAR(255) NOT NULL,
                    county VARCHAR(255) NOT NULL,
                    skills TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                $conn->query($query);
                echo "Table '<b>jobs</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "<br>";

            // Create users
            try {
                $query = "CREATE TABLE users (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(100) NOT NULL,
                    username VARCHAR(32) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    avatar VARCHAR(255) NULL
                )";
                $conn->query($query);
                echo "Table '<b>users</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "<br>";

            // Create events
            try {
                $query = "CREATE TABLE events(
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    datetime DATETIME NOT NULL,
                    location VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );";
                $conn->query($query);
                echo "Table '<b>events</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "<br>";

            // Create messages
            try {
                $query = "CREATE TABLE messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sender_id INT NOT NULL,
                    receiver_id INT NOT NULL,
                    message TEXT NOT NULL,
                    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    is_read TINYINT(1) DEFAULT 0
                );";
                $conn->query($query);
                echo "Table '<b>messages</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "<br>";

            $conn->close();
        ?>
        </div>
    </body>
</html