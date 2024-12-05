<?php

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Function to get all incidents
    function getAllIncidentInfo($pdo) {
        try {
            // SQL query with joins and conditional logic
            $query = "
                SELECT 
                    DATE(`created_date`) AS `date`, 
                    COUNT(*) AS `count`
                FROM 
                    `tbl_appointment`
                WHERE 
                    MONTH(`created_date`) = MONTH(CURDATE()) 
                    AND YEAR(`created_date`) = YEAR(CURDATE())
                GROUP BY 
                    DATE(`created_date`)
                ORDER BY 
                    `date` ASC;
            ";
    
            // Prepare and execute the query
            $stmt = $pdo->prepare($query); 
            $stmt->execute(); 
            
            // Fetch all results
            $slots = $stmt->fetchAll(PDO::FETCH_ASSOC); 
            
            // Output results as JSON
            header('Content-Type: application/json');
            echo json_encode($slots); 
        } catch (PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Call the function and pass the PDO connection
    getAllIncidentInfo($pdo);

} catch (PDOException $e) {
    // Handle database connection errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>
