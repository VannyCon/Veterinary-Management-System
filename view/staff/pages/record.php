<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approved.php">Approved Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-danger">Diagnosis</h1>
        <p>Here, you can manage users, view reports, and perform administrative tasks.</p>


        <!-- THIS PART WILL NEED APT WHERE DISPLAY ALL THE INFORMATION THEN PASS EACH PETID -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pet Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> 1</p>
                                <p><strong>Pet Name:</strong> Buddy</p>
                                <p><strong>Pet Species:</strong> Dog</p>
                                <p><strong>Pet Age:</strong> 3 years</p>
                            </div>
                        </div>
                        <br>
                        <div class="card p-3">
                            <form action="/submit-appointment-pet" method="POST">
                                <div class="mb-3">
                                    <label for="appointment_id" class="form-label">Appointment ID</label>
                                    <input type="text" id="appointment_id" name="appointment_id" class="form-control" placeholder="Enter Appointment ID">
                                </div>
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">User ID</label>
                                    <input type="text" id="user_id" name="user_id" class="form-control" placeholder="Enter User ID">
                                </div>
                                <div class="mb-3">
                                    <label for="pet_id" class="form-label">Pet ID</label>
                                    <input type="text" id="pet_id" name="pet_id" class="form-control" placeholder="Enter Pet ID">
                                </div>
                                <div class="mb-3">
                                    <label for="pet_symptoms" class="form-label">Pet Symptoms</label>
                                    <textarea id="pet_symptoms" name="pet_symptoms" class="form-control" rows="4" placeholder="Describe symptoms"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>

                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pet Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> 1</p>
                                <p><strong>Pet Name:</strong> Buddy</p>
                                <p><strong>Pet Species:</strong> Dog</p>
                                <p><strong>Pet Age:</strong> 3 years</p>
                            </div>
                        </div>
                        <br>
                        <div class="card p-3">
                            <form action="/submit-appointment-pet" method="POST">
                                <div class="mb-3">
                                    <label for="appointment_id" class="form-label">Appointment ID</label>
                                    <input type="text" id="appointment_id" name="appointment_id" class="form-control" placeholder="Enter Appointment ID">
                                </div>
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">User ID</label>
                                    <input type="text" id="user_id" name="user_id" class="form-control" placeholder="Enter User ID">
                                </div>
                                <div class="mb-3">
                                    <label for="pet_id" class="form-label">Pet ID</label>
                                    <input type="text" id="pet_id" name="pet_id" class="form-control" placeholder="Enter Pet ID">
                                </div>
                                <div class="mb-3">
                                    <label for="pet_symptoms" class="form-label">Pet Symptoms</label>
                                    <textarea id="pet_symptoms" name="pet_symptoms" class="form-control" rows="4" placeholder="Describe symptoms"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>

                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
