<?php
session_start();

// Database connection
$conn = new mysqli('127.0.0.1', 'root', 'password', 'resume');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all resumes from the resumedata table
$sql = "SELECT * FROM resumedata";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); // Catch any errors in the query
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Resumes</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<h1>Your Resumes</h1>

<?php if ($result->num_rows > 0) : ?>
    <?php while ($row = $result->fetch_assoc()) : ?>
        <div class="resume-preview">
            <h2><?php echo htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']); ?></h2>
            <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
            <p>Designation: <?php echo htmlspecialchars($row['designation']); ?></p>
            
            <?php if (!empty($row['image_path'])) : ?>
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Resume Image" style="max-width: 200px;">
            <?php endif; ?>

            <h3>Achievements</h3>
            <ul>
            <?php
            // Decode the achievements JSON field and display it
            $achievements = json_decode($row['achievements'], true);
            if (!empty($achievements)) {
                foreach ($achievements as $achievement) {
                    echo "<li>" . htmlspecialchars($achievement['achieve_title']) . ": " . htmlspecialchars($achievement['achieve_description']) . "</li>";
                }
            } else {
                echo "<p>No achievements found.</p>";
            }
            ?>
            </ul>

            <!-- Add similar sections for experiences, educations, projects, and skills -->
            <h3>Experiences</h3>
            <ul>
            <?php
            $experiences = json_decode($row['experiences'], true);
            if (!empty($experiences)) {
                foreach ($experiences as $experience) {
                    echo "<li>" . htmlspecialchars($experience['job_title']) . " at " . htmlspecialchars($experience['company']) . "</li>";
                }
            } else {
                echo "<p>No experiences found.</p>";
            }
            ?>
            </ul>

            <h3>Educations</h3>
            <ul>
            <?php
            $educations = json_decode($row['educations'], true);
            if (!empty($educations)) {
                foreach ($educations as $education) {
                    echo "<li>" . htmlspecialchars($education['degree']) . " at " . htmlspecialchars($education['institution']) . "</li>";
                }
            } else {
                echo "<p>No education records found.</p>";
            }
            ?>
            </ul>

            <h3>Projects</h3>
            <ul>
            <?php
            $projects = json_decode($row['projects'], true);
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    echo "<li>" . htmlspecialchars($project['project_title']) . ": " . htmlspecialchars($project['project_description']) . "</li>";
                }
            } else {
                echo "<p>No projects found.</p>";
            }
            ?>
            </ul>

            <h3>Skills</h3>
            <ul>
            <?php
            $skills = json_decode($row['skills'], true);
            if (!empty($skills)) {
                foreach ($skills as $skill) {
                    echo "<li>" . htmlspecialchars($skill['skill_name']) . ": " . htmlspecialchars($skill['skill_level']) . "</li>";
                }
            } else {
                echo "<p>No skills found.</p>";
            }
            ?>
            </ul>

            <!-- Edit & Delete Buttons -->
            <a href="edit_resume.php?id=<?php echo urlencode($row['id']); ?>">Edit</a> |
            <a href="delete_resume.php?id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this resume?');">Delete</a>
            
            <hr>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <p>No resumes found.</p>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
