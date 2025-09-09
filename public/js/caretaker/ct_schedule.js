document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const modal = document.getElementById('scheduleModal');
  const closeBtn = document.querySelector('.modal .close');
  const modalDate = document.getElementById('modalDate');
  const scheduleTableBody = document.querySelector('#scheduleTable tbody');

  // Dummy caretaker schedules
  const schedules = {
    "2025-09-10": [
      { caretaker: "John Doe", service: "Elder Care", time: "09:00 - 12:00", status: "Approved" },
      { caretaker: "Jane Smith", service: "Elder Care", time: "14:00 - 17:00", status: "Pending" }
    ],
    "2025-09-12": [
      { caretaker: "Alice Brown", service: "Elder Care", time: "10:00 - 15:00", status: "Approved" },
      { caretaker: "Mark Wilson", service: "Elder Care", time: "16:00 - 19:00", status: "Rejected" }
    ],
    "2025-09-15": [
      { caretaker: "Maria Lopez", service: "Elder Care", time: "08:00 - 11:00", status: "Approved" }
    ]
  };

  // Convert schedules into FullCalendar events (for glimpse)
  const events = [];
  Object.keys(schedules).forEach(date => {
    schedules[date].forEach(sch => {
      events.push({
        title: sch.service + " - " + sch.caretaker,
        start: date,
        color: (sch.status === "Approved") ? "#2ecc71" :
               (sch.status === "Pending") ? "#f1c40f" :
               "#e74c3c"
      });
    });
  });

  // Initialize calendar
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    editable: false,
    height: 'auto',
    events: events,

    // Click on a date → show all schedules for that date
    dateClick: function (info) {
      showSchedule(info.dateStr);
    },

    // Click on an event → also show schedule for that date
    eventClick: function (info) {
      const clickedDate = info.event.startStr;
      showSchedule(clickedDate);
    }
  });

  calendar.render();

  // Function to show schedule in modal
  function showSchedule(dateStr) {
    modalDate.textContent = "Date: " + dateStr;
    scheduleTableBody.innerHTML = "";

    if (schedules[dateStr]) {
      schedules[dateStr].forEach(sch => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${sch.caretaker}</td>
          <td>${sch.service}</td>
          <td>${sch.time}</td>
          <td>
            <span class="status-badge ${sch.status.toLowerCase()}">
              ${sch.status}
            </span>
          </td>
        `;
        scheduleTableBody.appendChild(row);
      });
    } else {
      const row = document.createElement("tr");
      row.innerHTML = `<td colspan="4">No schedules for this date.</td>`;
      scheduleTableBody.appendChild(row);
    }

    modal.style.display = "block";
  }

  // Close modal
  closeBtn.onclick = function () {
    modal.style.display = "none";
  };
  window.onclick = function (event) {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  };
});
