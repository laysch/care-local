// Correctly checking the data and setting the values
$user = [
    "first_name" => "John",
    "last_name" => "Doe",
    "bio" => "A passionate developer with a love for creating innovative solutions. I enjoy working on web and mobile applications.",
    "location" => "New York, USA",
    "skills" => ["Communication", "Teamwork", "Problem-Solving"], // Example skills
    "profile_picture" => "https://example.com/profile.jpg"
];

// Check if the form is submitted to update the user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure 'first_name' and 'last_name' are set before accessing them
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    
    // Combine first and last names to form the full name
    $fullName = $firstName . ' ' . $lastName;

    // Update user profile based on submitted form data
    $user['first_name'] = $firstName;
    $user['last_name'] = $lastName;
    $user['full_name'] = $fullName; // Store the full name
    $user['bio'] = $_POST['bio'] ?? '';
    $user['skills'] = isset($_POST['skills']) ? $_POST['skills'] : [];

    // Handle the file upload for the avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);

        // Check if file is an image
        if (getimagesize($_FILES['avatar']['tmp_name'])) {
            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
            $user['profile_picture'] = $uploadFile; // Update the avatar path
        }
    }

    // Assuming $conn is your database connection
    // You would update the database with the new first name, last name, and full name:
    $query = "UPDATE users SET first_name = ?, last_name = ?, full_name = ?, bio = ?, skills = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $firstName, $lastName, $fullName, $_POST['bio'], implode(',', $_POST['skills']), $userId);
    $stmt->execute();
}


