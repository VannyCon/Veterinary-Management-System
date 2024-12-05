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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const monthYear = document.getElementById("monthYear");
        const calendarBody = document.getElementById("calendarBody");
        const nextMonth = document.getElementById("nextMonth");
        
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

            monthYear.textContent = date.toLocaleDateString("default", { month: "long", year: "numeric" });

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

        // Initial fetch and render
        fetchSlots();
    </script>
</body>
</html>