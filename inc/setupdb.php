<html>
    <head>
        <title>Database Setup</title>
    </head>
    <body>
        <?php
            require_once 'database.php';

            // Create jobs
            echo "<p>";
            try {
                $query = "CREATE TABLE IF NOT EXISTS jobs (
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
            echo "</p>";

            // Create users
            echo "<p>";
            try {
                $query = "CREATE TABLE IF NOT EXISTS users (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(100) NOT NULL,
                    username VARCHAR(32) NOT NULL,
                    password VARCHAR(255) NOT NULL
                )";
                $conn->query($query);
                echo "Table '<b>users</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "</p>";

            // Create events
            echo "<p>";
            try {
                $query = "CREATE TABLE IF NOT EXISTS events(
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
            echo "</p>";

            // Create messages
            echo "<p>";
            try {
                $query = "CREATE TABLE messages (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    sender_id INT NOT NULL,
                    receiver_id INT NOT NULL,
                    messages TEXT NOT NULL,
                    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    is_read TINYINT(1) DEFAULT 0
                );";
                $conn->query($query);
                echo "Table '<b>messages</b>' created successfully";
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "</p>";

            $conn->close();
        ?>
    </body>
</html