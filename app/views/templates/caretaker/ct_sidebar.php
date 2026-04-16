<?php
// Include badge helper
require_once APPROOT . '/core/SidebarBadgeHelper.php';
// Get badge counts once for this sidebar
$badgeCounts = getSidebarBadgeCounts();
$ctUser = $_SESSION['user'] ?? [];
$ctDisplay = trim((string) ($ctUser['name'] ?? ($ctUser['username'] ?? 'Caretaker')));
if ($ctDisplay === '') {
    $ctDisplay = 'Caretaker';
}
$ctParts = preg_split('/\s+/', trim((string) $ctDisplay));
if (count($ctParts) >= 2) {
    $ctLast = $ctParts[count($ctParts) - 1];
    $ctInitials = strtoupper(substr($ctParts[0], 0, 1) . substr($ctLast, 0, 1));
} else {
    $ctInitials = strtoupper(substr((string) $ctDisplay, 0, min(2, strlen((string) $ctDisplay))));
}
?>
<button class="sidebar-toggle" type="button" aria-label="Toggle sidebar menu">
    <i class="bx bx-menu"></i>
</button>

<aside class="sidebar">
    <div class="menu-scroll">
        <ul class="sidebar-menu">
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_dashboard"><i class="bx bxs-dashboard"></i> <span>Dashboard</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_schedule"><i class="bx bx-calendar"></i> <span>My Schedule</span></a></li>
            <li>
                <a href="<?= URLROOT ?>/public?url=caretaker/ct_booking">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-book-alt"></i> <span>Bookings</span>
                        </span>
                        <?php echo renderBadge('bookings', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li>
                <a href="<?= URLROOT ?>/public?url=caretaker/ct_leave">
                    <span class="menu-item-content">
                        <span class="menu-left">
                            <i class="bx bx-calendar-x"></i> <span>Leave Request</span>
                        </span>
                        <?php echo renderBadge('leave_requests', $badgeCounts); ?>
                    </span>
                </a>
            </li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_complaints"><i class="fa-solid fa-file-circle-exclamation"></i> <span>Complaints</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_reviews"><i class="bx bxs-star"></i> <span>Reviews</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_reports"><i class="bx bx-line-chart"></i> <span>Reports</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_announcement"><i class="bx bxs-megaphone"></i> <span>Announcements</span></a></li>
            <li><a href="<?= URLROOT ?>/public?url=caretaker/ct_settings"><i class="bx bx-cog"></i> <span>Profile settings</span></a></li>
        </ul>
    </div>

    <a href="<?= URLROOT ?>/public?url=caretaker/ct_settings"
       class="sidebar-rail-footer"
       title="<?= htmlspecialchars($ctDisplay, ENT_QUOTES, 'UTF-8') ?> — Settings"
       aria-label="<?= htmlspecialchars($ctDisplay, ENT_QUOTES, 'UTF-8') ?>, open profile settings">
        <span class="sidebar-rail-avatar" aria-hidden="true"><?= htmlspecialchars($ctInitials, ENT_QUOTES, 'UTF-8') ?></span>
    </a>
</aside>

<div class="sidebar-overlay"></div>

<script defer src="<?= URLROOT ?>/public/js/admin/admin-table-details.js"></script>
<script src="<?= URLROOT ?>/public/js/sidebar-toggle.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function isCollapsedRail() {
        return document.body.classList.contains('admin-sidebar-collapsed') &&
            window.matchMedia('(min-width: 1024px)').matches;
    }

    function syncSidebarFlyoutChrome() {
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        var open = isCollapsedRail() && !!sidebar.querySelector('.submenu.active');
        sidebar.classList.toggle('sidebar--submenu-flyout', open);
    }

    function positionBookingsFlyout(submenu) {
        var list = submenu.querySelector('.submenu-list');
        var btn = submenu.querySelector('.submenu-parent');
        var sidebar = submenu.closest('.sidebar');
        if (!list || !btn) return;
        if (isCollapsedRail() && submenu.classList.contains('active') && sidebar) {
            var r = btn.getBoundingClientRect();
            var sr = sidebar.getBoundingClientRect();
            list.style.position = 'absolute';
            list.style.left = (r.right - sr.left + 8) + 'px';
            list.style.top = (Math.max(8, r.top - 4) - sr.top) + 'px';
            list.style.right = 'auto';
            list.style.bottom = 'auto';
            list.style.zIndex = '5';
            list.classList.add('submenu-list--flyout');
        } else {
            list.style.position = '';
            list.style.left = '';
            list.style.top = '';
            list.style.right = '';
            list.style.bottom = '';
            list.style.zIndex = '';
            list.classList.remove('submenu-list--flyout');
        }
        syncSidebarFlyoutChrome();
    }

    function closeOtherFlyouts(current) {
        document.querySelectorAll('.sidebar .submenu.active').forEach(function (other) {
            if (other === current) return;
            other.classList.remove('active');
            var ob = other.querySelector('.submenu-parent');
            if (ob) ob.setAttribute('aria-expanded', 'false');
            positionBookingsFlyout(other);
        });
    }

    document.querySelectorAll('.sidebar .submenu-parent').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var li = btn.closest('.submenu');
            if (!li) return;
            closeOtherFlyouts(li);
            var open = li.classList.toggle('active');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            positionBookingsFlyout(li);
        });
    });

    // Capture click
    var sidebarRoot = document.querySelector('.sidebar');
    if (sidebarRoot) {
        sidebarRoot.addEventListener('click', function (e) {
            var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
            if (!a || a.classList.contains('submenu-parent')) return;
            if (!a.closest('.submenu-list')) return;
            var href = (a.getAttribute('href') || '').trim();
            if (href === '' || href === '#') return;
            if (!isCollapsedRail()) return;
            var sub = a.closest('.submenu');
            if (!sub) return;
            sub.classList.remove('active');
            var b = sub.querySelector('.submenu-parent');
            if (b) b.setAttribute('aria-expanded', 'false');
            positionBookingsFlyout(sub);
        }, true);
    }

    window.addEventListener('resize', function () {
        document.querySelectorAll('.sidebar .submenu.active').forEach(positionBookingsFlyout);
    });

    document.addEventListener('click', function (e) {
        if (!isCollapsedRail()) return;
        var t = e.target;
        if (t.closest && t.closest('.sidebar .submenu')) return;
        document.querySelectorAll('.sidebar .submenu.active').forEach(function (sub) {
            sub.classList.remove('active');
            var b = sub.querySelector('.submenu-parent');
            if (b) b.setAttribute('aria-expanded', 'false');
            positionBookingsFlyout(sub);
        });
    });

    const params = new URLSearchParams(window.location.search);
    let currentPage = params.get('url');
    if (!currentPage) {
        const path = window.location.pathname;
        const match = path.match(/\/(caretaker\/[a-z_]+)/i);
        if (match) {
            currentPage = match[1];
        }
    }

    document.querySelectorAll('.sidebar-menu a.active').forEach(function (link) {
        link.classList.remove('active');
    });
    document.querySelectorAll('.sidebar-menu li.active').forEach(function (li) {
        li.classList.remove('active');
    });

    document.querySelectorAll('.sidebar-menu a[href]').forEach(function (link) {
        if (link.classList.contains('submenu-parent')) return;
        let match = false;
        try {
            const u = new URL(link.href, window.location.origin);
            const linkPage = u.searchParams.get('url');
            if (linkPage && currentPage && linkPage === currentPage) {
                match = true;
            }
        } catch (err) { /* ignore */ }

        if (match) {
            link.classList.add('active');
            if (link.parentElement) {
                link.parentElement.classList.add('active');
            }
            const submenu = link.closest('.submenu');
            if (submenu) {
                submenu.classList.add('active');
                const parent = submenu.querySelector('.submenu-parent');
                if (parent) parent.setAttribute('aria-expanded', 'true');
                positionBookingsFlyout(submenu);
            }
        }
    });

    requestAnimationFrame(function () {
        document.querySelectorAll('.sidebar .submenu.active').forEach(positionBookingsFlyout);
    });

    window.addEventListener('smartcare-sidebar-layout', function () {
        document.querySelectorAll('.sidebar .submenu').forEach(positionBookingsFlyout);
    });
});
</script>