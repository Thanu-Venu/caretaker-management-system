// Dummy service data
const services = [
  { client: "Mrs Johnson", service: "Elder Care", date: "2025-08-01", hours: 4, payment: 700 },
  { client: "The Smith Family", service: "Babysitting", date: "2025-08-03", hours: 6, payment: 1200 },
  { client: "Mr Davis", service: "Maid Service", date: "2025-08-05", hours: 5, payment: 900 }
];

// Render service table
function renderServiceTable() {
  const tbody = document.getElementById("serviceTableBody");
  tbody.innerHTML = "";
  services.forEach(s => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${s.client}</td>
      <td>${s.service}</td>
      <td>${s.date}</td>
      <td>${s.hours}</td>
      <td>${s.payment}</td>
    `;
    tbody.appendChild(tr);
  });

  updateSummary();
}

// Update monthly summary
function updateSummary() {
  const totalServices = services.length;
  const totalHours = services.reduce((sum, s) => sum + s.hours, 0);
  const totalEarnings = services.reduce((sum, s) => sum + s.payment, 0);

  document.getElementById("totalServices").textContent = totalServices;
  document.getElementById("totalHours").textContent = totalHours;
  document.getElementById("totalEarnings").textContent = totalEarnings;
}

// Download CSV
document.getElementById("downloadReport").addEventListener("click", function() {
  let csvContent = "data:text/csv;charset=utf-8,";
  csvContent += "Client,Service,Date,Hours,Payment\n";
  services.forEach(s => {
    csvContent += `${s.client},${s.service},${s.date},${s.hours},${s.payment}\n`;
  });
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", "caretaker_report.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});

// Initial render
renderServiceTable();
