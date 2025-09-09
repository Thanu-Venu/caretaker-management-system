document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');

  // Sample events data (replace with DB data)
  const eventsData = [
    { title: 'John Smith: Elder Care', start: '2025-09-10', service: 'Elder Care', time: '09:00-13:00', status: 'Confirmed' },
    { title: 'Jane Doe: Babysitting', start: '2025-09-10', service: 'Babysitting', time: '14:00-18:00', status: 'Pending' },
    { title: 'Michael Lee: Cleaning', start: '2025-09-11', service: 'Cleaning & Cooking', time: '10:00-14:00', status: 'Confirmed' }
  ];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: eventsData,
    dateClick: function(info) {
      showSchedule(info.dateStr);
    }
  });

  calendar.render();

  // Modal handling
  const modal = document.getElementById("scheduleModal");
  const closeBtn = document.querySelector(".close");
  const tableBody = document.querySelector("#scheduleTable tbody");
  const modalDate = document.getElementById("modalDate");

  function showSchedule(dateStr) {
    modalDate.innerText = "Date: " + dateStr;
    tableBody.innerHTML = "";

    // Filter events for selected date
    const dailyEvents = eventsData.filter(ev => ev.start === dateStr);

    if(dailyEvents.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = `<td colspan="4">No scheduled caretakers</td>`;
      tableBody.appendChild(row);
    } else {
      dailyEvents.forEach(ev => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${ev.title.split(":")[0]}</td>
          <td>${ev.service}</td>
          <td>${ev.time}</td>
          <td>${ev.status}</td>
        `;
        tableBody.appendChild(row);
      });
    }

    modal.style.display = "flex";
  }

  closeBtn.onclick = () => modal.style.display = "none";
  window.onclick = (e) => { if(e.target === modal) modal.style.display = "none"; }
});
