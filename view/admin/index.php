<?php 

/////////////////////////////////////////////////////
session_start(); // Ensure the session is started  
if(isset($_SESSION['admin'])) {
    header("Location: pages/dashboard.php"); 
}
/////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow" style="width: 500px;">
            <div class="card-header bg-danger text-white text-center">
                <h4>Admin Login</h4>
            </div>
            <div class="card-body">
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="userUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="userUsername" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter your password" required>
                    </div>
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-danger w-100  mb-3">Login</button>
                        <a href="../../index.php" class="btn btn-success w-100">Back to Main Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>