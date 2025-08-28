<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Sidebar</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_sidebar.css">
</head>
<body>
  <div class="sidebar">
    <br><br><br>
    <ul class="nav-links">

      <li>
        <a href="#">
          <i class='bx bxs-dashboard'></i>
          <span class="link_name">Dashboard</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-calendar'></i>
          <span class="link_name">My Schedule</span>
          <i class='bx bx-chevron-down arrow'></i>
        </a>
        <ul class="sub-menu">
          <li><a href="#">Today’s Schedule</a></li>
          <li><a href="#">Upcoming</a></li>
        </ul>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-user-pin'></i>
          <span class="link_name">Clients Assigned</span>
          <i class='bx bx-chevron-down arrow'></i>
        </a>
        <ul class="sub-menu">
          <li><a href="#">View Clients</a></li>
          <li><a href="#">Service Details</a></li>
        </ul>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-calendar-check'></i>
          <span class="link_name">Leave Request</span>
          <i class='bx bx-chevron-down arrow'></i>
        </a>
        <ul class="sub-menu">
          <li><a href="#">Request Leave</a></li>
          <li><a href="#">Leave History</a></li>
        </ul>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-error'></i>
          <span class="link_name">Complaints</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-message-dots'></i>
          <span class="link_name">Feedback</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-report'></i>
          <span class="link_name">Reports</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-user-circle'></i>
          <span class="link_name">Profile</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class='bx bxs-cog'></i>
          <span class="link_name">Settings</span>
        </a>
      </li>

      <li class="logout">
        <a href="#">
          <i class='bx bx-log-out'></i>
          <span class="link_name">Logout</span>
        </a>
      </li>

    </ul>
  </div>

  <script>
   // Select all links that have a submenu (contain .arrow)
let dropdownLinks = document.querySelectorAll(".nav-links li > a .arrow");

dropdownLinks.forEach(arrow => {
  let parentLink = arrow.parentElement; // the <a> containing arrow and text
  let parentLi = parentLink.parentElement; // the <li>

  parentLink.addEventListener("click", e => {
    e.preventDefault(); // prevent default link behavior
    parentLi.classList.toggle("showMenu");
  });
});

  </script>
</body>
</html>
