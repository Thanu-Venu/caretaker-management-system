// ================= TAB HANDLING =================
const tabButtons = document.querySelectorAll(".tab-btn");
const feedbackSection = document.getElementById("feedbackSection");
const complaintsSection = document.getElementById("complaintsSection");

// Function to switch tabs
function switchTab(tab) {
  tabButtons.forEach(btn => btn.classList.remove("active"));

  if (tab === "complaints") {
    document.querySelector('[data-tab="complaints"]').classList.add("active");
    feedbackSection.style.display = "none";
    complaintsSection.style.display = "block";

    history.pushState(null, "", "?url=admin/ad_feedback&tab=complaints");
  } else {
    document.querySelector('[data-tab="feedback"]').classList.add("active");
    feedbackSection.style.display = "block";
    complaintsSection.style.display = "none";

    history.pushState(null, "", "?url=admin/ad_feedback&tab=feedback");
  }
}

// Button click events
tabButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    switchTab(btn.dataset.tab);
  });
});

// Load correct tab on page refresh
window.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const tab = params.get("tab");

  if (tab === "complaints") {
    switchTab("complaints");
  } else {
    switchTab("feedback");
  }
});


// ================= FEEDBACK FILTERING =================
const ratingFilter = document.getElementById("ratingFilter");
const dateFilter = document.getElementById("dateFilter");
const table = document.getElementById("feedbackTable");
const noResults = document.getElementById("noResults");

if (table) {
  const rows = table.querySelectorAll("tbody tr");

  function filterTable() {
    const ratingValue = ratingFilter.value;
    const dateValue = dateFilter.value;

    let visibleCount = 0;

    rows.forEach(row => {
      const ratingCell = row.querySelector("td[data-rating]");
      const dateCell = row.querySelector("td[data-date]");

      if (!ratingCell || !dateCell) return;

      const rowRating = ratingCell.getAttribute("data-rating");
      const rowDate = dateCell.getAttribute("data-date");

      const matchesRating = ratingValue === "" || rowRating === ratingValue;
      const matchesDate = dateValue === "" || rowDate === dateValue;

      if (matchesRating && matchesDate) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    noResults.style.display = visibleCount === 0 ? "block" : "none";
  }

  ratingFilter.addEventListener("change", filterTable);
  dateFilter.addEventListener("change", filterTable);
}
