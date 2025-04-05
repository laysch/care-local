function notifyUsersAboutJob($jobID) {
    global $conn;

    // Example query to get users based on location (you can modify this query based on your requirements)
    $stmt = $conn->prepare("SELECT user_id, email FROM users WHERE location = (SELECT location FROM jobs WHERE job_id = ?)");
    $stmt->bind_param("i", $jobID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Loop through the users and send a notification (email or any other method)
    while ($user = $result->fetch_assoc()) {
        // Here you can implement your notification logic, e.g., sending an email
        $to = $user['email'];
        $subject = "New Job Posting in Your Area!";
        $message = "A new job has been posted in your area. Check it out!";
        mail($to, $subject, $message);  // Example email sending
    }

    // Close statement
    $stmt->close();
}
