const tabButtons = document.querySelectorAll('.tab-btn');
const historySection = document.getElementById('historySection');
const pendingSection = document.getElementById('pendingSection');
const invoicesSection = document.getElementById('invoicesSection');

// Switch tabs
tabButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    tabButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    if (btn.dataset.tab === "history") {
      showSection("history");
      history.pushState(null, "", "?url=admin/payment_history");
    } else if (btn.dataset.tab === "pending") {
      showSection("pending");
      history.pushState(null, "", "?url=admin/pending_payment");
    } else {
      showSection("invoices");
      history.pushState(null, "", "?url=admin/invoices");
    }
  });
});

function showSection(section) {
  historySection.style.display = section === "history" ? "block" : "none";
  pendingSection.style.display = section === "pending" ? "block" : "none";
  invoicesSection.style.display = section === "invoices" ? "block" : "none";
}

// Load correct tab on page refresh
window.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const urlValue = params.get("url");

  if (urlValue === "admin/pending_payment") {
    switchTab("pending");
  } else if (urlValue === "admin/invoices") {
    switchTab("invoices");
  } else {
    switchTab("history");
  }
});

function switchTab(tabName) {
  tabButtons.forEach(b => b.classList.remove('active'));
  document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
  showSection(tabName);
}

// Search filtering
function setupSearch(inputId, tableId) {
  const searchInput = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  const rows = table.querySelectorAll("tbody tr");

  searchInput.addEventListener("keyup", () => {
    const filter = searchInput.value.toLowerCase();

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
}

setupSearch("historySearch", "historyTable");
setupSearch("pendingSearch", "pendingTable");
