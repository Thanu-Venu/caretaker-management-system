
const logs = [
  { timestamp: "2024-01-10 10:00 AM", iso: "2024-01-15T10:00:00", user: "Emily Carter", role: "Admin", action: "Created new caregiver profile", section: "Caregivers" },
  { timestamp: "2024-01-11 11:30 AM", iso: "2024-01-15T11:30:00", user: "David Lee", role: "Manager", action: "Updated client information", section: "Clients" },
  { timestamp: "2024-01-12 01:45 PM", iso: "2024-01-15T13:45:00", user: "Sarah Jones", role: "Client", action: "Submitted availability", section: "Schedules" },
  { timestamp: "2024-01-13 03:20 PM", iso: "2024-01-15T15:20:00", user: "Michael Brown", role: "Admin", action: "Generated invoice", section: "Invoices" },
  { timestamp: "2024-01-16 09:15 AM", iso: "2024-01-16T09:15:00", user: "Emily Carter", role: "Admin", action: "Viewed reports", section: "Reports" },
  { timestamp: "2024-01-15 10:00 AM", iso: "2024-01-15T10:00:00", user: "Emily Carter", role: "Client", action: "Created new caregiver profile", section: "Caregivers" },
  { timestamp: "2024-01-14 11:30 AM", iso: "2024-01-15T11:30:00", user: "David Lee", role: "Manager", action: "Updated client information", section: "Clients" },
  { timestamp: "2024-01-15 01:45 PM", iso: "2024-01-15T13:45:00", user: "Sarah Jones", role: "Client", action: "Submitted availability", section: "Schedules" },
  { timestamp: "2024-01-19 03:20 PM", iso: "2024-01-15T15:20:00", user: "Michael Brown", role: "Client", action: "Generated invoice", section: "Invoices" },
  { timestamp: "2024-01-16 09:15 AM", iso: "2024-01-16T09:15:00", user: "Emily Carter", role: "Admin", action: "Viewed reports", section: "Reports" },
  { timestamp: "2024-01-16 10:40 AM", iso: "2024-01-16T10:40:00", user: "David Lee", role: "Manager", action: "Modified schedule", section: "Schedules" },
  { timestamp: "2024-01-16 12:55 PM", iso: "2024-01-16T12:55:00", user: "Sarah Jones", role: "Caregiver", action: "Checked client details", section: "Clients" },
  { timestamp: "2024-01-16 02:10 PM", iso: "2024-01-16T14:10:00", user: "Michael Brown", role: "Admin", action: "Processed payment", section: "Invoices" },
  { timestamp: "2024-01-17 08:00 AM", iso: "2024-01-17T08:00:00", user: "Emily Carter", role: "Admin", action: "Updated system settings", section: "Settings" }
];

const tableBody = document.querySelector("#logTable tbody");
const roleFilter = document.getElementById("roleFilter");
const userFilter = document.getElementById("userFilter");
const dateFilter = document.getElementById("dateFilter");
const actionFilter = document.getElementById("actionFilter");

function populateDropdowns() {
  const users = [...new Set(logs.map(l => l.user))].sort();
  const actions = [...new Set(logs.map(l => l.action))].sort();

  users.forEach(u => {
    const option = document.createElement("option");
    option.value = u;
    option.textContent = u;
    userFilter.appendChild(option);
  });

  actions.forEach(a => {
    const option = document.createElement("option");
    option.value = a;
    option.textContent = a;
    actionFilter.appendChild(option);
  });
}

function renderTable(data) {
  tableBody.innerHTML = "";
  if (data.length === 0) {
    tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px">No records found</td></tr>`;
    return;
  }
  data.forEach(log => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${log.timestamp}</td>
      <td>${log.user}</td>
      <td>${log.role}</td>
      <td>${log.action}</td>
      <td>${log.section}</td>
    `;
    tableBody.appendChild(tr);
  });
}

function applyFilters() {
  let filtered = [...logs];

  if (roleFilter.value) filtered = filtered.filter(l => l.role === roleFilter.value);
  if (userFilter.value) filtered = filtered.filter(l => l.user === userFilter.value);
  if (actionFilter.value) filtered = filtered.filter(l => l.action === actionFilter.value);
   
  //dt chnges 
   if (dateFilter.value === "asc") {
    filtered.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
  } else if (dateFilter.value === "desc") {
    filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
  }



  renderTable(filtered);
}

// Add event listeners
roleFilter.addEventListener("change", applyFilters);
userFilter.addEventListener("change", applyFilters);
dateFilter.addEventListener("change", applyFilters);
actionFilter.addEventListener("change", applyFilters);

// Init
populateDropdowns();
renderTable(logs);
