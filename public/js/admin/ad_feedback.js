const tabButtons = document.querySelectorAll('.tab-btn');
const feedbackSection = document.getElementById('feedbackSection');
const complaintsSection = document.getElementById('complaintsSection');

// Tab switching
tabButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    if (btn.dataset.tab === "feedback") {
      feedbackSection.style.display = "block";
      complaintsSection.style.display = "none";
      history.pushState(null, "", "?url=admin/ad_feedback");
    } else {
      feedbackSection.style.display = "none";
      complaintsSection.style.display = "block";
       history.pushState(null, "", "?url=admin/ad_complaints");
    }
  });
});
window.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const urlValue = params.get("url");

  if (urlValue === "admin/ad_complaints") {
    switchTab("complaints");
  } else {
    switchTab("feedback");
  }
});
// === Filtering for feedback table ===
const ratingFilter = document.getElementById("ratingFilter");
const dateFilter = document.getElementById("dateFilter");
const table = document.getElementById("feedbackTable");
const rows = table.querySelectorAll("tbody tr");
const noResults = document.getElementById("noResults");

function filterTable() {
  const ratingValue = ratingFilter.value;
  const dateValue = dateFilter.value;

  let visibleCount = 0;

  rows.forEach(row => {
    const rowRating = row.querySelector("td[data-rating]").getAttribute("data-rating");
    const rowDate = row.querySelector("td[data-date]").getAttribute("data-date");

    let matchesRating = ratingValue === "" || rowRating === ratingValue;
    let matchesDate = dateValue === "" || rowDate === dateValue;

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
