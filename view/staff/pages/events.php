<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die("Session admin is not set. Please log in again.");
}
$userid = $_SESSION['admin'];
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

    <link rel="stylesheet" href="../../../assets/vendors/simple-datatables/style.css">

    <link rel="stylesheet" href="../../../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="../../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="../../../assets/css/app.css">
    <link rel="shortcut icon" href="../../../assets/images/favicon.svg" type="image/x-icon">


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
    </style>
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
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
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

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-stack"></i>
                                <span>Appointment</span>
                            </a>
                            <ul class="submenu ">
                                <li class="submenu-item ">
                                    <a href="transactions.php">Pending</a>
                                </li>
                                <li class="submenu-item ">
                                    <a href="approved.php">Approve</a>
                                </li>
                              
                            </ul>

                            <li class="sidebar-item  ">
                            <a href="all_user.php" class='sidebar-link'>
                            <i class="bi bi-pen-fill"></i>
                                <span>Pet Record</span>
                            </a>
                        </li>
                    
                        <li class="sidebar-item active ">
                            <a href="events.php" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Events</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Manage User &amp; Staff</li>

                        <li class="sidebar-item  has-sub ">
                            <a href="" class='sidebar-link'>
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
                        </li>
                        <div class="logout-btn text-center" style="padding: 50px;">
                    <a href="logout.php" class="btn btn-primary btn-block mt-4 d-flex align-items-center justify-content-center" style="padding: 8px 12px;">
                        <i class="fa fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a> 
            

                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
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
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Events</h3>
                            <p class="text-subtitle text-muted">For user to check they list</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">DataTable</li>
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

<!-- Modal for adding appointments -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="event_maker.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date</label>
                        <input type="text" class="form-control" id="eventDate" name="date" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Event</label>
                        <input type="text" class="form-control" id="title" name="title">
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

<!-- Modal for editing events -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editEventForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editEventDate" class="form-label">Date</label>
                        <input type="text" class="form-control" id="editEventDate" name="date" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Event</label>
                        <input type="text" class="form-control" id="editTitle" name="title">
                    </div>
                    <input type="hidden" id="editEventId" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-danger" id="deleteEventButton">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for success/error messages -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="messageModalText">The event has been saved successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const monthYear = document.getElementById("monthYear");
    const calendarBody = document.getElementById("calendarBody");
    const nextMonth = document.getElementById("nextMonth");
    const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
    const eventDateInput = document.getElementById("eventDate");
    const editEventModal = new bootstrap.Modal(document.getElementById("editEventModal"));
    const editEventDateInput = document.getElementById("editEventDate");
    const editTitleInput = document.getElementById("editTitle");
    const editEventIdInput = document.getElementById("editEventId");
    const deleteEventButton = document.getElementById("deleteEventButton");

    let currentDate = new Date();
    let events = []; // Declare events as a global variable

    // Fetch events from a JSON source
    async function fetchEvents() {
        try {
            const response = await fetch("calendar_data_json.php");
            events = await response.json(); // Store events after fetching
            renderCalendar(currentDate); // Render calendar after fetching
        } catch (error) {
            console.error("Error fetching events:", error);
        }
    }

    // Render the calendar
    function renderCalendar(date) {
    calendarBody.innerHTML = "";
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    monthYear.textContent = date.toLocaleDateString("default", { month: "long", year: "numeric" });

    let row = document.createElement("tr");

    // Fill the calendar with empty cells for days before the start of the month
    for (let i = 0; i < firstDay; i++) {
        row.appendChild(document.createElement("td"));
    }

    // Create cells for each day of the month
    for (let day = 1; day <= lastDate; day++) {
        let cell = document.createElement("td");
        cell.classList.add("day-cell");
        cell.textContent = day;

        const currentDayDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Check if the day has any events
        const event = events.find(e => e.date === currentDayDate);
        if (event) {
            const eventDiv = document.createElement("div");
            eventDiv.classList.add("event");
            eventDiv.textContent = event.title;
            cell.appendChild(eventDiv);

            // Add click handler to edit the event
            cell.classList.add("holiday");
            cell.onclick = () => {
                editEventDateInput.value = currentDayDate;
                editTitleInput.value = event.title;
                editEventIdInput.value = event.id;
                editEventModal.show();
            };
        } else {
            // Add event handler for adding new events
            cell.onclick = () => {
                eventDateInput.value = currentDayDate;
                eventModal.show();
            };
        }

        row.appendChild(cell);
        if (row.children.length === 7) {
            calendarBody.appendChild(row);
            row = document.createElement("tr");
        }
    }

    // Add the remaining row if necessary
    if (row.children.length > 0) {
        calendarBody.appendChild(row);
    }
}

    // Event listener for next month
    nextMonth.addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        fetchEvents(); // Fetch events for the new month
    });

    // Event listener for editing the event
    document.getElementById("editEventForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        const eventData = new FormData(e.target);
        const eventId = editEventIdInput.value;
        const updatedTitle = editTitleInput.value;

        // Update the event in the database via AJAX
        try {
            await fetch('update_event.php', {
                method: 'POST',
                body: eventData
            });
            
            fetchEvents(); // Re-render calendar after update
            editEventModal.hide();
        } catch (error) {
            
        }
    });

    // Delete event handler
    deleteEventButton.addEventListener("click", async () => {
        const eventId = editEventIdInput.value;
        try {
            await fetch('delete_event.php', {
                method: 'POST',
                body: JSON.stringify({ id: eventId }),
                headers: { 'Content-Type': 'application/json' }
            });
           
            fetchEvents(); // Re-render calendar after delete
            editEventModal.hide();
        } catch (error) {

        }
    });
    // Event listener for "Back" button
document.getElementById("prevMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1); // Go to the previous month
    fetchEvents(); // Fetch events for the updated month
});

// Event listener for "Today" button
document.getElementById("today").addEventListener("click", () => {
    currentDate = new Date(); // Reset to the current date
    fetchEvents(); // Fetch events for the current month
});

// Event listener for "Next" button (already implemented)
nextMonth.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1); // Go to the next month
    fetchEvents(); // Fetch events for the new month
});


    // Initialize the calendar
    fetchEvents();
    
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
