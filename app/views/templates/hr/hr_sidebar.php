<?php
require_once APPROOT . '/core/SidebarBadgeHelper.php';
$badgeCounts = getSidebarBadgeCounts();
$sbUser    = $_SESSION['user'] ?? [];
$sbDisplay = $sbUser['name'] ?? $sbUser['username'] ?? 'HR';
$sbParts   = preg_split('/\s+/', trim((string) $sbDisplay));
if (count($sbParts) >= 2) {
    $sbLast     = $sbParts[count($sbParts) - 1];
    $sbInitials = strtoupper(substr($sbParts[0], 0, 1) . substr($sbLast, 0, 1));
} else {
    $sbInitials = strtoupper(substr((string) $sbDisplay, 0, min(2, strlen((string) $sbDisplay))));
}
?>
<button class="sidebar-toggle" type="button" aria-label="Toggle sidebar menu">
    <i class="bx bx-menu"></i>
</button>

<aside class="sidebar">
    <div class="menu-scroll">
        <ul class="sidebar-menu">
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_dashboard"><i class="bx bxs-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="<?= URLROOT ?>/HRCaretakerCRUD/list"><i class="bx bx-group"></i> <span>Caregivers</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_schedule"><i class="bx bx-calendar"></i> <span>Schedule</span></a></li>

            <li>
                <a href="<?= URLROOT ?>/public?url=hr/hr_pending_request">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-hourglass"></i> <span>Pending Request</span>
                        </span>
                        <?php echo renderBadge('bookings', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/public?url=hr/pendingPayments">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-money"></i> <span>Pending Payments</span>
                        </span>
                        <?php echo renderBadge('payments', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li><a href="<?= URLROOT ?>/public?url=hr/paymentMonitor"><i class="bx bx-line-chart"></i> <span>Payment Monitor</span></a></li>
            <li><a href="<?= URLROOT ?>/hr/refunds"><i class="bx bx-receipt"></i> <span>Refunds</span></a></li>
            <li>
                <a href="<?= URLROOT ?>/public?url=hr/changeRequests">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-user-check"></i> <span>Change Requests</span>
                        </span>
                        <?php echo renderBadge('change_requests', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/public?url=hr/rescheduleRequests">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-calendar-edit"></i> <span>Reschedule Requests</span>
                        </span>
                        <?php echo renderBadge('reschedule_requests', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/public?url=HrLeave/index">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-time"></i> <span>Leave</span>
                        </span>
                        <?php echo renderBadge('leave_requests', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/public/index.php?url=Complaint/index">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-error"></i> <span>Complaints</span>
                        </span>
                        <?php echo renderBadge('complaints', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_feedback"><i class="bx bx-message-detail"></i> <span>Feedback</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_logs"><i class="bx bx-history"></i> <span>Logs</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_reports"><i class="bx bx-bar-chart"></i> <span>Reports</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_announcement"><i class="bx bxs-megaphone"></i> <span>Announcements</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=hr/hr_settings"><i class="bx bx-cog"></i> <span>Settings</span></a></li>
        </ul>
    </div>

    <a href="<?= URLROOT ?>/public?url=hr/hr_settings"
       class="sidebar-rail-footer"
       title="<?= htmlspecialchars($sbDisplay, ENT_QUOTES, 'UTF-8') ?> — Settings"
       aria-label="<?= htmlspecialchars($sbDisplay, ENT_QUOTES, 'UTF-8') ?>, open account settings">
        <span class="sidebar-rail-avatar" aria-hidden="true"><?= htmlspecialchars($sbInitials, ENT_QUOTES, 'UTF-8') ?></span>
    </a>
</aside>

<div class="sidebar-overlay"></div>

<script defer src="<?= URLROOT ?>/public/js/admin/admin-table-details.js"></script>
<script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    let currentPage = params.get('url');
    const path = window.location.pathname.toLowerCase();

    if (!currentPage) {
        if (path.includes('hrcaretakercrud')) {
            currentPage = '__hrcaretaker__';
        } else if (path.includes('hrleave') || path.includes('hr_leave')) {
            currentPage = 'HrLeave/index';
        } else if (path.includes('complaint')) {
            currentPage = 'Complaint/index';
        } else if (path.includes('/hr/refunds')) {
            currentPage = '__hr_refunds__';
        }
    }

    document.querySelectorAll('.sidebar-menu a.active').forEach(function (link) {
        link.classList.remove('active');
    });
    document.querySelectorAll('.sidebar-menu li.active').forEach(function (li) {
        li.classList.remove('active');
    });

    document.querySelectorAll('.sidebar-menu a').forEach(function (link) {
        let match = false;
        const href = (link.getAttribute('href') || '').toLowerCase();
        try {
            const u = new URL(link.href, window.location.origin);
            const linkPage = u.searchParams.get('url');
            if (linkPage && currentPage && linkPage === currentPage) {
                match = true;
            }
        } catch (e) { /* ignore */ }

        if (!match && currentPage === 'HrLeave/index' && href.indexOf('hrleave') !== -1) {
            match = true;
        }
        if (!match && currentPage === 'Complaint/index' && href.indexOf('complaint') !== -1) {
            match = true;
        }
        if (!match && currentPage === '__hrcaretaker__' && href.indexOf('hrcaretakercrud') !== -1) {
            match = true;
        }
        if (!match && currentPage === '__hr_refunds__' && href.indexOf('/hr/refunds') !== -1) {
            match = true;
        }

        if (match) {
            link.classList.add('active');
            if (link.parentElement) {
                link.parentElement.classList.add('active');
            }
        }
    });
});
</script>
