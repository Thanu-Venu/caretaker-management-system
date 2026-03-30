// Initialize Charts
let revenueChart = null;
let bookingsChart = null;

document.addEventListener('DOMContentLoaded', function () {
  initializeCharts();

  // Download Report button
  document.getElementById('downloadReport').addEventListener('click', downloadReport);
});

/**
 * Initialize Charts with data from PHP
 */
function initializeCharts() {
  // Revenue by Service Type Chart
  const ctx1 = document.getElementById("revenueChart").getContext("2d");

  if (revenueChart) {
    revenueChart.destroy();
  }

  revenueChart = new Chart(ctx1, {
    type: "pie",
    data: {
      labels: revenueChartData.labels,
      datasets: [{
        label: "Revenue",
        data: revenueChartData.data,
        backgroundColor: ["#00bfa5", "#1E88E5", "#FFC107", "#E53935", "#8E24AA"]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return context.label + ': LKR ' + context.parsed.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Monthly Bookings Trend Chart
  const ctx2 = document.getElementById("bookingsChart").getContext("2d");

  if (bookingsChart) {
    bookingsChart.destroy();
  }

  bookingsChart = new Chart(ctx2, {
    type: "line",
    data: {
      labels: bookingsTrendData.labels,
      datasets: [{
        label: "Bookings",
        data: bookingsTrendData.data,
        borderColor: "#1E88E5",
        backgroundColor: "rgba(30,136,229,0.2)",
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
}

/**
 * Apply date filters and reload data via AJAX
 */
function applyFilters() {
  const fromDate = document.getElementById("fromDate").value;
  const toDate = document.getElementById("toDate").value;

  if (!fromDate || !toDate) {
    alert("Please select both From and To dates.");
    return;
  }

  if (new Date(fromDate) > new Date(toDate)) {
    alert("From date cannot be after To date.");
    return;
  }

  // Reload page with query parameters
  window.location.href = `${URLROOT}/hr/hr_reports?from=${fromDate}&to=${toDate}`;
}

/**
 * Download Report as CSV
 */
function downloadReport() {
  const fromDate = document.getElementById("fromDate").value;
  const toDate = document.getElementById("toDate").value;

  // Get all data from the page
  const summary = {
    totalCaretakers: document.getElementById('caretakersCount').textContent,
    totalClients: document.getElementById('clientsCount').textContent,
    ongoingServices: document.getElementById('ongoingCount').textContent,
    totalRevenue: document.getElementById('revenueCount').textContent,
    pendingRequests: document.getElementById('pendingCount').textContent
  };

  // Get service engagement data
  const engagementRows = document.querySelectorAll('#serviceEngagementBody tr');
  const engagements = [];
  engagementRows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length > 1) {
      engagements.push({
        caretaker: cells[0].textContent,
        client: cells[1].textContent,
        serviceType: cells[2].textContent,
        basis: cells[3].textContent,
        location: cells[4].textContent,
        hours: cells[5].textContent,
        rating: cells[6].textContent,
        earnings: cells[7].textContent,
        status: cells[8].textContent
      });
    }
  });

  // Get caretaker summary data
  const summaryRows = document.querySelectorAll('#summaryBody tr');
  const caretakerSummary = [];
  summaryRows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length > 1) {
      caretakerSummary.push({
        caretaker: cells[0].textContent,
        totalHours: cells[1].textContent,
        totalEarnings: cells[2].textContent,
        numClients: cells[3].textContent,
        avgRating: cells[4].textContent
      });
    }
  });

  // Generate CSV content
  let csv = "SmartCare HR Reports\n\n";

  if (fromDate && toDate) {
    csv += `Date Range: ${fromDate} to ${toDate}\n\n`;
  } else {
    csv += "Date Range: All Time\n\n";
  }

  csv += "SUMMARY STATISTICS\n";
  csv += `Total Caregivers,${summary.totalCaretakers}\n`;
  csv += `Total Clients,${summary.totalClients}\n`;
  csv += `Ongoing Services,${summary.ongoingServices}\n`;
  csv += `Total Revenue,${summary.totalRevenue}\n`;
  csv += `Pending Requests,${summary.pendingRequests}\n\n`;

  csv += "PER SERVICE ENGAGEMENT\n";
  csv += "Caregiver,Client,Service Type,Basis,Location,Hours,Rating,Earnings,Status\n";
  engagements.forEach(e => {
    csv += `${e.caretaker},${e.client},${e.serviceType},${e.basis},${e.location},${e.hours},${e.rating},${e.earnings},${e.status}\n`;
  });

  csv += "\nCAREGIVER MONTHLY SUMMARY\n";
  csv += "Caregiver,Total Hours,Total Earnings,No. of Clients,Average Rating\n";
  caretakerSummary.forEach(c => {
    csv += `${c.caretaker},${c.totalHours},${c.totalEarnings},${c.numClients},${c.avgRating}\n`;
  });

  // Create download link
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  const url = URL.createObjectURL(blob);

  link.setAttribute('href', url);
  link.setAttribute('download', `hr_report_${new Date().toISOString().split('T')[0]}.csv`);
  link.style.visibility = 'hidden';

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
