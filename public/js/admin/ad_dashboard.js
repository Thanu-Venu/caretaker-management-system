const bookingData = window.DASHBOARD_DATA?.bookingStats || { labels: [], values: [] };
const engagementData = window.DASHBOARD_DATA?.engagement || { labels: [], values: [] };
const paymentStatusData = window.DASHBOARD_DATA?.paymentStatus || { labels: [], values: [] };
const bookingStatusPieData = window.DASHBOARD_DATA?.bookingStatusPie || { labels: [], values: [] };
const caretakerStatusData = window.DASHBOARD_DATA?.caretakerStatus || { labels: [], values: [] };

const CHART_COLORS = [
  "#1e88e5",
  "#00bfa5",
  "#7c3aed",
  "#f59e0b",
  "#ef4444",
  "#0d9488",
  "#64748b",
  "#ec4899",
];

function sumValues(arr) {
  return (arr || []).reduce(function (a, b) {
    return a + (Number(b) || 0);
  }, 0);
}

function pickColors(n) {
  const out = [];
  for (let i = 0; i < n; i++) {
    out.push(CHART_COLORS[i % CHART_COLORS.length]);
  }
  return out;
}

function initDoughnut(canvasId, emptyId, labels, values, fixedColors) {
  const canvas = document.getElementById(canvasId);
  const emptyEl = emptyId ? document.getElementById(emptyId) : null;
  if (!canvas) {
    return;
  }
  const total = sumValues(values);
  if (total <= 0 || !labels.length) {
    canvas.style.display = "none";
    if (emptyEl) {
      emptyEl.hidden = false;
    }
    return;
  }
  canvas.style.display = "block";
  if (emptyEl) {
    emptyEl.hidden = true;
  }
  const colors =
    fixedColors && fixedColors.length >= labels.length
      ? fixedColors.slice(0, labels.length)
      : pickColors(labels.length);
  const ctx = canvas.getContext("2d");
  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels,
      datasets: [
        {
          data: values,
          backgroundColor: colors,
          borderColor: "#fff",
          borderWidth: 2,
          hoverOffset: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "58%",
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            boxWidth: 11,
            padding: 12,
            maxWidth: 140,
            font: { size: 11, weight: "500" },
          },
        },
        tooltip: {
          callbacks: {
            label: function (ctx) {
              const v = ctx.parsed || 0;
              const sum = ctx.dataset.data.reduce(function (a, b) {
                return a + b;
              }, 0);
              const pct = sum ? Math.round((v / sum) * 100) : 0;
              return (ctx.label || "") + ": " + v + " (" + pct + "%)";
            },
          },
        },
      },
    },
  });
}

initDoughnut(
  "paymentStatusChart",
  "paymentStatusChartEmpty",
  paymentStatusData.labels || [],
  paymentStatusData.values || [],
  ["#f59e0b", "#00bfa5", "#ef4444"]
);

initDoughnut(
  "bookingStatusPieChart",
  "bookingStatusPieEmpty",
  bookingStatusPieData.labels || [],
  bookingStatusPieData.values || []
);

initDoughnut(
  "caretakerStatusChart",
  "caretakerStatusChartEmpty",
  caretakerStatusData.labels || [],
  caretakerStatusData.values || []
);

const bookingChartEl = document.getElementById("bookingChart");
if (bookingChartEl) {
  new Chart(bookingChartEl.getContext("2d"), {
    type: "line",
    data: {
      labels: bookingData.labels,
      datasets: [
        {
          label: "Bookings",
          data: bookingData.values,
          borderColor: "#1E88E5",
          fill: false,
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { left: 4, right: 8, top: 4, bottom: 4 } },
    },
  });
}

const engagementChartEl = document.getElementById("engagementChart");
if (engagementChartEl) {
  new Chart(engagementChartEl.getContext("2d"), {
    type: "bar",
    data: {
      labels: engagementData.labels,
      datasets: [
        {
          label: "Engagement",
          data: engagementData.values,
          backgroundColor: "#00BFA5",
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: { padding: { left: 4, right: 8, top: 4, bottom: 4 } },
    },
  });
}
