function setActiveTab(tabName) {
  // Hide all tab contents
  document.querySelectorAll(".tab-content").forEach(tab => {
    tab.classList.remove("active");
  });

  // Remove active class from all buttons
  document.querySelectorAll(".top button").forEach(btn => {
    btn.classList.remove("active");
  });

  // Show the selected tab
  const tab = document.getElementById(tabName);
  if (tab) {
    tab.classList.add("active");
  }

  // Highlight matching button
  const btn = document.querySelector(`.top button[data-tab="${tabName}"]`);
  if (btn) {
    btn.classList.add("active");
  }
}

// Switch tabs (click handler)
function showTab(tabName, event) {
  setActiveTab(tabName);

  // Ensure clicked button is active if provided
  if (event?.target) {
    event.target.classList.add("active");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const bookingId = params.get("booking_id");
  const tab = params.get("tab") || "ongoing";

  if (bookingId) {
    setActiveTab(tab);
    const row = document.querySelector(`.booking-row[data-booking-id="${bookingId}"]`);
    if (row) {
      row.classList.add("highlight");
      row.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }
});
