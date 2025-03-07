<?php
require_once 'inc/database.php';
include_once 'inc/func.php';

if (isset($_GET['q'])) {
    $search = sanitizeInput($_GET['q']);
    $searchTerm = "%" . $search . "%";

    $query = "
        SELECT 'user' AS type, id, username AS title FROM users WHERE username LIKE ?
        UNION
        SELECT 'job' AS type, id, jobtitle AS title FROM jobs WHERE jobtitle LIKE ?
        UNION
        SELECT 'event' AS type, id, title FROM events WHERE title LIKE ?
        LIMIT 15;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $searchResults = [];
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }

    echo json_encode($searchResults);
}
?>