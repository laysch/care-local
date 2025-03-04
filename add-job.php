<?php   
$currentPage = "Add Job";
require_once 'inc/database.php';

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: /login.php');
    exit;
}

$success_message = "";
$jobID = ""; // Initialize jobID variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $jobtitle = sanitizeInput($_POST['jobtitle']);
    $description = sanitizeInput($_POST['description']);

    // Collect city input
    $city = sanitizeInput($_POST['city']);
    $location = $city;  // Only the city is needed now

    // Get county based on city
    $county = sanitizeInput($_POST['county']);

    // Check if any skills are selected (multiple selection)
    if (isset($_POST['skills']) && !empty($_POST['skills'])) {
        $skills = sanitizeInput($_POST['skills']);
    } else {
        $skills = "";
    }

    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO jobs (jobtitle, description, location, county, skills) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $jobtitle, $description, $location, $county, $skills);

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Job posted successfully!";
        $jobID = $stmt->insert_id;
        header("Location: search-jobs.php");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job | CareLocal</title>
    <link href="https://fonts.cdnfonts.com/css/share-techmono-2" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/ubuntu-mono" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/pt-sans" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/source-sans-pro" rel="stylesheet">
    <link href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/echxn/yeolithm@master/src/css/pixelution.css" rel="stylesheet">
    <style>
        :root {
            --bodyFontFamily: 'Share Tech Mono', monospace;
            --bodyFontSize: 14px;
            --backgroundColor: #f9eedd;
            --bordersColor: #839c99;
            --bodyTextColor: #839c99;
            --linksColor: #222222;
            --linksHoverColor: #efac9a;
        }

        body {
            background-color: #f9eedd;
            background-image: url('https://example.com/background.jpg');
            background-attachment: fixed;
            background-repeat: repeat;
            font-family: 'Share Tech Mono', monospace;
        }

        #main-body-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #cdd8c4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .hero {
            text-align: center;
            padding: 50px 20px;
        }

        .hero h1 {
            font-size: 2.5em;
            color: #5D674C;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            color: #839c99;
            margin-bottom: 30px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .cta-buttons a {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .cta-buttons a:hover {
            background-color: #efac9a;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #839c99;
            border-radius: 5px;
        }

        .category-btn {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .category-btn.active {
            background-color: #efac9a;
        }

        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tag {
            background-color: #D1D79D;
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tag.selected {
            background-color: #5D674C;
        }

        .cta-button {
            background-color: #5D674C;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .cta-button:hover {
            background-color: #efac9a;
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Body -->
    <div id="main-body-wrapper">
        <section class="hero">
            <h1>Create Job Posting</h1>
            <p>Provide details for the job position you'd like to post.</p>
        </section>

        <div class="form-container">
            <?php if ($success_message != "") { echo "<p style='color: green;'>$success_message</p>"; } ?>
            <form action="add-job.php" method="POST">
                <label for="jobtitle">Job Title:</label>
                <input type="text" id="jobtitle" name="jobtitle" required>

                <label for="description">Job Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <label for="city">City:</label>
                <select id="city-dropdown" name="city" required onchange="updateCounty()">
                    <option value="">Select a City</option>
                    <?php
                        // City list sorted alphabetically
                        $cities = array(
                            "Albertson", "Amagansett", "Amityville", "Atlantic Beach", "Baldwin", "Bay Park", "Bay Shore", "Bayville", 
                            "Bellmore", "Bellport", "Bellerose", "Bethpage", "Blue Point", "Bohemia", "Brentwood", "Bridgehampton", 
                            "Brookhaven", "Brookville", "Calverton", "Carle Place", "Cedarhurst", "Centre Island", "Centreport", "Cove Neck",
                            "Deer Park", "East Hills", "East Hampton", "East Meadow", "East Moriches", "East Marion", "East Northport", 
                            "East Norwich", "East Port", "East Quogue", "East Rockaway", "East Setauket", "Elmont", "Farmingdale", "Farmingville", 
                            "Fishers Island", "Floral Park", "Flower Hill", "Franklin Square", "Freeport", "Garden City", "Glen Cove", 
                            "Glen Head", "Glenwood Landing", "Great Neck", "Great River", "Greenlawn", "Greenport", "Greenvale", "Harbor Hills", 
                            "Harbor Isle", "Hampton Bays", "Hauppauge", "Hempstead", "Herricks", "Hewlett", "Hicksville", "Holbrook", 
                            "Holtsville", "Inwood", "Island Park", "Islandia", "Jericho", "Kensington", "Kings Park", "Kings Point", 
                            "Lake Grove", "Lake Success", "Lakeview", "Laurel", "Laurel Hollow", "Lattingtown", "Lawrence", "Levittown", 
                            "Lindenhurst", "Lido Beach", "Locust Valley", "Long Beach", "Lynbrook", "Malverne", "Manorhaven", "Manorville",
                            "Mastic", "Mastic Beach", "Mattituck", "Melville", "Mineola", "Moriches", "Mount Sinai", "Nesconset", 
                            "North Bellmore", "North Babylon", "North Great River", "North Merrick", "Northport", "Oceanside", "Old Brookville", 
                            "Old Westbury", "Oyster Bay", "Patchogue", "Plainview", "Point Lookout", "Port Jefferson", "Port Jefferson Station", 
                            "Port Washington", "Riverhead", "Rockville Centre", "Roslyn", "Roslyn Heights", "Seaford", "Shirley", "Smithtown", 
                            "South Farmingdale", "South Hempstead", "South Huntington", "South Jamesport", "Southold", "St. James", 
                            "Stony Brook", "Syosset", "Uniondale", "Upper Brookville", "Wantagh", "Water Mill", "West Babylon", "West Hempstead", 
                            "West Islip", "Westbury", "Westhampton", "Westhampton Beach", "Westport", "Williston Park", "Woodbury", 
                            "Woodmere", "Wyandanch"
                        );
                        sort($cities); // Sort cities alphabetically
                        foreach ($cities as $city) {
                            echo "<option value=\"$city\">$city</option>";
                        }
                    ?>
                </select>

                <label for="county">County:</label>
                <input type="text" id="county" name="county" readonly>

                <label for="skills">Skills:</label>
                <div class="tags-container" id="tags-container">
                    <div class="tag" data-tag="Communication">Communication</div>
                    <div class="tag" data-tag="Teamwork">Teamwork</div>
                    <div class="tag" data-tag="Problem-Solving">Problem-Solving</div>
                    <div class="tag" data-tag="Leadership">Leadership</div>
                    <div class="tag" data-tag="Technical Skills">Technical Skills</div>
                    <div class="tag" data-tag="Time Management">Time Management</div>
                </div>

                <button type="submit" class="cta-button">Post Job</button>
            </form>
        </div>
    </div>

    <script>
        // Update county field based on selected city
        function updateCounty() {
            var city = document.getElementById('city-dropdown').value;
            var countyInput = document.getElementById('county');
            // This is a placeholder example for how the county might be derived
            var counties = {
                "Amagansett": "Suffolk",
                "Bay Shore": "Suffolk",
                "Freeport": "Nassau",
                "Jericho": "Nassau"
                // Add additional mappings as needed
            };
            countyInput.value = counties[city] || '';
        }
    </script>
</body>
</html>
