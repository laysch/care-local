<?php 
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_postings";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";
$jobID = ""; // Initialize jobID variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $jobtitle = sanitizeInput($_POST['jobtitle']);
    $description = sanitizeInput($_POST['description']);

    // Collect and combine city and state
    $city = sanitizeInput($_POST['city']);
    $state = sanitizeInput($_POST['state']);
    $location = $city . ', ' . $state;  // Combine city and state

    // Check if any skills are selected (multiple selection)
    if (isset($_POST['skills']) && is_array($_POST['skills'])) {
        $skills = implode(", ", $_POST['skills']);
    } else {
        $skills = "";
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO jobs (jobtitle, description, location, skills) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $jobtitle, $description, $location, $skills);

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Job posted successfully!";
        $jobID = $stmt->insert_id;
        header("Location: jobsearch.php");
        exit; // Ensure no further code is executed
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data); // Removes whitespace from the beginning and end of string
    $data = stripslashes($data); // Removes quotes from a quoted string
    $data = htmlspecialchars($data); // Converts special characters to HTML entities
    return $data;
}

// Set the current page
$currentPage = 'Home'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLocal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
    <link rel="icon" type="image/x-icon" href="/img/favicon.png">
    <style>
        body {
            background-color: #FFFFFF;
            font-family: Arial, sans-serif;
            color: #5D674C;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            width: 80%;
            background-color: #FCEADE;
            padding: 20px;
            border-radius: 10px;
            overflow-y: auto;
        }
        h2 {
            text-align: center;
            color: #5D674C;
        }
        label {
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #D1D79D;
            border-radius: 5px;
        }
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .tag {
            background-color: #D1D79D;
            color: #fff;
            padding: 10px;
            border-radius: 25px;
            margin: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .tag.selected {
            background-color: #5D674C;
        }
        button {
            background-color: #F3E9B5;
            color: #5D674C;
            padding: 10px;
            border: none;
            width: 100%;
            cursor: pointer;
        }
        button:hover {
            background-color: #D1D79D;
        }
    </style>
</head>
<body>

    <!-- Include the Navbar at the very top -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h2>Create a Job Posting</h2>

        <?php if ($success_message != "") { echo "<p>$success_message</p>"; } ?>

        <form action="JobPost.php" method="POST">
            <label for="jobtitle">Job Title:</label>
            <input type="text" id="jobtitle" name="jobtitle" required>

            <label for="description">Job Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <!-- Separate city and state inputs -->
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>

            <label for="state">State:</label>
            <select id="state" name="state" required>
                <option value="">Select a State</option>
                <option value="New York">New York</option>
                <option value="California">California</option>
                <option value="Texas">Texas</option>
                <!-- Add other states as needed -->
            </select>

            <label for="skills">Required Skills:</label>
            <div class="tags-container">
                <?php 
                $available_skills = ['Communication', 'Teamwork', 'Problem-Solving', 'Leadership', 'Technical Skills', 'Time Management'];
                $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];

                foreach ($available_skills as $skill) {
                    $selected_class = in_array($skill, $selected_skills) ? 'selected' : '';
                    echo "<div class='tag $selected_class' data-skill='$skill'>$skill</div>";
                }
                ?>
            </div>

            <input type="hidden" name="skills[]" id="skills-input">
            <button type="submit">Post Job</button>
        </form>
    </div>

    <script>
        // Handle skill selection and deselection
        const tags = document.querySelectorAll('.tag');
        const skillsInput = document.getElementById('skills-input');

        tags.forEach(tag => {
            tag.addEventListener('click', () => {
                tag.classList.toggle('selected');
                updateSkillsInput();
            });
        });

        // Update the hidden input field with selected skills
        function updateSkillsInput() {
            const selectedTags = document.querySelectorAll('.tag.selected');
            const selectedSkills = Array.from(selectedTags).map(tag => tag.getAttribute('data-skill'));
            skillsInput.value = selectedSkills.join(', ');
        }
    </script>

</body>
</html>
