<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar with Available Slots</title>
    <style>
        /* Simple styling for the calendar */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            width: 14.28%;
            height: 100px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #ddd;
        }
        .disabled {
            background-color: #f2f2f2;
            color: #ddd;
            cursor: not-allowed;
        }
        .today {
            background-color: #ffeb3b;
        }
        .event {
            font-size: 12px;
            color: #333;
            margin-top: 5px;
        }
        .holiday {
            background-color: #ffcccb;
        }
        .slot-left {
            font-size: 10px;
            color: green;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<h2>Available Slots Calendar</h2>
<table id="calendar"></table>

<script>
    let availableSlots = [];
    let unavailableSlots = [];
    let currentDate = new Date();
    const calendarBody = document.getElementById("calendar");
    const monthYear = document.createElement("h3");
    document.body.insertBefore(monthYear, calendarBody);

    // Fetch events, available slots, and unavailable slots from the PHP JSON endpoints
    async function fetchData() {
        const eventResponse = await fetch("calendar_data_json.php"); // Modify if needed for your events
        const events = await eventResponse.json();

        const availableSlotResponse = await fetch("available_slot_json.php");
        availableSlots = await availableSlotResponse.json();

        const unavailableSlotResponse = await fetch("unavailable_slot_json.php");
        unavailableSlots = await unavailableSlotResponse.json();

        renderCalendar(currentDate, events);
    }

    // Render calendar with events and availability logic
    function renderCalendar(date, events) {
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

            let isHoliday = false;
            const dayEvents = events.filter(event => event.date === cellDate);

            dayEvents.forEach(event => {
                const eventEl = document.createElement("div");
                eventEl.className = "event";
                eventEl.textContent = event.title;
                cell.appendChild(eventEl);

                // If the event is a "Holiday", disable the click
                if (event.title === "Holiday") {
                    isHoliday = true;
                    cell.classList.add("holiday");
                }
            });

            // Check if the date is unavailable
            if (unavailableSlots.some(slot => slot.date === cellDate)) {
                cell.classList.add("disabled");
            } else {
                // Find the available slot for this date
                const availableForDate = availableSlots.find(slot => slot.date === cellDate);
                if (availableForDate) {
                    const remainingSlots = availableForDate.slot;
                    if (remainingSlots === 0) {
                        // No slots available, disable the cell
                        cell.classList.add("disabled");
                    } else {
                        // Display the available slots in the cell
                        const slotText = document.createElement("div");
                        slotText.className = "slot-left";
                        slotText.textContent = `${remainingSlots} slots left`;
                        cell.appendChild(slotText);

                        // Add click handler to add events
                        cell.addEventListener("click", () => {
                            if (remainingSlots > 0) {
                                alert(`You can book a slot on ${cellDate}.`);
                            }
                        });
                    }
                } else {
                    // If there are no available slots for the date, disable the cell
                    cell.classList.add("disabled");
                }
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

    // Initialize calendar rendering
    fetchData();
</script>

</body>
</html>
