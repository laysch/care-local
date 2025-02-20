<html>
    <head>
        <title>Database Setup</title>
    </head>
    <body>
        <?php
            require_once 'sql.php';

            try {  // LOGIN
                $pdo = new PDO($attr, $user, $pass, $opts);
                try { // CREATE USERS
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS users (
                            username VARCHAR(255) NOT NULL UNIQUE,
                            email VARCHAR(255) NOT NULL,
                            password_hash VARCHAR(255) NOT NULL,
                            PRIMARY KEY (username)
                        )"
                    );
                    echo "Table '<b>users</b>' created successfully.<br>";
                }
                catch (Exception $e) { // CREATE USER EXCEPT
                    echo $e->getMessage();
                }
            }
            catch (Exception $e) { // LOGIN EXCEPT
                echo $e->getMessage();
            }
        ?>
    </body>
</html