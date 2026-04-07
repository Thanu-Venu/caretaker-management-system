function setActiveTab(tabName) {
  // Hide all tab contents
  document.querySelectorAll(".tab-content").forEach(tab => {
    tab.classList.remove("active");
  });

  // Remove active class from all buttons
  document.querySelectorAll(".top button").forEach(btn => {
    btn.classList.remove("active");
  });

  // Show selected tab
  const tab = document.getElementById(tabName);
  if (tab) {
    tab.classList.add("active");
  }

  // Highlight correct button (based on onclick attribute)
  document.querySelectorAll(".top button").forEach(btn => {
    if (btn.getAttribute("onclick")?.includes(tabName)) {
      btn.classList.add("active");
    }
  });
}

// ✅ THIS is the function your HTML is calling
function switchTab(tabName, event) {
  setActiveTab(tabName);

  if (event && event.target) {
    event.target.classList.add("active");
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const bookingId = params.get("booking_id");
  const tab = params.get("tab") || "ongoing";

  setActiveTab(tab); // ensure default tab loads

  if (bookingId) {
    const row = document.querySelector(`.booking-row[data-booking-id="${bookingId}"]`);
    if (row) {
      row.classList.add("highlight");
      row.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }
});