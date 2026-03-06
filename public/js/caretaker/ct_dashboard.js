

function openProfile() {
  // Fill modal inputs with caretaker data
  document.getElementById('name').value = caretaker.name;
  document.getElementById('experience').value = caretaker.experience;
  document.getElementById('qualifications').value = caretaker.qualifications;

  // Show modal and blur dashboard
  document.getElementById('profileModal').style.display = 'flex';
  document.getElementById('dashboard').classList.add('blur');
}

function saveProfile() {
  // Save edited values back to JS object
  caretaker.name = document.getElementById('name').value;
  caretaker.experience = document.getElementById('experience').value;
  caretaker.qualifications = document.getElementById('qualifications').value;

  alert('Profile saved!');
  closeProfile();

  // Optional: update dashboard display if showing caretaker name
  document.getElementById('caretakerNameDisplay').textContent = caretaker.name;
}

function closeProfile() { 
   document.getElementById('profileModal').style.display = 'none';
    document.getElementById('dashboard').classList.remove('blur'); 
   }

   //leve mengement js
const leaveModal = document.getElementById("leaveModal");
const openLeaveBtn = document.getElementById("openLeaveModal");
const closeLeaveBtn = document.getElementById("closeLeaveModal");
const leaveForm = document.getElementById("leaveForm");

// Open leave modal
openLeaveBtn.onclick = () => {
  leaveModal.style.display = "flex";
};

// Close leave modal
closeLeaveBtn.onclick = () => {
  leaveModal.style.display = "none";
};

// Submit leave form
leaveForm.addEventListener("submit", (e) => {
  e.preventDefault();
  alert("Leave request submitted!");
  leaveModal.style.display = "none";
  leaveForm.reset();
});








// Toggle availability button state
document.querySelectorAll('.switch input').forEach(toggle => {
  toggle.addEventListener('change', function() {
    if(this.checked) {
      alert("You are now Available!");
    } else {
      alert("You are now Unavailable!");
    }
  });
});
// ===== Dashboard Calendar (dynamic) =====
const calendarDates = document.getElementById("calendarDates");
const monthLabel = document.getElementById("calendarMonthLabel");

if (calendarDates && monthLabel) {
  const bookingDatesArr = window.CT_DASHBOARD_BOOKING_DATES || [];
  const bookingDates = new Set(bookingDatesArr); // YYYY-MM-DD

  const today = new Date();
  const year = today.getFullYear();
  const month = today.getMonth(); // 0-11

  // Label: "March 2026"
  monthLabel.textContent = today.toLocaleDateString("en-US", { month: "long", year: "numeric" });

  // First day of month (0=Sun..6=Sat)
  const firstDow = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  // Clear
  calendarDates.innerHTML = "";

  // Add empty boxes before day 1
  for (let i = 0; i < firstDow; i++) {
    calendarDates.appendChild(document.createElement("div"));
  }

  // Render days
  for (let d = 1; d <= daysInMonth; d++) {
    const dateEl = document.createElement("div");
    dateEl.classList.add("date");
    dateEl.textContent = d;

    const ymd = `${year}-${String(month + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;

    // highlight today
    const isToday =
      d === today.getDate() &&
      month === today.getMonth() &&
      year === today.getFullYear();

    if (isToday) dateEl.classList.add("today");

    // highlight booking dates separately
    if (bookingDates.has(ymd)) dateEl.classList.add("has-booking");

    // click -> go to schedule page and show that date
    dateEl.addEventListener("click", () => {
      // send selected date as query param
      const scheduleUrl = `${window.URLROOT}/caretaker/ct_schedule?date=${encodeURIComponent(ymd)}`;
      window.location.href = scheduleUrl;
    });

    calendarDates.appendChild(dateEl);
  }
}