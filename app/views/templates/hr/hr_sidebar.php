<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SmartCare — HR Sidebar</title>

  <!-- Boxicons (used in markup) -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
  <!-- Your HR sidebar CSS -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_sidebar.css">
  
</head>
<body>
  <div class="sidebar">
  
     
   
    <ul class="menu">
      <li><a href="http://localhost/CMA/public?url=hr/hr_dashboard"><i class='bx bx-home'></i> <span>Dashboard</span></a></li>

      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class='bx bx-group'></i> <span>Caregivers</span> <i class='bx bx-chevron-down arrow'></i></a>
        <ul class="dropdown-container">
          <li><a href="http://localhost/CMA/public?url=hr/hr_addct">Add Caregivers</a></li>
          <li><a href="http://localhost/CMA/public?url=hr/hr_managect">Manage Caregivers</a></li>
        </ul>
      </li>

      <li><a href="http://localhost/CMA/public?url=hr/hr_pending_request"><i class="fas fa-hourglass-half"></i> <span>Pending Request</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_schedule"><i class='bx bx-calendar'></i> <span>Schedule</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_leave"><i class='bx bx-time'></i> <span>Leave</span></a></li>
      <li><a href="http://localhost/CMA/public/index.php?url=Complaint/index"><i class='bx bx-error'></i> <span>Complaints</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_feedback"><i class='bx bx-message'></i> <span>Feedback</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_history"><i class='bx bx-history'></i> <span>History</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_reports"><i class='bx bx-bar-chart'></i> <span>Reports</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_announcement"><i class='bx bxs-megaphone'></i> <span>Announcement</span></a></li>
      <li><a href="http://localhost/CMA/public?url=hr/hr_settings"><i class='bx bx-cog'></i> <span>Settings</span></a></li>

      <li class="logout"><a href="<?= URLROOT ?>/index.php?url=auth/logout"><i class='bx bx-log-out'></i> <span>Logout</span></a></li>
    </ul>
  </div>


 
 <script>
  // Dropdown toggle (works for items with class .dropdown-btn)
  document.querySelectorAll(".dropdown-btn").forEach(button => {
    button.addEventListener("click", function(e) {
      e.preventDefault();
      const dropdown = this.nextElementSibling; // .dropdown-container
      if (dropdown) dropdown.classList.toggle("show");

      // rotate arrow on the clicked button
      const arrow = this.querySelector(".arrow");
      if (arrow) arrow.classList.toggle("rotate");
    });
  });

  // Highlight active link based on "url" query param (e.g. ?url=hr/hr_dashboard)
  (function highlightActive() {
    const params = new URLSearchParams(window.location.search);
    const current = params.get("url") || "";

    // All anchors inside .menu
   const menuLinks = document.querySelectorAll('.menu a[href*="url="]');


    menuLinks.forEach(link => {
      try {
        const linkUrl = new URL(link.href).searchParams.get("url") || "";
        if (!linkUrl) return; // skip links like href="#" (acts like continue)
        if (linkUrl === current && current !== "") {
          // add active class, but DO NOT auto-open the parent dropdown
          link.classList.add("active");

          // --- removed auto-open code so dropdown stays collapsed on load ---
          // If you still want the parent arrow to appear rotated without opening,
          // you could add a different visual indicator here, but avoid opening.
        }
      } catch (err) {
        // ignore malformed hrefs
      }
    });
  })();
</script>








</body>
</html>