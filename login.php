<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli('127.0.0.1', 'root', 'mypass', 'resume');  

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
} else {
    echo "Connected successfully to the 'resume' database.<br>";
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $errors = [];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, '@gmail.com')) {
        $errors[] = "Please enter a valid Gmail address.";
    }

    // If no errors, check credentials
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $db_password);
            $stmt->fetch();

            // Compare plain text passwords
            if ($password === $db_password) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;

                header('Location: index.php');
                exit();
            } else {
                echo "<p style='color:red;'>Invalid password.</p>";
            }
        } else {
            echo "<p style='color:red;'>No account found with this email.</p>";
        }

        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Resume Builder</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="logo">ResumeBuilder<span>.IT</span></div>
    </header>

    <main class="main-container">
        <section class="form-section">
            <h2>Login to Your Account</h2>
            <form action="login.php" method="POST" class="form">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your Gmail address" required
                       pattern=".*@gmail\.com" title="Please enter a valid Gmail address.">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" class="button">Login</button>
                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 ResumeBuilder.IT. All rights reserved.</p>
    </footer>
</body>
</html>
