function filterTable() {
  const typeFilter = document.getElementById("type").value.toLowerCase();
  const statusFilter = document.getElementById("status").value.toLowerCase();

  const table = document.getElementById("leaveTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    const type = cells[1].innerText.toLowerCase();
    const status = cells[4].innerText.toLowerCase();

    let typeMatch = (typeFilter === "all" || type === typeFilter);
    let statusMatch = (statusFilter === "all" || status.includes(statusFilter));

    if (typeMatch && statusMatch) {
      rows[i].style.display = "";
    } else {
      rows[i].style.display = "none";
    }
  }
}
function applyFilters() {
  const type = document.getElementById('type').value;
  const status = document.getElementById('status').value;

  const params = new URLSearchParams();
  params.set("page", "1"); // reset to first page
  params.set("type", type);
  params.set("status", status);

  window.location = window.location.pathname + "?" + params.toString();
}