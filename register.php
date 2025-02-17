<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Resume Builder</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="logo">ResumeBuilder<span>.IT</span></div>
    </header>

    <main class="main-container">
        <section class="form-section">
            <h2>Create an Account</h2>
            <?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'resume', 3308);
if ($conn->connect_error) {
    die("<p style='color:red;'>Connection failed: " . $conn->connect_error . "</p>");
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $errors = [];

    // Validate email (must be Gmail)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, '@gmail.com')) {
        $errors[] = "Please enter a valid Gmail address.";
    }

    // Validate password strength (at least 8 characters, one uppercase letter, one number, one special character)
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one number, and one special character.";
    }

    // If no errors, insert into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<p style='color:red;'>Email is already registered.</p>";
        } else {
            // Insert plain text password into the database
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $password);
            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
            }
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



            <form action="register.php" method="POST" class="form">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your Gmail address" required
                       pattern=".*@gmail\.com" title="Please enter a valid Gmail address.">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required
                       pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                       title="Password must contain at least one uppercase letter, one number, one special character, and be at least 8 characters long.">

                <button type="submit" class="button">Register</button>
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2024 ResumeBuilder.IT. All rights reserved.</p>
    </footer>
</body>
</html>
