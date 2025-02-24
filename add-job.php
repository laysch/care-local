<?php   

require_once 'inc/database.php';

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
            align-items: center;
            justify-content: center;
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

    </style>
</head>
<body>

    <!-- Include the Navbar at the very top -->
    <?php include 'navbar.php'; ?>

    <div class="hero-section">
        <h1>Create a Job Posting</h1>
    </div>
    <div class="features-grid">
        <div class="feature-card">

            <?php if ($success_message != "") { echo "<p>$success_message</p>"; } ?>

            <form action="add-job.php" method="POST">
                <label for="jobtitle">Job Title:</label>
                <input type="text" id="jobtitle" name="jobtitle" required>

                <label for="description">Job Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <!-- City dropdown -->
                <label for="city">City:</label>
                <select id="city-dropdown" name="city" required onchange="updateCounty()">
                    <option value="">Select a City</option>
                    <!-- List of cities from Nassau and Suffolk counties, combined alphabetically -->
                    <?php
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
                            "Massapequa", "Massapequa Park", "Matinecock", "Mastic", "Mastic Beach", "Melville", "Merrick", "Miller Place", 
                            "Mineola", "Montauk", "Moriches", "Middle Island", "Muttontown", "New Cassel", "New Hyde Park", "New Suffolk", 
                            "North Babylon", "North Hills", "Northport", "Oakdale", "Oceanside", "Old Bethpage", "Old Brookville", "Old Westbury", 
                            "Orient", "Oyster Bay", "Patchogue", "Peconic", "Plainedge", "Plainview", "Port Jefferson", "Port Jefferson Station", 
                            "Point Lookout", "Port Washington", "Quogue", "Remsenburg", "Riverhead", "Rockville Centre", "Roosevelt", "Roslyn", 
                            "Sag Harbor", "Sagaponack", "Saint James", "Salisbury", "Sands Point", "Sea Cliff", "Seaford", "Searingtown", 
                            "Selden", "Shelter Island", "Shelter Island Heights", "Shirley", "Shoreham", "Smithtown", "Sound Beach", "South Jamesport", 
                            "Southampton", "Southold", "Speonk", "Stony Brook", "Strathmore", "Syosset", "Thomaston", "Uniondale", "Valley Stream", 
                            "Wading River", "Wantagh", "Wainscott", "Water Mill", "West Babylon", "West Hempstead", "West Islip", "Westbury", 
                            "Wyandanch", "Woodbury", "Woodmere", "Woodsburgh", "Yaphank"
                        );

                        sort($cities);
                        foreach ($cities as $city) {
                            echo "<option value='$city'>$city</option>";
                        }
                    ?>
                </select>

                <label for="county">County:</label>
                <input type="text" id="county" name="county" readonly>

                <label for="skills">Required Skills:</label>
                <div class="tags-container">
                    <?php 
                    $available_skills = ['Communication', 'Teamwork', 'Problem-Solving', 'Leadership', 'Technical Skills', 'Time Management'];
                    $selected_skills = isset($_POST['skills']) ? $_POST['skills'] : [];
                    foreach ($available_skills as $skill) {
                        $isSelected = in_array($skill, $selected_skills) ? 'selected' : '';
                        echo "<button type='button' class='tag $isSelected' onclick='toggleSkillSelection(this, \"$skill\")'>$skill</button>";
                    }
                    ?>
                </div>

                <input type="hidden" name="skills" id="skills-input">

                <button type="submit" class="cta-button">Post Job</button>
            </form>
        </div>
    </div>
    <script>
        const countyMapping = {
    "Albertson": "Nassau", "Amagansett": "Suffolk", "Amityville": "Suffolk", "Atlantic Beach": "Nassau", "Baldwin": "Nassau", 
    "Bay Park": "Nassau", "Bay Shore": "Suffolk", "Bayville": "Nassau", "Bellmore": "Nassau", "Bellport": "Suffolk", 
    "Bellerose": "Queens", "Bethpage": "Nassau", "Blue Point": "Suffolk", "Bohemia": "Suffolk", "Brentwood": "Suffolk", 
    "Bridgehampton": "Suffolk", "Brookhaven": "Suffolk", "Brookville": "Nassau", "Calverton": "Suffolk", "Carle Place": "Nassau",
    "Cedarhurst": "Nassau", "Centre Island": "Nassau", "Centreport": "Suffolk", "Cove Neck": "Nassau", "Deer Park": "Suffolk", 
    "East Hills": "Nassau", "East Hampton": "Suffolk", "East Meadow": "Nassau", "East Moriches": "Suffolk", "East Marion": "Suffolk", 
    "East Northport": "Suffolk", "East Norwich": "Nassau", "East Port": "Suffolk", "East Quogue": "Suffolk", "East Rockaway": "Nassau", 
    "East Setauket": "Suffolk", "Elmont": "Nassau", "Farmingdale": "Nassau", "Farmingville": "Suffolk", "Fishers Island": "Suffolk", 
    "Floral Park": "Queens", "Flower Hill": "Nassau", "Franklin Square": "Nassau", "Freeport": "Nassau", "Garden City": "Nassau", 
    "Glen Cove": "Nassau", "Glen Head": "Nassau", "Glenwood Landing": "Nassau", "Great Neck": "Nassau", "Great River": "Suffolk", 
    "Greenlawn": "Suffolk", "Greenport": "Suffolk", "Greenvale": "Nassau", "Harbor Hills": "Nassau", "Harbor Isle": "Nassau", 
    "Hampton Bays": "Suffolk", "Hauppauge": "Suffolk", "Hempstead": "Nassau", "Herricks": "Nassau", "Hewlett": "Nassau", 
    "Hicksville": "Nassau", "Holbrook": "Suffolk", "Holtsville": "Suffolk", "Inwood": "Nassau", "Island Park": "Nassau", "Islandia": "Suffolk", 
    "Jericho": "Nassau", "Kensington": "Nassau", "Kings Park": "Suffolk", "Kings Point": "Nassau", "Lake Grove": "Suffolk", 
    "Lake Success": "Nassau", "Lakeview": "Nassau", "Laurel": "Suffolk", "Laurel Hollow": "Nassau", "Lattingtown": "Nassau", 
    "Lawrence": "Nassau", "Levittown": "Nassau", "Lindenhurst": "Suffolk", "Lido Beach": "Nassau", "Locust Valley": "Nassau", 
    "Long Beach": "Nassau", "Lynbrook": "Nassau", "Malverne": "Nassau", "Manorhaven": "Nassau", "Manorville": "Suffolk", 
    "Massapequa": "Nassau", "Massapequa Park": "Nassau", "Matinecock": "Nassau", "Mastic": "Suffolk", "Mastic Beach": "Suffolk", 
    "Melville": "Suffolk", "Merrick": "Nassau", "Miller Place": "Suffolk", "Mineola": "Nassau", "Montauk": "Suffolk", 
    "Moriches": "Suffolk", "Middle Island": "Suffolk", "Muttontown": "Nassau", "New Cassel": "Nassau", "New Hyde Park": "Nassau", 
    "New Suffolk": "Suffolk", "North Babylon": "Suffolk", "North Hills": "Nassau", "Northport": "Suffolk", "Oakdale": "Suffolk", 
    "Oceanside": "Nassau", "Old Bethpage": "Nassau", "Old Brookville": "Nassau", "Old Westbury": "Nassau", "Orient": "Suffolk", 
    "Oyster Bay": "Nassau", "Patchogue": "Suffolk", "Peconic": "Suffolk", "Plainedge": "Nassau", "Plainview": "Nassau", 
    "Port Jefferson": "Suffolk", "Port Jefferson Station": "Suffolk", "Point Lookout": "Nassau", "Port Washington": "Nassau", 
    "Quogue": "Suffolk", "Remsenburg": "Suffolk", "Riverhead": "Suffolk", "Rockville Centre": "Nassau", "Roosevelt": "Nassau", "Roslyn": "Nassau", 
    "Sag Harbor": "Suffolk", "Sagaponack": "Suffolk", "Saint James": "Suffolk", "Salisbury": "Nassau", "Sands Point": "Nassau", 
    "Sea Cliff": "Nassau", "Seaford": "Nassau", "Searingtown": "Nassau", "Selden": "Suffolk", "Shelter Island": "Suffolk", 
    "Shelter Island Heights": "Suffolk", "Shirley": "Suffolk", "Shoreham": "Suffolk", "Smithtown": "Suffolk", "Sound Beach": "Suffolk", 
    "South Jamesport": "Suffolk", "Southampton": "Suffolk", "Southold": "Suffolk", "Speonk": "Suffolk", "Stony Brook": "Suffolk", 
    "Strathmore": "Nassau", "Syosset": "Nassau", "Thomaston": "Nassau", "Uniondale": "Nassau", "Valley Stream": "Nassau", "Wading River": "Suffolk", 
    "Wantagh": "Nassau", "Wainscott": "Suffolk", "Water Mill": "Suffolk", "West Babylon": "Suffolk", "West Hempstead": "Nassau", 
    "West Islip": "Suffolk", "Westbury": "Nassau", "Wyandanch": "Suffolk", "Woodbury": "Nassau", "Woodmere": "Nassau", "Woodsburgh": "Nassau", 
    "Yaphank": "Suffolk"
};
function updateCounty() {
            const city = document.getElementById('city-dropdown').value;
            const countyInput = document.getElementById('county');
            
            if (city && countyMapping[city]) {
                countyInput.value = countyMapping[city];
            } else {
                countyInput.value = '';
            }
        }

        function toggleSkillSelection(button, skill) {
            button.classList.toggle('selected');
            let skillsInput = document.getElementById('skills-input');
            let selectedSkills = skillsInput.value ? skillsInput.value.split(', ') : [];

            if (button.classList.contains('selected')) {
                if (!selectedSkills.includes(skill)) {
                    selectedSkills.push(skill);
                }
            } else {
                selectedSkills = selectedSkills.filter(item => item !== skill);
            }

            skillsInput.value = selectedSkills.join(', ');
        }
        document.querySelector("form").addEventListener("submit", function () {
            let selectedButtons = document.querySelectorAll(".tag.selected");
            let skillsArray = Array.from(selectedButtons).map(btn => btn.textContent);
            document.getElementById("skills-input").value = skillsArray.join(', ');
        });
    </script>

</body>
</html>