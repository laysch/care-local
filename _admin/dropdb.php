<?php

require_once "../inc/database.php";

function sanitizeInput($data) {
    $data = trim($data); 
    $data = stripslashes($data); 
    $data = htmlspecialchars($data); 
    return $data;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tableName === sanitizeInput($_POST['tableName']) ?? '';
        $action = $_POST['action'] ?? '';

        if ($action == 'drop') {
            $query = "DROP TABLE '$tableName'";
        } elseif ($action === 'truncate') {
            $query = "TRUNCATE TABLE '$tableName'";
        } else {
            throw new Exception("Invalid action");
        }

        if (!$conn->query($query)) {
            throw new Exception($conn->error);
        }

        echo "Successful " . $action . " on " . $tableName;
    }
} catch (Exception $e) {
    echo $e->getMessage();;
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop DB Tables</title>
</head>
<body>
    <form method="POST" action="" style="display: flex; justify-content: center; align-items:center; min-height: 100vh; mid-width: 100vh;">
        <label for="tableName">Table:</label>
        <input type="text" name="tableName" id="tableName" required>
        <label for="action">Action:</label>
        <select name="action" id="action">
            <option value="drop">Drop</option>
            <option value="truncate">Truncate</option>
        </select>

        <button tyle="submit">Go!</button>
    </form>
</body>
</html>