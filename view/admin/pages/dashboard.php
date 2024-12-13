<?php
/////////////////////////////////////////////////////
session_start(); // Ensure the session is started  
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
}
/////////////////////////////////////////////////////0



$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    // Establish the database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to count pending users
    $pendingQuery = "SELECT COUNT(*) AS pending_count FROM tbl_user WHERE isApproved IS NULL";
    $pendingResult = $pdo->query($pendingQuery);
   
    if ($pendingResult) {
        $row = $pendingResult->fetch(PDO::FETCH_ASSOC);
        $pendingCount = $row ? $row['pending_count'] : 0;
    } else {
        $pendingCount = 0; // Default to 0 if query fails
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$staffCount = 0;
try {
    // Query to count staff members
    $staffQuery = "SELECT COUNT(*) AS staff_count FROM tbl_staff";  // Replace with your actual table and column names
    $staffResult = $pdo->query($staffQuery);
    if ($staffResult) {
        $row = $staffResult->fetch(PDO::FETCH_ASSOC);
        $staffCount = $row ? $row['staff_count'] : 0;
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

   
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="../../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../assets/css/app.css">
    <link rel="shortcut icon" href="../../../assets/images/favicon.svg" type="image/x-icon">

</head>
<style>
    .day-cell {
        cursor: pointer;
        height: 80px;
        vertical-align: top;
        width: 90px;
    }

    .day-cell.today {
        background-color: #d4edda;
    }

    .event {
        background-color: #f8d7da;
        color: #721c24;
        border-radius: 5px;
        padding: 2px 5px;
        margin-top: 5px;
        font-size: 0.8em;
    }

    .appointment-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
        width: 100%;
    }

    .day-cell:hover {
        background-color: #f0f0f0;
    }
</style>

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
                            <div class="toggler">
                                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="transactions.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="transactions.php">Pending</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="approved.php">Approved</a>
                                </li>

                            </ul>

                            <li class="sidebar-item  ">
                            <a href="all_user.php" class='sidebar-link'>
                            <i class="bi bi-pen-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>



                        <li class="sidebar-item  ">
                            <a href="events.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item  has-sub ">
                            <a href="users.php" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>User Request</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="users.php">Pending Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="user_approve_index.php">Approved Account</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="user_decline_index.php">Declined Account</a>
                                </li>


                            </ul>
                        

                        <li class="sidebar-item  ">
                            <a href="staff.php" class='sidebar-link'>
                                <i class="bi bi-file-earmark-medical-fill"></i>
                                <span>Staff</span>
                            </a>
                        </li>
                        <div class="logout-btn text-center" style="padding: 50px;">
                            <a href="logout.php" class="btn btn-primary btn-block mt-4 d-flex align-items-center justify-content-center" style="padding: 8px 12px;">
                                <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                            </a>

                        </div>
                        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
                </div>
            </div>
            <div id="main">
                <header class="mb-3">
                    <a href="#" class="burger-btn d-block d-xl-none">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                </header>
                <div class="page-heading">
                </div>
                <div class="page-content">
                    <section class="row">
                        <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12 col-lg-12 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                                <h1 class="text-danger ">Welcome admin!</h1>
                                                <p>Here, you can manage users, view reports, and perform administrative tasks.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-6 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                                <h5 class="card-title">Manage Users Request</h5>
                                                <p class="card-text">There are <span style="font-size: 1.2em; font-weight: bold; color: red;"><?php echo $pendingCount; ?></span> Pending user accounts for approval or decline.</p>
                                                <a href="users.php" class="btn btn-danger">View</a>


            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-6 col-md-2">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-6">
                <h5 class="card-title">Manage Staffs </h5>
                <p class="card-text">You have <span style="font-size: 1.2em; font-weight: bold; color: #d9534f;"><?php echo $staffCount; ?></span> staff members.</p>
                <a href="staff.php" class="btn btn-danger">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-12 col-md-12">
                                    <div class="card">
                                        <div class="card-body px-3 py-4-5">
                                            <div class="row">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <button id="backBtn" class="btn btn-primary me-2">Back</button>
                                                        <button id="todayBtn" class="btn btn-success">Today</button>
                                                    </div>
                                                    <h2 id="monthYear"></h2>
                                                    <button id="nextMonth" class="btn btn-primary">Next</button>

                                                </div>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Sun</th>
                                                            <th>Mon</th>
                                                            <th>Tue</th>
                                                            <th>Wed</th>
                                                            <th>Thu</th>
                                                            <th>Fri</th>
                                                            <th>Sat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="calendarBody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                    </section>


                 
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
            <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

            <script src="assets/vendors/apexcharts/apexcharts.js"></script>
            <script src="../../../assets/js/pages/dashboard.js"></script>

            <script src="../../../assets/js/main.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                const monthYear = document.getElementById("monthYear");
                const calendarBody = document.getElementById("calendarBody");
                const nextMonth = document.getElementById("nextMonth");
                const backBtn = document.getElementById("backBtn");
                const todayBtn = document.getElementById("todayBtn");

                let currentDate = new Date();
                let events = [];

                // Fetch events from a JSON source
                async function fetchEvents() {
                    const response = await fetch("calendar_data_json.php");
                    events = await response.json();
                    renderCalendar(currentDate);
                }

                // Replace with slot availability data
                async function fetchSlots() {
                    const response = await fetch("appoinment_calendar_json.php");
                    const slots = await response.json();
                    renderCalendar(currentDate, slots);
                }

                function renderCalendar(date, slots) {
                    calendarBody.innerHTML = "";
                    const year = date.getFullYear();
                    const month = date.getMonth();
                    const firstDay = new Date(year, month, 1).getDay();
                    const lastDate = new Date(year, month + 1, 0).getDate();

                    monthYear.textContent = date.toLocaleDateString("default", {
                        month: "long",
                        year: "numeric"
                    });

                    let row = document.createElement("tr");
                    for (let i = 0; i < firstDay; i++) {
                        row.appendChild(document.createElement("td"));
                    }

                    for (let day = 1; day <= lastDate; day++) {
                        const cell = document.createElement("td");
                        cell.className = "day-cell";
                        const cellDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

                        // Create a link element
                        const link = document.createElement("a");
                        link.href = `appointment_transactions.php?date=${cellDate}`; // Link to the appointments page
                        link.className = "appointment-link";

                        // Add the day number
                        const dayNumber = document.createElement("div");
                        dayNumber.textContent = day;
                        link.appendChild(dayNumber);

                        if (day === date.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
                            cell.classList.add("today");
                        }

                        // Check for events
                        const dayEvents = events.filter(event => event.date === cellDate);
                        let isHoliday = false;
                        dayEvents.forEach(event => {
                            if (event.title != null) {
                                isHoliday = true;
                                cell.classList.add("holiday");
                                cell.style.cursor = "not-allowed";

                                const eventEl = document.createElement("div");
                                eventEl.className = "event";
                                eventEl.textContent = event.title;
                                link.appendChild(eventEl);
                            }
                        });

                        // Add slot information
                        const slot = slots?.find(slot => slot.date === cellDate);
                        if (slot) {
                            const remainingSlots = slot.count;
                            const slotEl = document.createElement("div");
                            slotEl.className = "slot-count";
                            slotEl.textContent = `${remainingSlots}`;
                            slotEl.style.fontSize = "25px";
                            slotEl.style.fontWeight = "700";
                            slotEl.style.color = remainingSlots > 0 ? "#20b8d6" : "#dc3545";
                            slotEl.style.textAlign = "center";
                            link.appendChild(slotEl);
                        }

                        // Only add the link if it's not a holiday
                        if (!isHoliday) {
                            cell.appendChild(link);
                        } else {
                            cell.textContent = day;
                        }

                        row.appendChild(cell);

                        if ((firstDay + day) % 7 === 0) {
                            calendarBody.appendChild(row);
                            row = document.createElement("tr");
                        }
                    }
                    calendarBody.appendChild(row);
                }

                nextMonth.addEventListener("click", () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    fetchSlots();
                });

                backBtn.addEventListener("click", () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    fetchSlots();
                });

                todayBtn.addEventListener("click", () => {
                    currentDate = new Date();
                    fetchSlots();
                });

                // Initial fetch and render
                fetchSlots();
            </script>


            <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
            <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

            <script src="../../../assets/vendors/simple-datatables/simple-datatables.js"></script>


            <script src="../../../assets/js/main.js"></script>
            <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
            <script src="../../../js/bootstrap.bundle.min.js"></script>
</body>

</html>