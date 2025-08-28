<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Dashboard Sidebar</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_sidebar.css">
  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    
    <ul class="menu">
      <li><a href="#"><i class="bx bxs-dashboard"></i> Dashboard</a></li>

      <li><a href="#"><i class="bx bx-search"></i> Find Caretakers</a></li>

      <!-- My Bookings Dropdown -->
      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class="bx bx-calendar"></i> My Bookings <i class="bx bx-chevron-down arrow"></i></a>
        <ul class="dropdown-container">
          <li><a href="#">Upcoming Bookings</a></li><br>
          <li><a href="#">Past Bookings</a></li><br>
          <li><a href="#">Cancelled Bookings</a></li><br>
        </ul>
      </li>

      <!-- Payment Dropdown -->
      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class="bx bx-dollar-circle"></i> Payment History <i class="bx bx-chevron-down arrow"></i></a>
        <ul class="dropdown-container">
          <li><a href="#">Invoices</a></li><br>
          <li><a href="#">Pending Payments</a></li><br>
          <li><a href="#">Completed Payments</a></li><br>
        </ul>
      </li>

      <li><a href="#"><i class="bx bx-message-dots"></i> Feedback</a></li>

      <!-- Settings Dropdown -->
      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class="bx bx-cog"></i> Settings <i class="bx bx-chevron-down arrow"></i></a>
        <ul class="dropdown-container">
          <li><a href="#">Edit Profile</a></li><br>
          <li><a href="#">Change Password</a></li><br>
          <li><a href="#">Manage Addresses</a></li><br>
        </ul>
      </li>

      <li class="logout"><a href="#"><i class="bx bx-log-out"></i> Logout</a></li>
    </ul>
  </div>

  <!-- JS for dropdown -->
  <script>
    document.querySelectorAll(".dropdown-btn").forEach(button => {
      button.addEventListener("click", e => {
        e.preventDefault();
        let dropdown = button.nextElementSibling;
        dropdown.classList.toggle("show");

        // Rotate arrow
        button.querySelector(".arrow").classList.toggle("rotate");
      });
    });
  </script>
</body>
</html>
