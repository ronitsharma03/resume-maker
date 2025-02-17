<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'resume', 3308);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

// Fetch resume details
$sql = "SELECT * FROM about_section WHERE user_id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];

    $update_sql = "UPDATE about_section SET first_name='$first_name', last_name='$last_name', email='$email', designation='$designation' WHERE user_id=$id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Resume updated successfully'); window.location='view_resumes.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Resume</title>
</head>
<body>

<h1>Edit Resume</h1>

<form method="POST">
    <label>First Name:</label>
    <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" required><br>

    <label>Last Name:</label>
    <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" required><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo $row['email']; ?>" required><br>

    <label>Designation:</label>
    <input type="text" name="designation" value="<?php echo $row['designation']; ?>" required><br>

    <button type="submit">Update</button>
</form>

<a href="view_resumes.php">Back</a>

</body>
</html>

<?php
$conn->close();
?>
