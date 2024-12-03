<?php
// Assuming the event ID is passed as JSON
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id'])) {
    $eventId = $data['id'];

    // Database connection
    $host = 'localhost';
    $dbname = 'system_db';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Delete query
        $query = "DELETE FROM tbl_calendar WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $eventId]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
