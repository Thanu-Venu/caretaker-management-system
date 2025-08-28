<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR Manager Sidebar</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_sidebar.css">
</head>
<body>
  <div class="sidebar">
   <br><br><br>

    <ul class="menu">
      <li><a href="#"><i class='bx bx-home'></i><span>Dashboard</span></a></li>

      <li class="submenu">
        <a href="#"><i class='bx bx-group'></i><span>Caretakers</span><i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          <li><a href="#">Add Caretaker</a></li>
          <li><a href="#">Manage Caretakers</a></li>
        </ul>
      </li>

      <li><a href="#"><i class='bx bx-calendar'></i><span>Schedule</span></a></li>

      <li class="submenu">
        <a href="#"><i class='bx bx-time'></i><span>Leave</span><i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          <li><a href="#">Request Leave</a></li>
          <li><a href="#">Leave History</a></li>
        </ul>
      </li>

      <li><a href="#"><i class='bx bx-error'></i><span>Complaints</span></a></li>
      <li><a href="#"><i class='bx bx-message'></i><span>Feedback</span></a></li>
      <li><a href="#"><i class='bx bx-history'></i><span>History</span></a></li>

      <li class="submenu">
        <a href="#"><i class='bx bx-bar-chart'></i><span>Reports</span><i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          <li><a href="#">Monthly Reports</a></li>
          <li><a href="#">Yearly Reports</a></li>
        </ul>
      </li>

      <li><a href="#"><i class='bx bx-cog'></i><span>Settings</span></a></li>
    </ul>

    <div class="logout">
      <a href="#"><i class='bx bx-log-out'></i><span>Logout</span></a>
    </div>
  </div>

  <script>
    // Toggle submenu
    document.querySelectorAll(".submenu > a").forEach(menu => {
      menu.addEventListener("click", function(e) {
        e.preventDefault();
        let parent = this.parentElement;
        parent.classList.toggle("open");
      });
    });
  </script>
</body>
</html>
