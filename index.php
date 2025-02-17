<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli('127.0.0.1', 'root', 'password', 'resume');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user info from session
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Builder - Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Common CSS file -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index1.html">Create Resume</a></li>
                <li><a href="view_resumes.php">View Resumes</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="welcome">
            <h1>Welcome, <?php echo $email; ?>!</h1>
            <p>Select an option below to start creating or managing your resumes.</p>
        </section>

        <section class="actions">
            <div class="action-box">
                <h2>Create a New Resume</h2>
                <p>Fill in your details and choose a template to create a resume.</p>
                <a href="index1.html" class="button">Create Resume</a>
            </div>

            <div class="action-box">
                <h2>View Existing Resumes</h2>
                <p>Review or download resumes you have already created.</p>
                <a href="view_resumes.php" class="button">View Resumes</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Resume Builder | All Rights Reserved</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
