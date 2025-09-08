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
