<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userid = $_SESSION['user_id'];

// Database connection
$host = 'localhost';
$dbname = 'pet_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user's pets
    $stmt = $pdo->prepare("SELECT pet_id, pet_name FROM tbl_pet WHERE user_id = ?");
    $stmt->execute([$userid]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the services
    $serviceStmt = $pdo->query("SELECT service_id, service_name FROM tbl_service");
    $services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<p class="text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../../assets/vendors/iconly/bold.css">

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
            width: 80px;
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
        .holiday {
            background-color: #ffeeba;
            cursor: pointer;
        }

    table tbody td.day-cell {
        background-color: white; /* Light blue color for the days */
        border: 1px solid #b0e0e6; /* Slight border for separation */
    }

    /* Highlight the current day with a stronger blue */
    table tbody td.today {
        background-color: #89cff0;
        color: white;
        font-weight: bold;
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
                </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item  ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item active ">
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

                        <li class="sidebar-item  ">
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
             
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Appointment Calendar</h3>
                            <p class="text-subtitle text-muted">Cause the date of your appointment </p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Appointment</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                          
                        </div>
                                <div class="card-body">
                            <div class="container mt-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <button id="prevMonth" class="btn btn-primary me-2">Back</button>
                                    <button id="today" class="btn btn-success">Today</button>
                                </div>
                                <h2 id="monthYear"></h2>
                                <button id="nextMonth" class="btn btn-primary">Next</button>
                            </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
                                        </tr>
                                    </thead>
                                    <tbody id="calendarBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>




<!-- Modal for adding events -->
    <div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="appointment_process.php" method="post">
                        <div class="modal-body">
                            <input type="hidden" value="<?php echo $userid; ?>" class="form-control" id="ownerName" name="user_id" required>
                            <div class="mb-3">
                                <label for="eventDate" class="form-label">Date</label>
                                <input type="text" class="form-control" id="eventDate" name="created_date" readonly>
                            </div>
                            <input type="hidden" class="form-control" id="eventTime" name="created_time" readonly>

                            <div class="mb-3">
                                <label for="eventService" class="form-label">Service</label>
                                <select class="form-select" id="eventService" name="service_id" required>
                                    <option value="" disabled selected>Select a service</option>
                                    <?php
                                    foreach ($services as $service) {
                                        echo '<option value="' . htmlspecialchars($service['service_id']) . '">' . htmlspecialchars($service['service_name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="eventPet" class="form-label">Pet</label>
                                <select class="form-select" id="eventPet" name="pet_id" required>
                                    <option value="" disabled selected>Select a Pet</option>
                                    <?php foreach ($pets as $pet): ?>
                                        <option value="<?php echo $pet['pet_id']; ?>"><?php echo $pet['pet_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="eventSymptoms" class="form-label">Pet Concern</label>
                                <textarea class="form-control" id="eventSymptoms" name="pet_symptoms" rows="3" placeholder="Describe your Pet Concerns..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
        </div>
    </div>
</div>

<footer>
               
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const monthYear = document.getElementById("monthYear");
    const calendarBody = document.getElementById("calendarBody");
    const prevMonth = document.getElementById("prevMonth");
    const todayButton = document.getElementById("today");
    const nextMonth = document.getElementById("nextMonth");
    const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
    const eventDateInput = document.getElementById("eventDate");

    let currentDate = new Date();
    let events = [];

    async function fetchEventsAndSlots() {
        try {
            const eventResponse = await fetch("calendar_data_json.php");
            events = await eventResponse.json();

            const slotResponse = await fetch("slot_available_json.php");
            const slots = await slotResponse.json();

            renderCalendar(currentDate, slots);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    }

    async function renderCalendar(date, slots) {
        calendarBody.innerHTML = "";
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();

        monthYear.textContent = date.toLocaleDateString("default", { month: "long", year: "numeric" });

        let row = document.createElement("tr");
        for (let i = 0; i < firstDay; i++) {
            row.appendChild(document.createElement("td"));
        }

        for (let day = 1; day <= lastDate; day++) {
            const cell = document.createElement("td");
            cell.textContent = day;
            cell.className = "day-cell";
            const cellDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

            // Highlight today's date
            if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
                cell.classList.add("today");
            }

            // Check for holidays and events
            const dayEvents = events.filter(event => event.date === cellDate);
            let isHoliday = false;

            dayEvents.forEach(event => {
                const eventEl = document.createElement("div");
                eventEl.className = "event";
                eventEl.textContent = event.title;
                
                if (event.title) {
                    isHoliday = true;
                    cell.classList.add("holiday", "daycell-unclickable");
                    cell.title = "Holiday - No appointments available";
                    eventEl.style.backgroundColor = "#f5c1c1";
                    eventEl.style.color = "#2e2d2d";
                }
                
                cell.appendChild(eventEl);
            });

            // Add slot information only if it's not a holiday
            if (!isHoliday) {
                const slot = slots.find(slot => slot.date === cellDate);
                if (slot) {
                    const remainingSlots = 20 - slot.count;
                    const slotEl = document.createElement("div");
                    slotEl.className = remainingSlots > 0 ? "slot-count" : "slot-count no-slots";
                    slotEl.textContent = `Slots: ${remainingSlots}`;
                    cell.appendChild(slotEl);

                    // Make cell unclickable if no slots available
                    if (remainingSlots <= 0) {
                        cell.classList.add("daycell-unclickable");
                        cell.title = "No available slots";
                    }
                }

                // Add click event listener only if it's not a holiday and has available slots
                if (!cell.classList.contains("daycell-unclickable")) {
                    cell.addEventListener("click", () => {
                        eventDateInput.value = cellDate;
                        eventModal.show();
                    });
                }
            }

            row.appendChild(cell);

            if ((firstDay + day) % 7 === 0) {
                calendarBody.appendChild(row);
                row = document.createElement("tr");
            }
        }

        calendarBody.appendChild(row);
    }

    prevMonth.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        fetchEventsAndSlots();
    });

    todayButton.addEventListener("click", () => {
        currentDate = new Date();
        fetchEventsAndSlots();
    });

    nextMonth.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        fetchEventsAndSlots();
    });

    fetchEventsAndSlots(); // Initial fetch
</script>
<script src="../../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
                <script src="../../../assets/js/bootstrap.bundle.min.js"></script>

                <script src="../../../assets/vendors/apexcharts/apexcharts.js"></script>
                <script src="../../../assets/js/pages/dashboard.js"></script>

                <script src="../../../assets/js/main.js"></script>
            </body>

            </html>