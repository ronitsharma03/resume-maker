<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'resume', 3308);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

// Delete from about_section
$sql = "DELETE FROM about_section WHERE user_id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Resume deleted successfully'); window.location='view_resumes.php';</script>";
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
