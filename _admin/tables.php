<?php
require_once '../inc/database.php';

$tables = array('users', 'messages', 'jobs', 'events', 'job_applications');
$tbl = $_POST['table'] ?? $_GET['table'] ?? 'users';
$query = "SELECT * FROM $tbl";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM $tbl WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF'] . "?table=" . urlencode($tbl)); 
    exit();
}
?>

<html>
<head>
    <title>View and Delete Records</title>
</head>
<body>
    <form method="post">
        <select name="table" id="table">
            <?php 
            foreach ($tables as $table) {
                echo "<option value=\"$table\">$table</option>";
            }
            ?>
        </select>
        <input type="submit">
    </form>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <?php
            if ($result->num_rows > 0) {
                $cols = $result->fetch_fields();
                foreach ($cols as $col) {
                    echo "<th>" . htmlspecialchars($col->name) . "</th>";
                }
                echo "<th>Action</th>";
            }
            ?>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $i) {
                    echo "<td>" . htmlspecialchars($i) . "</td>";
                }
                echo "<td>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='table' value='$tbl'>
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
                            <button type='submit' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</button>
                        </form>
                    </td>
                </tr>";
            }
        }
        ?>
    </table>
</body>
</html>