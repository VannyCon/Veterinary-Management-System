<?php
session_start(); // Start session to access $_SESSION

// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['admin'];

try {
    // Establish PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    // Correct the variable from $conn to $pdo
    $query = "UPDATE tbl_user SET isApproved = 1 WHERE id = ?";
    $stmt = $pdo->prepare($query); // Use $pdo here
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: user_approve_index.php"); // Redirect to approved accounts page
        exit;
    } else {
        echo "Error: " . $stmt->errorInfo()[2]; // Show detailed error from PDO
    }
}
?>
