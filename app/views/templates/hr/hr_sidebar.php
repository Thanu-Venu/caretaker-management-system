
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Sidebar</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
  
      <div class="logo">


      <h2>SmartCare</h2>

    <ul class="menu">
      <li><a href="http://localhost/CMA/public?url=hr/hr_dashboard"><i class='bx bx-home'></i><span>Dashboard</span></a></li>

      <li class="submenu">
        <a href="#"><i class='bx bx-group'></i><span>Caretakers</span><i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          <li><a href="http://localhost/CMA/public?url=hr/hr_addct">Add Caretaker</a></li>
          <li><a href="http://localhost/CMA/public?url=hr/hr_managect">Manage Caretakers</a></li>
        </ul>
      </li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_pending_request"><i class="fas fa-hourglass-half"></i><span>Pending Request</span></a></li>

      <li><a href="http://localhost/CMA/public?url=hr/hr_schedule"><i class='bx bx-calendar'></i><span>Schedule</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_leave"><i class='bx bx-time'></i><span>Leave</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_complaint"><i class='bx bx-error'></i><span>Complaints</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_feedback"><i class='bx bx-message'></i><span>Feedback</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_history"><i class='bx bx-history'></i><span>History</span></a></li>

      <li>
        <a href="http://localhost/CMA/public?url=hr/hr_reports"><i class='bx bx-bar-chart'></i><span>Reports</span></a>
      </li>

      <li><a href="http://localhost/CMA/public?url=hr/hr_settings"><i class='bx bx-cog'></i><span>Settings</span></a></li>
    </ul>

    <div class="logout">
      <a href="http://localhost/CMA/public"><i class='bx bx-log-out'></i><span>Logout</span></a>

    </div>
    <ul class="nav-links">
      
      <li><a href="#"><i class="fa fa-home"></i><span>Dashboard</span></a></li>
      <li class="active"><a href="#"><i class="fa fa-users"></i><span>Caretakers</span></a></li>
      <li><a href="#"><i class="fa fa-user-shield"></i><span>Priority</span></a></li>
      <li><a href="#"><i class="fa fa-bookmark"></i><span>Schedule</span></a></li>
      <li><a href="#"><i class="fa fa-calendar-minus"></i><span>Leave</span></a></li>
      <li><a href="#"><i class="fa fa-book-open"></i><span>Complaints</span></a></li>
      <li><a href="#"><i class="fa fa-comment-dots"></i><span>Feedback</span></a></li>
      <li><a href="#"><i class="fa fa-history"></i><span>History</span></a></li>
      <li><a href="#"><i class="fa fa-chart-bar"></i><span>Reports</span></a></li>
      <li><a href="#"><i class="fa fa-cog"></i><span>Settings</span></a></li>
    </ul>
  </div>




  <script src="script.js"></script>

  <script>
    // Toggle submenu
    document.querySelectorAll(".submenu > a").forEach(menu => {
      menu.addEventListener("click", function(e) {
        e.preventDefault();
        let parent = this.parentElement;
        parent.classList.toggle("open");
      });
    });

    // Highlight active menu item
    const currentPath = window.location.href;

  document.querySelectorAll(".menu a").forEach(link => {
    // Check if the link's href is included in the current URL
    if (currentPath.includes(link.getAttribute("href"))) {
      link.classList.add("active");

      // If it's inside a submenu, also open the submenu
      const parent = link.closest(".submenu");
      if (parent) {
        parent.classList.add("open");
      }
    }
  });
      
  
  

    
   // Highlight active sidebar item
const currentUrl = window.location.href.split("?")[1]; // just compare after ?
document.querySelectorAll(".sidebar .menu li a").forEach(link => {
  const href = link.getAttribute("href");
  
  if (href && href.includes(currentUrl)) {
    link.classList.add("active");

    // If it's inside a dropdown, also expand it
    const dropdown = link.closest(".dropdown-container");
    if (dropdown) {
      dropdown.classList.add("show");
      dropdown.previousElementSibling.querySelector(".arrow").classList.add("rotate");
    }
  }
});

  </script>

</body>
</html>
