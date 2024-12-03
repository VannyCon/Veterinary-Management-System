<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'system_db';
$username = 'root';
$password = '';

try {
    // Connect to the database using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from tbl_calendar
    $query = "SELECT `id`, `date`, `title` FROM `tbl_calendar` WHERE 1";
    $stmt = $pdo->query($query);
    
    // Fetch all rows
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the JSON-encoded data
    echo json_encode($events);

} catch (PDOException $e) {
    // Handle errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
