<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the user is logged in
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['error'] = "You must be logged in to add a pet.";
        header('Location: login.php');
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pet_id = uniqid('PET_'); // Generate unique pet ID
        $user_id = $_SESSION['user_id'];
        $pet_name = $_POST['pet_name'];
        $pet_species = $_POST['pet_species'];
        $pet_age = $_POST['pet_age'];

        // Insert pet data into the database
        $query = "INSERT INTO `tbl_pet` (`pet_id`, `user_id`, `pet_name`, `pet_species`, `pet_age`) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$pet_id, $user_id, $pet_name, $pet_species, $pet_age]);

        // Redirect to dashboard with success message
        $_SESSION['message'] = "Pet added successfully!";
        header('Location: dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-primary">Add a New Pet</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="pet_name" class="form-label">Pet Name</label>
                <input type="text" class="form-control" id="pet_name" name="pet_name" required>
            </div>
            <div class="mb-3">
                <label for="pet_species" class="form-label">Pet Species</label>
                <input type="text" class="form-control" id="pet_species" name="pet_species" required>
            </div>
            <div class="mb-3">
                <label for="pet_age" class="form-label">Pet Age (months or years)</label>
                <input type="text" class="form-control" id="pet_age" name="pet_age" min="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Pet</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
