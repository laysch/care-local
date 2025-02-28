<?php

require_once '../inc/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

if (empty($search)) {
    // search is empty, return empty array
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, username FROM users WHERE username LIKE CONCAT('%', ?, '%') LIMIT 10");
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "id" => $row["id"],
        "username" => $row["username"]
    ];
}

echo json_encode($users);
?>