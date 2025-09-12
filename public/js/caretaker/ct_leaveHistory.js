// Dummy leave history data
const leaveData = [
  { type: "Vacation", start: "2025-01-10", end: "2025-01-12", reason: "Family trip", status: "approved" },
  { type: "Sick Leave", start: "2025-02-05", end: "2025-02-06", reason: "Fever", status: "approved" },
  { type: "Personal", start: "2025-03-15", end: "2025-03-15", reason: "Urgent errand", status: "pending" },
  { type: "Vacation", start: "2025-04-20", end: "2025-04-25", reason: "Travel", status: "rejected" },
  { type: "Sick Leave", start: "2025-05-02", end: "2025-05-03", reason: "Flu", status: "approved" },
  // Add more dummy rows as needed
];

// Pagination variables
let currentPage = 1;
const rowsPerPage = 5;

// Render table rows
function renderTable(page) {
  const tableBody = document.getElementById("leaveTableBody");
  tableBody.innerHTML = "";
  
  const start = (page - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const paginatedData = leaveData.slice(start, end);

  paginatedData.forEach(leave => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${leave.type}</td>
      <td>${leave.start}</td>
      <td>${leave.end}</td>
      <td>${leave.reason}</td>
      <td><span class="status ${leave.status}">${leave.status.charAt(0).toUpperCase() + leave.status.slice(1)}</span></td>
    `;
    tableBody.appendChild(tr);
  });

  // Update page info
  document.getElementById("pageInfo").textContent = `Page ${currentPage} of ${Math.ceil(leaveData.length / rowsPerPage)}`;
}

// Pagination buttons
document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    renderTable(currentPage);
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  if (currentPage < Math.ceil(leaveData.length / rowsPerPage)) {
    currentPage++;
    renderTable(currentPage);
  }
});

// Initial render
renderTable(currentPage);
