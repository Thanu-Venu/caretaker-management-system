// Dummy data
const upcoming = [
  { client: "Mrs. Davis", service: "Elder Care", location: "Vavuniya", date: "2024-01-20 8:00 AM - 5:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. John", service: "Elder Care", location: "Colombo", date: "2024-01-21 9:00 AM - 1:00 PM", payment: "LKR 10,000" },
  { client: "Mr. Bharath", service: "Elder Care", location: "Gampaha", date: "2024-01-22 9:00 AM - 1:00 PM", payment: "LKR 15,000" },
  { client: "Mrs. Davis", service: "Elder Care", location: "Vavuniya", date: "2024-01-23 8:00 AM - 5:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. John", service: "Elder Care", location: "Colombo", date: "2024-01-24 9:00 AM - 1:00 PM", payment: "LKR 10,000" },
  { client: "Mr. Bharath", service: "Elder Care", location: "Gampaha", date: "2024-01-25 9:00 AM - 1:00 PM", payment: "LKR 15,000" },
  { client: "Mrs. Kamal", service: "Elder Care", location: "Colombo", date: "2024-01-26 9:00 AM - 1:00 PM", payment: "LKR 18,000" }
  
];

const past = [
  { client: "Mrs. Kamal", service: "Elder Care", location: "Colombo", date: "2024-01-15 9:00 AM - 1:00 PM", payment: "LKR 18,000" },
  { client: "Mrs. Pirakash", service: "Elder Care", location: "Jaffna", date: "2024-01-10 9:00 AM - 1:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. Davis", service: "Elder Care", location: "Vavuniya", date: "2024-01-20 8:00 AM - 5:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. John", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 10,000" },
  { client: "Mr. Bharath", service: "Elder Care", location: "Gampaha", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 15,000" },
  { client: "Mrs. Kamal", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 18,000" },
  { client: "Mrs. Pirakash", service: "Elder Care", location: "Jaffna", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. Birana", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 25,000" },
  { client: "Mrs. Davis", service: "Elder Care", location: "Vavuniya", date: "2024-01-20 8:00 AM - 5:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. John", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 10,000" },
  { client: "Mr. Bharath", service: "Elder Care", location: "Gampaha", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 15,000" },
  { client: "Mrs. Kamal", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 18,000" },
  { client: "Mrs. Pirakash", service: "Elder Care", location: "Jaffna", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 20,000" },
  { client: "Mrs. Birana", service: "Elder Care", location: "Colombo", date: "2024-01-20 9:00 AM - 1:00 PM", payment: "LKR 25,000" }
];

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
