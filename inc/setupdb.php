<html>
    <head>
        <title>Database Setup</title>
    </head>
    <body>
        <?php
            require_once 'sql.php';

            try {  // LOGIN
                $pdo = new PDO($attr, $user, $pass, $opts);
                try { // CREATE users EXCEPT
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS users (
                            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            email VARCHAR(100) NOT NULL,
                            username VARCHAR(32) NOT NULL,
                            password VARCHAR(255) NOT NULL
                        )"
                );
                echo "Table '<b>users</b>' created successfully.<br>";
                catch (Exception $e) { // CREATE users EXCEPT
                    echo $e->getMessage();
                }
                try { // CREATE jobs
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS jobs (
                            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            jobtitle VARCHAR(255) NOT NULL,
                            description TEXT NOT NULL,
                            location VARCHAR(255) NOT NULL,
                            skills TEXT,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )"
                    );
                    echo "Table '<b>jobs</b>' created successfully.<br>";
                }
                catch (Exception $e) { // CREATE jobs EXCEPT
                    echo $e->getMessage();
                }
            }
            catch (Exception $e) { // LOGIN EXCEPT
                echo $e->getMessage();
            }
        ?>
    </body>
</html