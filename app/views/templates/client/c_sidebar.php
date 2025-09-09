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
      <li><a href="http://localhost/CMA/public?url=client/c_dashboard"><i class="bx bxs-dashboard"></i> Dashboard</a></li>

      <li><a href="http://localhost/CMA/public?url=client/c_find"><i class="bx bx-search"></i> Find Caretakers</a></li>

      <!-- My Bookings Dropdown -->
      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class="bx bx-calendar"></i> My Bookings <i class="bx bx-chevron-down arrow"></i></a>
        <ul class="dropdown-container">
          <li><a href="http://localhost/CMA/public?url=client/c_upcomingBookings">Upcoming Bookings</a></li><br>
          <li><a href="http://localhost/CMA/public?url=client/c_pastBookings">Past Bookings</a></li><br>
          <li><a href="http://localhost/CMA/public?url=client/c_cancelledBookings">Cancelled Bookings</a></li><br>
        </ul>
      </li>

      <!-- Payment Dropdown -->
      <li>
        <a href="http://localhost/CMA/public?url=client/c_paymentHistory"><i class="bx bx-dollar-circle"></i> Payment History </a>
      </li>

      <li>
        <a href="http://localhost/CMA/public?url=client/c_settings"><i class="bx bx-cog"></i> Settings </i></a>
      
      </li>

      <li class="logout"><a href="http://localhost/CMA/public"><i class="bx bx-log-out"></i> Logout</a></li>
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
