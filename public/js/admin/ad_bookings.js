function filterTable() {
  const typeFilter = document.getElementById("type").value.toLowerCase();
  const dateFilter = document.getElementById("date").value; // YYYY-MM-DD
  const statusFilter = document.getElementById("status").value.toLowerCase();

  const rows = document.querySelectorAll("#bookingTable tbody tr");

  rows.forEach(row => {
    const cells = row.getElementsByTagName("td");
    if (!cells.length) return;

    const type = (cells[3]?.textContent || "").trim().toLowerCase();   // Service Type
    const date = (cells[4]?.textContent || "").trim();                // Booking Date
    const status = (cells[5]?.textContent || "").trim().toLowerCase();// Status

    const typeMatch = (typeFilter === "all" || type === typeFilter);
    const dateMatch = (dateFilter === "" || date === dateFilter);
    const statusMatch = (statusFilter === "all" || status.includes(statusFilter));

    row.style.display = (typeMatch && dateMatch && statusMatch) ? "" : "none";
  });
}
