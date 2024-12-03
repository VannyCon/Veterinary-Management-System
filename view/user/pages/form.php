<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Service Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Pet Service Form</h1>
        <div class="card shadow">
            <div class="card-body">
                <form action="submit_service.php" method="POST">
                    <!-- Date -->
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <!-- Time -->
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>

                    <!-- Pet ID -->
                    <div class="mb-3">
                        <label for="petId" class="form-label">Pet ID</label>
                        <input type="text" class="form-control" id="petId" name="pet_id" placeholder="Enter pet ID" required>
                    </div>

                    <!-- Service ID -->
                    <div class="mb-3">
                        <label for="serviceId" class="form-label">Service ID</label>
                        <input type="text" class="form-control" id="serviceId" name="service_id" placeholder="Enter service ID" required>
                    </div>

                    <!-- Symptoms -->
                    <div class="mb-3">
                        <label for="symptoms" class="form-label">Symptoms</label>
                        <textarea class="form-control" id="symptoms" name="symptoms" rows="3" placeholder="Describe the symptoms" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.n
