


// Example caretaker profile  data
let caretaker = {
  name: "Sarah Johnson",
  experience: "Elder care specialist with 8 years of compassionate service.",
  qualifications: "Elder Care, Medication Management, Mobility Assistance"
};

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

// Generate calendar dates (for September 2025)
// --- Calendar widget (dynamic current month) ---
function renderMiniCalendar(year, monthIndex) {
  // monthIndex: 0 = Jan, 11 = Dec
  const calendarDates = document.getElementById("calendarDates");
  const titleEl = document.getElementById("calendarMonthTitle");

  if (!calendarDates) return;

  // clear old cells
  calendarDates.innerHTML = "";

  const firstOfMonth = new Date(year, monthIndex, 1);
  const lastOfMonth = new Date(year, monthIndex + 1, 0); // last day of month
  const daysInMonth = lastOfMonth.getDate();

  // 0=Sun..6=Sat (matches your Su Mo Tu We Th Fr Sa header)
  const firstDayIndex = firstOfMonth.getDay();

  // Set title (e.g., March 2026)
  if (titleEl) {
    const monthName = firstOfMonth.toLocaleString("en-US", { month: "long" });
    titleEl.textContent = `${monthName} ${year}`;
  }

  // Add blank cells before day 1
  for (let i = 0; i < firstDayIndex; i++) {
    const blank = document.createElement("div");
    blank.classList.add("empty");
    calendarDates.appendChild(blank);
  }

  const today = new Date();
  const isCurrentMonth =
    today.getFullYear() === year && today.getMonth() === monthIndex;

  // Add date cells
  for (let d = 1; d <= daysInMonth; d++) {
    const dateEl = document.createElement("div");
    dateEl.classList.add("date");
    dateEl.textContent = d;

    if (isCurrentMonth && d === today.getDate()) {
      dateEl.classList.add("active"); // highlight today
    }

    calendarDates.appendChild(dateEl);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const now = new Date();
  renderMiniCalendar(now.getFullYear(), now.getMonth());
});
