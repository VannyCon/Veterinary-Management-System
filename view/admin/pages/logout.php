<?php
// Start the session
session_start();

// Unset specific session variable
if (isset($_SESSION['admin'])) {
    unset($_SESSION['admin']);
    unset($_SESSION['isWho']);
}
// Optional: Unset other session variables if needed
// unset($_SESSION['isApproved']);

// Destroy the entire session if you want to log out completely


// Redirect to the login page (or another page)
header("Location: ../index.php");
exit;
?>
