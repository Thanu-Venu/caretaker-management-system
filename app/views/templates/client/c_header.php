<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="main-header">
  <!-- Sidebar toggle -->
  <div class="left-section">
  

   <!--Logo + Company Name -->
  <div class="logo-section">
    <img src="<?php echo URLROOT; ?>/public/images/logo.jpg" alt="SmartCare Logo" class="logo">
    <span class="company-name">SmartCare</span>
  </div> 
</div>
  <!-- Search bar -->
  <div class="right-section">
  <div class="search-bar">
    <input type="text" placeholder="Search...">
    <i class="fas fa-search"></i>
  </div>

  <!-- Right icons -->
  <div class="header-icons">
    <div class="notification-wrapper">
  <button id="notifBtn" class="notif-btn">
    <i class="fas fa-bell"></i>
    <span class="notif-count" id="notifCount">3</span>
  </button>

  <!-- Dropdown Menu -->
  <div id="notifDropdown" class="notif-dropdown">
    <ul id="notifList">
      <!-- Notifications injected by JS -->
    </ul>
    <div class="see-all">
      <a href="<?php echo URLROOT; ?>/admin/ad_notification">See All</a>
    </div>
  </div>
</div>

    <i class="fas fa-user-circle"></i>
  </div>
</div>
</header>
  <script src="<?php echo URLROOT; ?>/public/js/notification.js"></script>
</body>
