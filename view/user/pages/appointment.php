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
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="prevMonth" class="btn btn-primary">Previous</button>
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

    <!-- Modal for adding events -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventDate" class="form-label">Date</label>
                        <input type="text" class="form-control" id="eventDate" readonly>
                    </div>
                    <input type="hidden" class="form-control" id="eventTime" readonly>
                    
                    <div class="mb-3">
                        <label for="eventService" class="form-label">Service</label>
                        <select class="form-select" id="eventService" required>
                            <option value="" disabled selected>Select a service</option>
                            <option value="Deworming">Deworming</option>
                            <option value="Vaccination">Vaccination</option>
                            <option value="Checkup">Checkup</option>
                            <option value="Grooming">Grooming</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="eventService" class="form-label">Pet</label>
                        <select class="form-select" id="eventService" required>
                            <option value="" disabled selected>Select a Pet</option>
                            <option value="PET-001">Bogart</option>
                            <option value="PET-002">Max</option>
                            <option value="PET-003">Snow</option>
                            <option value="PET-004">Blacky</option>
                        </select>
                    </div>
                 
                    <div class="mb-3">
                        <label for="eventSymptoms" class="form-label">Symptoms</label>
                        <textarea class="form-control" id="eventSymptoms" rows="3" placeholder="Describe symptoms..."></textarea>
                    </div>   
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
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
        const prevMonth = document.getElementById("prevMonth");
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
            const response = await fetch("slot_available_json.php");
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
                        const remainingSlots = totalSlots - slot.count; // Calculate remaining slots
                        const slotEl = document.createElement("div");
                        slotEl.className = "slot-count";
                        slotEl.textContent = `Remaining Slots: ${remainingSlots}`;
                        slotEl.style.fontSize = "0.9em";
                        slotEl.style.color = remainingSlots > 0 ? "#007bff" : "#dc3545"; // Highlight if no slots left
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
            e.preventDefault();
            const newEvent = {
                date: eventDateInput.value,
                title: eventTitleInput.value,
            };
            events.push(newEvent);
            eventModal.hide();
            renderCalendar(currentDate);
        });

        prevMonth.addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            fetchSlots(); // Re-fetch slots for the new month
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
