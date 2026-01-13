


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
const calendarDates = document.getElementById("calendarDates");
const daysInSeptember = 30;
const firstDay = 1; // Monday = 1

for (let i = 0; i < firstDay; i++) {
  calendarDates.appendChild(document.createElement("div"));
}
for (let d = 1; d <= daysInSeptember; d++) {
  const dateEl = document.createElement("div");
  dateEl.classList.add("date");
  dateEl.textContent = d;
  if (d === 25) dateEl.classList.add("active");
  calendarDates.appendChild(dateEl);
}
