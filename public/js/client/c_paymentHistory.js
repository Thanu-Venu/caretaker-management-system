const statusFilter = document.getElementById("statusFilter");
const serviceFilter = document.getElementById("serviceFilter");
const searchInput = document.getElementById("searchInput");
const table = document.getElementById("paymentTable");
const rows = table.querySelectorAll("tbody tr");
const noResults = document.getElementById("noResults");

function filterTable() {
  const statusValue = statusFilter.value;
  const serviceValue = serviceFilter.value.toLowerCase();
  const searchValue = searchInput.value.toLowerCase();

  let visibleCount = 0;

  rows.forEach(row => {
    const rowStatus = row.getAttribute("data-status");
    const rowService = row.getAttribute("data-service").toLowerCase();
    const rowText = row.innerText.toLowerCase();

    let matchesStatus = statusValue === "" || rowStatus === statusValue;
    let matchesService = serviceValue === "" || rowService === serviceValue;
    let matchesSearch = searchValue === "" || rowText.includes(searchValue);

    if (matchesStatus && matchesService && matchesSearch) {
      row.style.display = "";
      visibleCount++;
    } else {
      row.style.display = "none";
    }
  });

  noResults.style.display = visibleCount === 0 ? "block" : "none";
}

statusFilter.addEventListener("change", filterTable);
serviceFilter.addEventListener("change", filterTable);
searchInput.addEventListener("input", filterTable);
