<?php
// Include badge helper
require_once APPROOT . '/core/SidebarBadgeHelper.php';
// Get badge counts once for this sidebar
$badgeCounts = getSidebarBadgeCounts();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sidebar</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <!-- Mobile Toggle Button (auto-created by JS if missing) -->
  <button class="sidebar-toggle">
    <i class='bx bx-menu'></i>
  </button>

  <!-- Sidebar -->
  <aside class="sidebar">


    <div class="menu-scroll">
      <ul class="sidebar-menu">
        <li><a href="http://localhost/CMA/public?url=admin/ad_dashboard"><i class='bx bxs-dashboard'></i> <span>Dashboard</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_caretakers"><i class='bx bx-user-circle'></i> <span>Caregivers</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_clients"><i class='bx bx-user'></i> <span>Clients</span></a></li>
        <li>
          <a href="http://localhost/CMA/public?url=admin/ad_bookings">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-calendar'></i> <span>Bookings</span>
              </span>
              <?php echo renderBadge('bookings', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li>
          <a href="http://localhost/CMA/public?url=admin/ad_leave">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-time'></i> <span>Leave</span>
              </span>
              <?php echo renderBadge('leave_requests', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li>
          <a href="http://localhost/CMA/public?url=admin/ad_profile_requests">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-user-check'></i> <span>Profile Requests</span>
              </span>
              <?php echo renderBadge('profile_requests', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li>
          <a href="http://localhost/CMA/public?url=admin/ad_payments">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-dollar-circle'></i> <span>Payments</span>
              </span>
              <?php echo renderBadge('payments', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_feedback"><i class='bx bx-message-detail'></i> <span>Feedback</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_users"><i class='bx bx-group'></i> <span>Staff</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_announcement"><i class='bx bxs-megaphone'></i> <span>Announcements</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_history"><i class='bx bx-history'></i> <span>Logs</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_reports"><i class='bx bx-bar-chart'></i> <span>Reports</span></a></li>
        <li><a href="http://localhost/CMA/public?url=admin/ad_settings"><i class='bx bx-cog'></i> <span>Settings</span></a></li>

      </ul>
    </div>
  </aside>

  <!-- Overlay (for mobile) -->
  <div class="sidebar-overlay"></div>

  <!-- Mobile Toggle Script -->
  <script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>

  <script>
    // Active page highlighting with URL mapping
    document.addEventListener('DOMContentLoaded', function() {
      // Get the current "url" parameter from the URL
      const params = new URLSearchParams(window.location.search);
      let currentPage = params.get('url'); // e.g., "admin/ad_dashboard"

      // Fallback: extract from pathname if url param not found
      if (!currentPage) {
        const path = window.location.pathname;

        // Map controller-based URLs to admin page names
        const urlMappings = {
          'CaretakerCRUD/list': 'admin/ad_caretakers',
          'UserCRUD': 'admin/ad_users',
          'ClientCRUD': 'admin/ad_clients',
          'admin/ad_': 'admin/'
        };

        // Check each mapping
        for (const [controller, adminPage] of Object.entries(urlMappings)) {
          if (path.includes(controller)) {
            currentPage = adminPage;
            break;
          }
        }

        // Fallback: extract admin/page_name pattern if no mapping matched
        if (!currentPage) {
          const match = path.match(/\/(admin\/[a-z_]+)/i);
          if (match) {
            currentPage = match[1]; // e.g., "admin/ad_caretakers"
          }
        }
      }

      // Remove any previously active links and list items
      document.querySelectorAll('.sidebar-menu a.active').forEach(link => {
        link.classList.remove('active');
      });
      document.querySelectorAll('.sidebar-menu li.active').forEach(li => {
        li.classList.remove('active');
      });

      // Loop through all sidebar links
      document.querySelectorAll('.sidebar-menu a').forEach(link => {
        // Only process links that have a "url" parameter
        const linkURL = new URL(link.href);
        const linkPage = linkURL.searchParams.get('url');

        // Compare only if linkPage exists
        if (linkPage && linkPage === currentPage) {
          // Add active class to the link itself
          link.classList.add('active');
          link.parentElement.classList.add('active'); // Also highlight <li> for nested styling

          // If the link is inside a submenu, open the parent submenu
          const submenu = link.closest('.submenu');
          if (submenu) submenu.classList.add('active');
        }
      });
    });
  </script>

</body>

</html>