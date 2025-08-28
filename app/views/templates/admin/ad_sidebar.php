<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sidebar</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_sidebar.css">
</head>
<body>

  <aside class="sidebar">
    <div class="sidebar-brand">
      <span class="brand-name">SmartCare</span>
    </div>

    <ul class="sidebar-menu">
      <li><a href="#"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
      <li><a href="#"><i class='bx bx-user-circle'></i> Caretakers</a></li>
      <li><a href="#"><i class='bx bx-user'></i> Clients</a></li>
      <li><a href="#"><i class='bx bx-calendar'></i> Bookings</a></li>
      <li><a href="#"><i class='bx bx-time'></i> Leave</a></li>
      <li><a href="#"><i class='bx bx-dollar-circle'></i> Payments</a></li>
      <li><a href="#"><i class='bx bx-message-detail'></i> Feedback</a></li>
      
      <!-- Submenu Example -->
      <li class="submenu">
        <a href="#"><i class='bx bx-group'></i> Users <i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          
          <li><a href="#">HR Managers</a></li>
          <li><a href="#">Caretakers</a></li>
          <li><a href="#">Clients</a></li>
        </ul>
      </li>

      <li><a href="#"><i class='bx bx-microphone'></i> Announcements</a></li>
      <li><a href="#"><i class='bx bx-history'></i> History</a></li>
      
      <!-- Submenu Example -->
      <li class="submenu">
        <a href="#"><i class='bx bx-bar-chart'></i> Reports <i class='bx bx-chevron-down arrow'></i></a>
        <ul class="submenu-list">
          <li><a href="#">Monthly</a></li>
          <li><a href="#">Yearly</a></li>
        </ul>
      </li>

      <li><a href="#"><i class='bx bx-cog'></i> Settings</a></li>
      <li class="logout"><a href="#"><i class="bx bx-log-out"></i> Logout</a></li>

    </ul>
  </aside>

  <script>
    // Toggle submenu
    document.querySelectorAll('.submenu > a').forEach(menu => {
      menu.addEventListener('click', e => {
        e.preventDefault();
        menu.parentElement.classList.toggle('active');
      });
    });
  </script>

</body>
</html>
