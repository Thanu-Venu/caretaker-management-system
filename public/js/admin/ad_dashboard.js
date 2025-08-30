// Booking Statistics Chart
const ctx1 = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx1, {
  type: 'line',
  data: {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
    datasets: [{
      label: 'Bookings',
      data: [30, 40, 35, 50],
      borderColor: '#1E88E5',
      fill: false,
      tension: 0.3
    }]
  }
});

// Client Engagement Chart
const ctx2 = document.getElementById('engagementChart').getContext('2d');
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Engagement',
      data: [100, 120, 150, 130, 160, 180],
      backgroundColor: '#00BFA5'
    }]
  }
});
