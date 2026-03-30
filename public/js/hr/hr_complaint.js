document.addEventListener("DOMContentLoaded", () => {
  // Ensure default tab is Client on page load
  const clientBtn = document.querySelector(".top button.active");
  const clientTab = document.getElementById("c_complaint");

  // Hide all tabs first
  document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
  // Show client tab
  if (clientTab) clientTab.classList.add("active");
});

function showTab(tabId, event) {
  document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
  document.querySelectorAll(".top button").forEach(btn => btn.classList.remove("active"));

  document.getElementById(tabId).classList.add("active");
  event.currentTarget.classList.add("active");
}