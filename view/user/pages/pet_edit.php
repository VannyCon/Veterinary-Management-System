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
        // Get the pet data from the form
        $pet_id = $_POST['pet_id'];
        $pet_name = $_POST['pet_name'];
        $pet_species = $_POST['pet_species'];
        $pet_age = $_POST['pet_age'];

        // Update the pet information in the database
        $query = "UPDATE tbl_pet SET pet_name = ?, pet_species = ?, pet_age = ? WHERE pet_id = ? AND user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pet_name, $pet_species, $pet_age, $pet_id, $_SESSION['user_id']]);

        // Redirect to the dashboard or pet list page after updating
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    echo '<p class="text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
