<?php
session_start();
$userid = $_SESSION['user_id']; // Assuming session contains user_id
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
        .holiday {
            background-color: #ffeeba;
            color: #856404;
            cursor: not-allowed;
        }
        .event {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            padding: 2px 5px;
            margin-top: 5px;
            font-size: 0.8em;
        }
        .slot-count {
            font-size: 0.9em;
            color: #007bff;
        }
        .slot-count.no-slots {
            color: #dc3545;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">User Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
                <li class="nav-item"><a class="nav-link active" href="appointment.php">Appointment</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
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

<!-- Modal for Adding/Viewing Appointments -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="appointment_process.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Add Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?php echo $userid; ?>">
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date</label>
                        <input type="text" class="form-control" id="eventDate" name="appointment_date" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="eventTime" class="form-label">Time</label>
                        <input type="time" class="form-control" id="eventTime" name="appointment_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDetails" class="form-label">Details</label>
                        <textarea class="form-control" id="eventDetails" name="details" rows="3" placeholder="Describe your appointment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Appointment</button>
                </div>
            </form>
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

    function renderCalendar(date, slots) {
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

            if (day === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
                cell.classList.add("today");
            }

            const dayEvents = events.filter(event => event.date === cellDate);
            let isHoliday = false;

            dayEvents.forEach(event => {
                const eventEl = document.createElement("div");
                eventEl.className = "event";
                eventEl.textContent = event.title;
                cell.appendChild(eventEl);

                if (event.title === "Holiday") {
                    isHoliday = true;
                    cell.classList.add("holiday");
                }
            });

            const slot = slots.find(slot => slot.date === cellDate);
            if (slot) {
                const remainingSlots = 20 - slot.count; // Assuming 20 total slots
                const slotEl = document.createElement("div");
                slotEl.className = remainingSlots > 0 ? "slot-count" : "slot-count no-slots";
                slotEl.textContent = `Slots: ${remainingSlots}`;
                cell.appendChild(slotEl);
            }

            if (!isHoliday) {
                cell.addEventListener("click", () => {
                    eventDateInput.value = cellDate;
                    eventModal.show();
                });
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
        fetchEventsAndSlots();
    });

    fetchEventsAndSlots(); // Initial fetch
</script>
</body>
</html>
