// ==============================
// GLOBAL CHART VARIABLES
// ==============================
let revenueChart = null;
let bookingsChart = null;

// ==============================
// INITIAL LOAD
// ==============================
document.addEventListener("DOMContentLoaded", function () {
  initializeCharts();

  // Safe event binding
  const downloadBtn = document.getElementById("downloadReport");
  if (downloadBtn) {
    downloadBtn.addEventListener("click", downloadReport);
  }
});

// ==============================
// INITIALIZE CHARTS
// ==============================
function initializeCharts() {
  // -------- Revenue Chart --------
  const revenueCanvas = document.getElementById("revenueChart");
  if (revenueCanvas && typeof revenueChartData !== "undefined") {
    const ctx1 = revenueCanvas.getContext("2d");

    if (revenueChart) revenueChart.destroy();

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
        plugins: {
          legend: { position: "bottom" },
          tooltip: {
            callbacks: {
              label: function (context) {
                return context.label + ": LKR " + context.parsed.toLocaleString();
              }
            }
          }
        }
      }
    });
  }

  // -------- Bookings Chart --------
  const bookingsCanvas = document.getElementById("bookingsChart");
  if (bookingsCanvas && typeof bookingsTrendData !== "undefined") {
    const ctx2 = bookingsCanvas.getContext("2d");

    if (bookingsChart) bookingsChart.destroy();

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
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  }

  // -------- Other Charts --------
  safeChart("caretakerStatusChart", "doughnut", caretakerStatusRows, "status", "count", ['#00ab94','#ea0b34','#3b82f6','#a855f7'], "Caregivers");

  safeChart("serviceMixChart", "bar", caretakersByServiceRows, "service_type", "count", ['#2066a8','#3594cc','#8cc5e3','#0ea5e9','#9fd3eb'], "Caregivers");

  safeChart("leaveStatusChart", "pie", leaveRequestsRows, "status", "count", ['#00ab94','#2064d0','#f59e0b','#ea0b34'], "Leaves");

  safeChart("rescheduleStatusChart", "pie", rescheduleRequestRows, "status", "count", ['#f59e0b','#00ab94','#ef4444','#a855f7'], "Reschedules");
}

// ==============================
// GENERIC SAFE CHART CREATOR
// ==============================
function safeChart(id, type, dataRows, labelKey, valueKey, colors, label) {
  const el = document.getElementById(id);

  if (!el || !dataRows || !dataRows.length) return;

  new Chart(el, {
    type,
    data: {
      labels: dataRows.map(r => r[labelKey]),
      datasets: [{
        label,
        data: dataRows.map(r => Number(r[valueKey] || 0)),
        backgroundColor: colors,
        borderColor: "#1f2937",
        borderWidth: type === "line" ? 2 : 0,
        fill: type === "line"
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: "bottom" }
      },
      scales: (type === "line" || type === "bar") ? {
        y: { beginAtZero: true }
      } : {}
    }
  });
}

// ==============================
// APPLY FILTERS
// ==============================
function applyFilters() {
  const from = document.getElementById("fromDate")?.value;
  const to = document.getElementById("toDate")?.value;

  if (from && to && new Date(from) > new Date(to)) {
    alert("From date cannot be after To date.");
    return;
  }

  const params = new URLSearchParams();
  if (from) params.set("from", from);
  if (to) params.set("to", to);

  window.location.href = `${URLROOT}/hr/hr_reports?${params.toString()}`;
}

// ==============================
// DOWNLOAD CSV REPORT
// ==============================
function downloadReport() {
  const getText = (id) => document.getElementById(id)?.textContent || "";

  const summary = {
    totalCaretakers: getText("caretakersCount"),
    totalClients: getText("clientsCount"),
    ongoingServices: getText("ongoingCount"),
    totalRevenue: getText("revenueCount"),
    pendingRequests: getText("pendingCount")
  };

  let csv = "SmartCare HR Reports\n\n";

  csv += "SUMMARY\n";
  csv += `Total Caregivers,${summary.totalCaretakers}\n`;
  csv += `Total Clients,${summary.totalClients}\n`;
  csv += `Ongoing Services,${summary.ongoingServices}\n`;
  csv += `Total Revenue,${summary.totalRevenue}\n`;
  csv += `Pending Requests,${summary.pendingRequests}\n`;

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");

  link.href = URL.createObjectURL(blob);
  link.download = `hr_report_${new Date().toISOString().split("T")[0]}.csv`;

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function exportReport(type) {
  if (type === 'csv') {
    downloadReport();
  } else if (type === 'pdf') {
    printReport();
  }
}

function printReport() {
  convertChartsToImages(); // keep charts visible

  const content = document.getElementById("reportContent");

  const printWindow = window.open('', '', 'width=1000,height=800');

  printWindow.document.write(`
    <html>
      <head>
        <title>HR Report</title>

        <!-- 🔥 include your real CSS -->
        <link rel="stylesheet" href="${URLROOT}/public/css/hr/hr_reports.css">

        <style>
          body { font-family: Arial; padding: 20px; }

          /* FORCE cards to be visible */
          .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
          }

          .card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 30%;
            margin-bottom: 10px;
          }

          .card-label {
            display: block;
            font-size: 12px;
            color: #555;
          }

          .card-value {
            font-size: 18px;
            font-weight: bold;
          }

          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }

          table, th, td {
            border: 1px solid #ccc;
          }

          th, td {
            padding: 8px;
          }

          h3 {
            margin-top: 20px;
          }
        </style>
      </head>

      <body>
        ${content.innerHTML}
      </body>
    </html>
  `);

  printWindow.document.close();

  printWindow.onload = function () {
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  };
}

function convertChartsToImages() {
  document.querySelectorAll("canvas").forEach(canvas => {
    const img = document.createElement("img");
    img.src = canvas.toDataURL("image/png");
    img.style.width = "100%";
    canvas.replaceWith(img);
  });
}