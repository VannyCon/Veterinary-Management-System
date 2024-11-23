<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h1 class="text-warning">User Approval</h1>
        <p>Here, you can manage users, view reports, and perform administrative tasks.</p>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example dummy rows -->
                                <tr>
                                    <td>1</td>
                                    <td>USER001</td>
                                    <td>johndoe</td>
                                    <td>password123</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">Approved</a>
                                        <a href="#" class="btn btn-sm btn-danger">Decline</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>USER002</td>
                                    <td>janedoe</td>
                                    <td>securepass456</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">Approved</a>
                                        <a href="#" class="btn btn-sm btn-danger">Decline</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>USER003</td>
                                    <td>marksmith</td>
                                    <td>letmein789</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">Approved</a>
                                        <a href="#" class="btn btn-sm btn-danger">Decline</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>USER004</td>
                                    <td>emilybrown</td>
                                    <td>mypassword321</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">Approved</a>
                                        <a href="#" class="btn btn-sm btn-danger">Decline</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>USER005</td>
                                    <td>alicejones</td>
                                    <td>qwerty123</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-success">Approved</a>
                                        <a href="#" class="btn btn-sm btn-danger">Decline</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>


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
