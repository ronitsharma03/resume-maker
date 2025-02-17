<?php
// Start session
session_start();

// Destroy session to log the user out
session_destroy();

// Redirect to login page after logging out
header("Location: login.html");
exit();
?>
