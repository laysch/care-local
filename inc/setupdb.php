<html>
    <head>
        <title>Database Setup</title>
    </head>
    <body>
        <?php
            require_once 'database.php';

            // Create 'jobs' table if it doesn't exist
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
                echo "Table '<b>jobs</b>' created successfully<br>";
            } catch (Exception $e) {
                echo $e->getMessage();
            }

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

            $conn->close();
        ?>
    </body>
</html