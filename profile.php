<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'resume');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    
    // Update email and password
    $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt->bind_param("ssi", $new_email, $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="create_resume.php">Create Resume</a></li>
                <li><a href="view_resumes.php">View Resumes</a></li>
                <li><a href="logout.php">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Update Your Profile</h1>
        <form method="POST" action="profile.php">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $current_user['email']; ?>" required><br><br>
            
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            
            <button type="submit">Update Profile</button>
        </form>
    </main>
</body>
</html>
