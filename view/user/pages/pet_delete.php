<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if the user is not logged in
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the pet_id from the form
        $pet_id = $_POST['pet_id'];

        // Delete the pet from the database
        $query = "DELETE FROM tbl_pet WHERE pet_id = ? AND user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pet_id, $_SESSION['user_id']]);

        // Redirect to the dashboard or pet list page after deletion
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    echo '<p class="text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
