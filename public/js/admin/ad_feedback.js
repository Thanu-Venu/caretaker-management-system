// ================= TAB HANDLING =================
const tabButtons = document.querySelectorAll(".tab-btn");
const feedbackSection = document.getElementById("feedbackSection");
const complaintsSection = document.getElementById("complaintsSection");

// Function to switch tabs
function switchTab(tab) {
  if (!feedbackSection || !complaintsSection) return;

  tabButtons.forEach(btn => btn.classList.remove("active"));

  if (tab === "complaints") {
    const c = document.querySelector('[data-tab="complaints"]');
    if (c) c.classList.add("active");
    feedbackSection.style.display = "none";
    complaintsSection.style.display = "block";

    history.pushState(null, "", "?url=admin/ad_feedback&tab=complaints");
  } else {
    const f = document.querySelector('[data-tab="feedback"]');
    if (f) f.classList.add("active");
    feedbackSection.style.display = "block";
    complaintsSection.style.display = "none";

    history.pushState(null, "", "?url=admin/ad_feedback&tab=feedback");
  }
}

// Button click events
if (tabButtons.length && feedbackSection && complaintsSection) {
  tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      switchTab(btn.dataset.tab);
    });
  });

  window.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get("tab");

    if (tab === "complaints") {
      switchTab("complaints");
    } else {
      switchTab("feedback");
    }
  });
}


// ================= FEEDBACK FILTERING =================
const ratingFilter = document.getElementById("ratingFilter");
const dateFilter = document.getElementById("dateFilter");
const table = document.getElementById("feedbackTable");
const noResults = document.getElementById("noResults");

if (table && ratingFilter && dateFilter && noResults) {
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
      const rawDate = (dateCell.getAttribute("data-date") || "").trim();
      const rowDateKey = rawDate.slice(0, 10);

      const matchesRating = ratingValue === "" || rowRating === ratingValue;
      const matchesDate = dateValue === "" || rowDateKey === dateValue;

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
