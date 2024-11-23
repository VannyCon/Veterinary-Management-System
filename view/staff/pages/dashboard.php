<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Staff Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h1 class="text-warning">Welcome, Staff Member!</h1>
        <p>Here, you can view your assigned tasks and report progress.</p>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Appointment Task</h5>
                <p class="card-text">Check your assigned tasks for today.</p>
                <a href="transactions.php" class="btn btn-warning">View Tasks</a>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Diagnosis Task</h5>
                <p class="card-text">Check your assigned tasks for today.</p>
                <a href="approved.php" class="btn btn-warning">View Tasks</a>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">User Request</h5>
                <p class="card-text">Check your assigned tasks for today.</p>
                <a href="user_approval.php" class="btn btn-warning">View Tasks</a>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Manage User</h5>
                <p class="card-text">Check your assigned tasks for today.</p>
                <a href="#" class="btn btn-warning">View Tasks</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
