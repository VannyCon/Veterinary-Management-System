<?php
// Start the session
session_start();

// Unset specific session variable
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
}

// Optional: Unset other session variables if needed
// unset($_SESSION['isApproved']);

// Destroy the entire session if you want to log out completely


// Redirect to the login page (or another page)
header("Location: index.php");
exit;
?>
