<?php
session_start(); // Start the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}

$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user information
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM tbl_user WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Set variables for profile edit
        $fullname = $user['fullname'];
        $address = $user['address'];
        $phone_number = $user['phone_number'];
        $username = $user['username'];
    } else {
        echo '<p class="text-danger">User not found.</p>';
        exit;
    }

    // Handle form submission to update profile
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get posted data
        $updated_fullname = $_POST['fullname'];
        $updated_address = $_POST['address'];
        $updated_phone_number = $_POST['phone_number'];
        $updated_username = $_POST['username'];
        $new_password = $_POST['new_password'];
        $current_password = $_POST['current_password'];

        // Check if the user wants to change the password
        if (!empty($new_password)) {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Hash new password before storing it
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                
                // Update the password in the database
                $updateQuery = "UPDATE tbl_user SET fullname = ?, address = ?, phone_number = ?, username = ?, password = ? WHERE user_id = ?";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute([$updated_fullname, $updated_address, $updated_phone_number, $updated_username, $hashed_password, $user_id]);

                // Set a session variable for success message
                $_SESSION['profile_update_success'] = 'Your profile and password have been successfully updated!';
            } else {
                $_SESSION['error_message'] = 'Current password is incorrect.';
            }
        } else {
            // If password was not changed, just update other details
            $updateQuery = "UPDATE tbl_user SET fullname = ?, address = ?, phone_number = ?, username = ? WHERE user_id = ?";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([$updated_fullname, $updated_address, $updated_phone_number, $updated_username, $user_id]);

            // Set a session variable for success message
            $_SESSION['profile_update_success'] = 'Your profile has been successfully updated!';
        }

        // Redirect to profile view page after update
        header("Location: profile_view.php");
        exit;
    }
} catch (PDOException $e) {
    echo '<p class="text-danger">Error fetching user data: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadiz City Veterinary Office</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../../assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="../../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../assets/css/app.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="shortcut icon" href="../../../assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                    <div class="logo">
                    <a href="dashboard.php">
                        <img src="../../../assets/images/logo/vetoff.png" alt="Logo" srcset="" style="width: 230px; height: auto"> <!-- Adjust width as needed -->
                    </a>
                </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="appointment.php" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Appointment</span>
                            </a>
                        </li>

                        <li class="sidebar-item  ">
                            <a href="transaction.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Transaction</span>
                            </a>
                        </li>

                        <li class="sidebar-item active  ">
                            <a href="profile_view.php" class='sidebar-link'>
                                <i class="bi bi-image-fill"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                    </ul>
                    <div class="logout-btn text-center" style="padding: 50px;">
                    <a href="../logout.php" class="btn btn-primary btn-block mt-4 d-flex align-items-center justify-content-center" style="padding: 8px 12px;">
                        <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a>
                </div>
            </div>
        </div>
        </div>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Your Profile</h3>
                            <p class="text-subtitle text-muted">View and Edit Your profile</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section id="multiple-column-form">
                    <div class="row match-height">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">You Can Edit your Profile</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <!-- Success message -->
                                        

                                        <form action="edit_profile.php" method="POST">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="fullname" class="form-label">Full Name</label>
                                                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="address" class="form-label">Address</label>
                                                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="phone_number" class="form-label">Phone Number</label>
                                                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="username" class="form-label">Username</label>
                                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                <label for="current_password">Enter New Password:</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                                                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

           
    <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

    <script src="../../../assets/js/main.js"></script>
    <script>
    // Toggle password visibility
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    togglePassword.addEventListener("click", function() {
        // Toggle the input type between password and text
        const type = password.type === "password" ? "text" : "password";
        password.type = type;
        
        // Toggle the eye icon
        eyeIcon.classList.toggle("fa-eye");
        eyeIcon.classList.toggle("fa-eye-slash");
    });
</script>
</body>

</html>
