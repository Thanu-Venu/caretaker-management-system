// Dummy data for per service engagement
const serviceEngagements = [
  { caretaker:"Mary", client:"Mr. Silva", type:"Elder Care", basis:"Monthly", location:"Colombo", hours:120, rating:4.8, earnings:40000, status:"Active" },
  { caretaker:"Mary", client:"Mrs. Perera", type:"Babysitting", basis:"Hourly", location:"Colombo", hours:12, rating:4.7, earnings:6000, status:"Completed" },
  { caretaker:"Kamal", client:"Raj Family", type:"Babysitting", basis:"Weekly", location:"Kandy", hours:40, rating:4.5, earnings:15000, status:"Active" },
  { caretaker:"Sita", client:"Jaffna House", type:"Maid Service", basis:"Weekly", location:"Jaffna", hours:60, rating:4.6, earnings:20000, status:"Completed" }
];

// Populate Per Service Engagement Table
const serviceBody = document.getElementById("serviceEngagementBody");
serviceEngagements.forEach(s => {
  const row = `<tr>
    <td>${s.caretaker}</td>
    <td>${s.client}</td>
    <td>${s.type}</td>
    <td>${s.basis}</td>
    <td>${s.location}</td>
    <td>${s.hours}</td>
    <td>${s.rating}</td>
    <td>LKR ${s.earnings.toLocaleString()}</td>
    <td>${s.status}</td>
  </tr>`;
  serviceBody.innerHTML += row;
});

// Generate Caretaker Monthly Summary
const summaryMap = {};
serviceEngagements.forEach(s => {
  if(!summaryMap[s.caretaker]){
    summaryMap[s.caretaker] = { totalHours:0, totalEarnings:0, clients:new Set(), ratings:[] };
  }
  summaryMap[s.caretaker].totalHours += s.hours;
  summaryMap[s.caretaker].totalEarnings += s.earnings;
  summaryMap[s.caretaker].clients.add(s.client);
  summaryMap[s.caretaker].ratings.push(s.rating);
});

const summaryBody = document.getElementById("summaryBody");
for(let caretaker in summaryMap){
  const data = summaryMap[caretaker];
  const avgRating = (data.ratings.reduce((a,b)=>a+b,0)/data.ratings.length).toFixed(2);
  const row = `<tr>
    <td>${caretaker}</td>
    <td>${data.totalHours}</td>
    <td>LKR ${data.totalEarnings.toLocaleString()}</td>
    <td>${data.clients.size}</td>
    <td>${avgRating}</td>
  </tr>`;
  summaryBody.innerHTML += row;
}

// Charts
const ctx1 = document.getElementById("revenueChart").getContext("2d");
new Chart(ctx1, {
  type: "pie",
  data: {
    labels: ["Elder Care", "Babysitting", "Maid Services"],
    datasets: [{
      label: "Revenue",
      data: [40000+0, 6000+15000, 20000], // summed revenue by type
      backgroundColor: ["#00bfa5", "#1E88E5", "#FFC107"]
    }]
  }
});

const ctx2 = document.getElementById("bookingsChart").getContext("2d");
new Chart(ctx2, {
  type: "line",
  data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    datasets: [{
      label: "Bookings",
      data: [5, 8, 6, 10, 7, 12], // dummy monthly booking counts
      borderColor: "#1E88E5",
      backgroundColor: "rgba(30,136,229,0.2)",
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false
  }
});

// Dummy filter function
function applyFilters(){
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;
  alert(`Filtering reports from ${from} to ${to}`);
}
