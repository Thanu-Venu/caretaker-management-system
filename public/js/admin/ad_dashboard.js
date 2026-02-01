const bookingData = window.DASHBOARD_DATA?.bookingStats || { labels: [], values: [] };
const engagementData = window.DASHBOARD_DATA?.engagement || { labels: [], values: [] };

// Booking Statistics Chart
const ctx1 = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx1, {
  type: 'line',
  data: {
    labels: bookingData.labels,
    datasets: [{
      label: 'Bookings',
      data: bookingData.values,
      borderColor: '#1E88E5',
      fill: false,
      tension: 0.3
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});

// Client Engagement Chart
const ctx2 = document.getElementById('engagementChart').getContext('2d');
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: engagementData.labels,
    datasets: [{
      label: 'Engagement',
      data: engagementData.values,
      backgroundColor: '#00BFA5'
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});

const searchInput = document.querySelector(".search-bar input");
const tableBody = document.getElementById("activityTable");

if (searchInput && tableBody) {
  searchInput.addEventListener("keyup", () => {
    const q = searchInput.value.toLowerCase();
    [...tableBody.querySelectorAll("tr")].forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(q) ? "" : "none";
    });
  });
}

