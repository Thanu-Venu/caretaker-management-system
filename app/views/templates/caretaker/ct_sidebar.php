<?php
// Include badge helper
require_once APPROOT . '/core/SidebarBadgeHelper.php';
// Get badge counts once for this sidebar
$badgeCounts = getSidebarBadgeCounts();
$ctUser = $_SESSION['user'] ?? [];
$ctDisplay = $ctUser['name'] ?? $ctUser['username'] ?? 'Caretaker';
$ctParts = preg_split('/\s+/', trim((string) $ctDisplay));
if (count($ctParts) >= 2) {
    $ctLast = $ctParts[count($ctParts) - 1];
    $ctInitials = strtoupper(substr($ctParts[0], 0, 1) . substr($ctLast, 0, 1));
} else {
    $ctInitials = strtoupper(substr((string) $ctDisplay, 0, min(2, strlen((string) $ctDisplay))));
}
?>
  <button class="sidebar-toggle" type="button" aria-label="Toggle sidebar menu">
    <i class='bx bx-menu'></i>
  </button>

  <aside class="sidebar">
    <div class="menu-scroll">
      <ul class="sidebar-menu">

        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_dashboard"><i class='bx bxs-dashboard'></i> <span>Dashboard</span></a></li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule"><i class='bx bxs-calendar'></i> <span>My Schedule</span></a></li>
        <li>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bx-book-alt'></i> <span>Bookings</span>
              </span>
              <?php echo renderBadge('bookings', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li>
          <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave">
            <span class="menu-item-content">
              <span class="menu-left">
                <i class='bx bxs-calendar-check'></i> <span>Leave Request</span>
              </span>
              <?php echo renderBadge('leave_requests', $badgeCounts); ?>
            </span>
          </a>
        </li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_complaints"><i class='bx bxs-error'></i> <span>Complaints</span></a></li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_reviews"><i class='bx bxs-message-dots'></i> <span>Reviews</span></a></li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_reports"><i class='bx bxs-report'></i> <span>Reports</span></a></li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_announcement"><i class='bx bxs-megaphone'></i> <span>Announcements</span></a></li>
        <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_settings"><i class='bx bxs-cog'></i> <span>Settings</span></a></li>
      </ul>
    </div>

    <a href="<?= URLROOT ?>/public?url=caretaker/ct_settings" class="sidebar-rail-footer" title="<?= htmlspecialchars($ctDisplay) ?> — Settings" aria-label="<?= htmlspecialchars($ctDisplay) ?>, open account settings">
      <span class="sidebar-rail-avatar" aria-hidden="true"><?= htmlspecialchars($ctInitials) ?></span>
    </a>
  </aside>

  <div class="sidebar-overlay"></div>
  <script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get the current "url" parameter from the URL
      const params = new URLSearchParams(window.location.search);
      let currentPage = params.get('url');

      if (!currentPage) {
        const path = window.location.pathname;
        const match = path.match(/\/(caretaker\/[a-z_]+)/i);
        if (match) {
          currentPage = match[1];
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
        const linkURL = new URL(link.href, window.location.origin);
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