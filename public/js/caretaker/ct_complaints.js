// Dummy complaint data
const complaints = [
  { client: "Mrs Johnson", service: "Elder Care", date: "2025-08-01", description: "Late payment", status: "pending" },
  { client: "The Smith Family", service: "Elder Care", date: "2025-07-20", description: "Short notice for task", status: "resolved" }
];

// Render table
function renderComplaints() {
  const tbody = document.getElementById("complaintTableBody");
  tbody.innerHTML = "";
  complaints.forEach(c => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${c.client}</td>
      <td>${c.service}</td>
      <td>${c.date}</td>
      <td>${c.description}</td>
      <td><span class="status ${c.status}">${c.status.charAt(0).toUpperCase() + c.status.slice(1)}</span></td>
    `;
    tbody.appendChild(tr);
  });
}

// Handle form submission
document.getElementById("complaintForm").addEventListener("submit", function(e){
  e.preventDefault();
  
  const client = document.getElementById("clientName").value;
  const service = document.getElementById("serviceType").value;
  const date = document.getElementById("dateOfService").value;
  const desc = document.getElementById("complaintDesc").value;

  if(client && service && date && desc){
    complaints.push({ client: client, service: service, date: date, description: desc, status: "pending" });
    renderComplaints();
    this.reset();
    alert("Complaint submitted successfully!");
  }
});

// Initial render
renderComplaints();
