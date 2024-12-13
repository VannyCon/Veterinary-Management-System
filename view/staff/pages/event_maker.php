<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start the transaction
    $pdo->beginTransaction();

    $date = $_POST['date']; // Ensure this session value is set
    $title = $_POST['title']; // Assuming data comes from form POST

    // Prepare and execute the insert query
    $event = "INSERT INTO `tbl_calendar`(`date`, `title`) VALUES (:date,:title)";
    $stmt2 = $pdo->prepare($event);
    $stmt2->bindParam(':date', $date);
    $stmt2->bindParam(':title', $title);
    $stmt2->execute();

    // Commit the transaction
    $pdo->commit();

    // Redirect after successful insertion
    header("Location: events.php");
    exit; // Make sure to call exit after header redirect

} catch (Exception $e) {
    // Roll back the transaction if an error occurs
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
