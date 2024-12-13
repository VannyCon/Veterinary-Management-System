<?php
// Assuming the event data is passed as POST variables
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['id'];
    $title = $_POST['title'];
    $date = $_POST['date'];

    // Database connection
    $host = 'localhost';
    $dbname = 'pet_db';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update query
        $query = "UPDATE tbl_calendar SET title = :title WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':title' => $title, ':id' => $eventId]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
