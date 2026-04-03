function switchTab(tabId, event) {
  // Hide all tabs
  document.querySelectorAll(".tab-content").forEach(tab => {
    tab.classList.remove("active");
  });

  // Remove active from buttons
  document.querySelectorAll(".top-button").forEach(btn => {
    btn.classList.remove("active");
  });

  // Show selected tab
  document.getElementById(tabId).classList.add("active");

  // Highlight clicked button
  event.currentTarget.classList.add("active");
}