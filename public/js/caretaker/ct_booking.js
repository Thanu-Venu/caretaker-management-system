
// Populate tables
function loadBookings() {
  const upcomingTable = document.getElementById("upcomingBookings");
  const pastTable = document.getElementById("pastBookings");

  upcoming.forEach(b => {
    upcomingTable.innerHTML += `
      <tr>
        <td>${b.client}</td>
        <td>${b.service}</td>
        <td>${b.location}</td>
        <td>${b.date}</td>
        <td>${b.payment}</td>
      </tr>`;
  });

  past.forEach(b => {
    pastTable.innerHTML += `
      <tr>
        <td>${b.client}</td>
        <td>${b.service}</td>
        <td>${b.location}</td>
        <td>${b.date}</td>
        <td>${b.payment}</td>
      </tr>`;
  });
}

// Switch tabs
function showTab(tabName, event) {
  // Hide all tab contents
  document.querySelectorAll(".tab-content").forEach(tab => {
    tab.classList.remove("active");
  });

  // Remove active class from all buttons
  document.querySelectorAll(".top button").forEach(btn => {
    btn.classList.remove("active");
  });

  // Show the clicked tab
  document.getElementById(tabName).classList.add("active");

  
  // Highlight active button
  event.target.classList.add("active");
  
}

document.addEventListener("DOMContentLoaded", loadBookings);
