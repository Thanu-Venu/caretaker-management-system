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
      <li><a href="http://localhost/CMA/public?url=admin/ad_dashboard"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_caretakers"><i class='bx bx-user-circle'></i> Caregivers</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_clients"><i class='bx bx-user'></i> Clients</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_bookings"><i class='bx bx-calendar'></i> Bookings</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_leave"><i class='bx bx-time'></i> Leave</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_payments"><i class='bx bx-dollar-circle'></i> Payments</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_feedback"><i class='bx bx-message-detail'></i> Feedback</a></li>
      
      
      <li><a href="http://localhost/CMA/public?url=admin/ad_users"><i class='bx bx-group'></i> Staff</a></li>

      <li><a href="http://localhost/CMA/public?url=admin/ad_announcement"><i class='bx bxs-megaphone'></i> Announcements</a></li>
      <li><a href="http://localhost/CMA/public?url=admin/ad_history"><i class='bx bx-history'></i> History</a></li>
      
      
      <li><a href="http://localhost/CMA/public?url=admin/ad_reports"><i class='bx bx-bar-chart'></i> Reports</a></li>

      <li><a href="http://localhost/CMA/public?url=admin/ad_settings"><i class='bx bx-cog'></i> Settings</a></li>
      <li class="logout"><a href="<?= URLROOT ?>/index.php?url=auth/logout"><i class="bx bx-log-out"></i> Logout</a></li>

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


  // Get the current "url" parameter from the URL
  const params = new URLSearchParams(window.location.search);
  const currentPage = params.get('url'); // e.g., "admin/ad_dashboard"

  // Loop through all sidebar links
  document.querySelectorAll('.sidebar-menu a').forEach(link => {
    // Only process links that have a "url" parameter
    const linkURL = new URL(link.href);
    const linkPage = linkURL.searchParams.get('url');

    // Compare only if linkPage exists
    if (linkPage && linkPage === currentPage) {
      link.parentElement.classList.add('active'); // highlight <li>

      // If the link is inside a submenu, open the parent submenu
      const submenu = link.closest('.submenu');
      if(submenu) submenu.classList.add('active');
    }
  });
</script>

</body>
</html>
