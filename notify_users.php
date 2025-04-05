<?php
require_once 'database.php';

function notifyUsersAboutJob($jobId) {
    global $conn;

    // Step 1: Get the job info
    $stmt = $conn->prepare("SELECT jobtitle, skills, county FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    $stmt->bind_result($jobtitle, $jobSkillsStr, $jobCounty);
    $stmt->fetch();
    $stmt->close();

    $jobSkills = array_map('trim', explode(',', $jobSkillsStr));

    // Step 2: Get all users and their preferences
    $query = "SELECT id, notify_preferences FROM users WHERE notify_preferences IS NOT NULL";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $prefs = json_decode($row['notify_preferences'], true);
        if (!$prefs || !isset($prefs['skills']) || !isset($prefs['county'])) continue;

        $userSkills = $prefs['skills'];
        $userCounties = $prefs['county'];

        // Step 3: Check for matching skill OR county
        $skillMatch = count(array_intersect($jobSkills, $userSkills)) > 0;
        $countyMatch = in_array($jobCounty, $userCounties);

        if ($skillMatch || $countyMatch) {
            // Step 4: Send a message to the user from "CareLocal"
            $message = "A new job '$jobtitle' matches your preferences.";
            $title = "New Job Alert: $jobtitle";

            // Send as a message from CareLocal (sender_id = 0, system user)
            $send = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, title) VALUES (?, ?, ?, ?)");
            $senderId = 0; // System user ID (CareLocal)
            $send->bind_param("iiss", $senderId, $userId, $message, $title);
            $send->execute();
            $send->close();
        }
    }

    $result->free();
}
?>
