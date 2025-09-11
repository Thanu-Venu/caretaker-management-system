/* Sample admin notifications
const adminNotifications = [
  "New caretaker registered",
  "Monthly report ready",
  "Leave request pending approval"
];

const notifBtn = document.getElementById("notifBtn");
const notifDropdown = document.getElementById("notifDropdown");
const notifList = document.getElementById("notifList");
const notifCount = document.getElementById("notifCount");

// Populate notifications dynamically
function loadNotifications(userType) {
  notifList.innerHTML = "";
  let notifications = [];
  
  if(userType === "admin") {
    notifications = adminNotifications;
  }
  
  notifications.forEach(notif => {
    const li = document.createElement("li");
    li.textContent = notif;
    notifList.appendChild(li);
  });

  notifCount.textContent = notifications.length;
}

// Toggle dropdown
notifBtn.addEventListener("click", () => {
  notifDropdown.style.display = notifDropdown.style.display === "block" ? "none" : "block";
});

// Close dropdown if clicked outside
document.addEventListener("click", function(event) {
  if(!notifBtn.contains(event.target) && !notifDropdown.contains(event.target)){
    notifDropdown.style.display = "none";
  }
});

// Initialize for admin
loadNotifications("admin");*/
