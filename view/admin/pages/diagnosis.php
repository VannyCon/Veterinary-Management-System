<?php
session_start();
// Database connection settings
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {

    
    // Generate Unique Appointment ID
    function generateDiagnosis() {
        $prefix = "DIAGNOSIS-";
        $timestamp = microtime(true);
        $randomNumber = mt_rand(100000, 999999);
        $uniqueHash = hash('sha256', $timestamp . $randomNumber);
        return $prefix . strtoupper(substr($uniqueHash, 0, 10));
    }


    $diagnosis = generateDiagnosis();

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare("INSERT INTO tbl_diagnosis 
            (diagnosis_id, appointment_id, pet_id, pet_diagnosis, pet_medication_prescribe, pet_doctor_notes, isComplete) 
            VALUES (?, ?, ?, ?, ?, ?, 1)");
        
        $stmt->execute([
            $diagnosis,
            $_POST['appointment_id'],
            $_POST['pet_id'],
            $_POST['pet_diagnosis'],
            $_POST['pet_medication_prescribe'],
            $_POST['pet_doctor_notes']
        ]);

        $_SESSION['success'] = "Diagnosis added successfully!";
        header("Location: approved.php"); // Error: Headers already sent
        exit();
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Diagnosis Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Pet Diagnosis Form</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_SESSION['success'])) {
                            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                            unset($_SESSION['success']);
                        }
                        ?>
                        
                        <form action="" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" class="form-control" name="appointment_id" id="appointment_id" value="<?php echo $_GET['appointment_id']?>">
                            <input type="hidden" class="form-control" name="pet_id" id="pet_id" value="<?php echo $_GET['pet_id']?>">
                            <div class="form-group mb-3">
                                <label for="pet_name" class="form-label">Pet Name</label>
                                <input type="text" class="form-control" id="pet_name" value="<?php echo $_GET['pet_name']?>" readonly>
                                <div class="invalid-feedback">
                                    Please provide a pet ID.
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pet_diagnosis" class="form-label">Pet Diagnosis</label>
                                <textarea class="form-control" id="pet_diagnosis" name="pet_diagnosis" rows="3" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide a diagnosis.
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pet_medication_prescribe" class="form-label">Medication Prescribed</label>
                                <textarea class="form-control" id="pet_medication_prescribe" name="pet_medication_prescribe" rows="3" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide medication details.
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="pet_doctor_notes" class="form-label">Doctor's Notes</label>
                                <textarea class="form-control" id="pet_doctor_notes" name="pet_doctor_notes" rows="3" required></textarea>
                                <div class="invalid-feedback">
                                    Please provide doctor's notes.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Submit Diagnosis</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation script
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>