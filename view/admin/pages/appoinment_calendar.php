<?php
    session_start();
    $userid = $_SESSION['admin'];
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
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
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="approved.php">Approved Transaction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../../index.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <p class="">Appointment Area</p>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <!-- <button id="prevMonth" class="btn btn-primary">Previous</button> -->
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
                            <option value="SERVICE-001">Deworming</option>
                            <option value="SERVICE-002">Vaccination</option>
                            <option value="SERVICE-003">Checkup</option>
                            <option value="SERVICE-004">Grooming</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="eventPet" class="form-label">Pet</label>
                        <select class="form-select" id="eventPet" name="pet_id" required>
                            <option value="" disabled selected>Select a Pet</option>
                            <option value="PET-1001">Bogart</option>
                            <option value="PET-1002">Max</option>
                            <option value="PET-1003">Snow</option>
                            <option value="PET-1004">Blacky</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="eventSymptoms" class="form-label">Symptoms</label>
                        <textarea class="form-control" id="eventSymptoms" name="pet_symptoms" rows="3" placeholder="Describe symptoms..."></textarea>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        document.getElementById("eventModal").addEventListener("show.bs.modal", () => {
            const now = new Date();
            const currentTime = now.toISOString().slice(11, 16); // Extract HH:MM from current time
            document.getElementById("eventTime").value = currentTime;
        });


        const monthYear = document.getElementById("monthYear");
        const calendarBody = document.getElementById("calendarBody");
        const nextMonth = document.getElementById("nextMonth");
        const eventModal = new bootstrap.Modal(document.getElementById("eventModal"));
        const eventForm = document.getElementById("eventForm");
        const eventDateInput = document.getElementById("eventDate");
        const eventTitleInput = document.getElementById("eventTitle");

        let currentDate = new Date();
        let events = [];

        // Fetch events from a JSON source
        async function fetchEvents() {
            const response = await fetch("calendar_data_json.php");
            events = await response.json();
            renderCalendar(currentDate);
        }

        // Replace "calendar_data_json.php" with "slot_available.json"
        async function fetchSlots() {
            const response = await fetch("appoinment_calendar_json.php");
            const slots = await response.json();
            renderCalendar(currentDate, slots);
        }
        
        function renderCalendar(date, slots) {
                const totalSlots = 20; // Define the total slots available per day
                calendarBody.innerHTML = ""; // Clear previous cells

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

                    if (day === date.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
                        cell.classList.add("today");
                    }

                    // Check for events on the current day
                    const dayEvents = events.filter(event => event.date === cellDate);
                    let isHoliday = false;
                    dayEvents.forEach(event => {
                        const eventEl = document.createElement("div");
                        eventEl.className = "event";
                        eventEl.textContent = event.title;
                        cell.appendChild(eventEl);

                        // If the event is a "Holiday", disable the click
                        if (event.title != null) {
                            isHoliday = true;
                            cell.classList.add("holiday");
                            cell.style.cursor = "not-allowed"; // Update cursor style
                        }
                    });

                    if (!isHoliday) {
                        // Add click handler to add events if not a holiday
                        cell.addEventListener("click", () => {
                            eventDateInput.value = cellDate;
                            eventModal.show();
                        });
                    }

                    // Find the slot availability for the current date
                    const slot = slots.find(slot => slot.date === cellDate);
                    if (slot) {
                            const remainingSlots = slot.count; // Calculate remaining slots
                            const slotEl = document.createElement("div");
                            slotEl.className = "slot-count";
                            slotEl.textContent = `${remainingSlots}`;
                            slotEl.style.fontSize = "25px";
                            slotEl.style.fontWeight = "700";
                            slotEl.style.color = remainingSlots > 0 ? "#20b8d6" : "#dc3545"; // Highlight if no slots left

                            // Center the slotEl content
                            slotEl.style.display = "flex";
                            slotEl.style.justifyContent = "center";
                            slotEl.style.alignItems = "center";
                            slotEl.style.height = "40%"; // Ensure it takes full height of the parent
                            slotEl.style.width = "100%"; // Optional: if the parent container doesn't have fixed dimensions

                            cell.appendChild(slotEl);
                        }


                    row.appendChild(cell);

                    // Add row after every 7 cells (days)
                    if ((firstDay + day) % 7 === 0) {
                        calendarBody.appendChild(row);
                        row = document.createElement("tr");
                    }
                }
                calendarBody.appendChild(row);
            }

        // Modify the initial fetch and render
        fetchSlots();

        // Handle form submission for adding events
        eventForm.addEventListener("submit", (e) => {
            const newEvent = {
                date: eventDateInput.value,
                title: eventTitleInput.value,
            };
            events.push(newEvent);
            eventModal.hide();
            renderCalendar(currentDate);
        });


        nextMonth.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            fetchSlots(); // Re-fetch slots for the new month
        });

        // Initial fetch and render
        fetchEvents();
    </script>
</body>
</html>