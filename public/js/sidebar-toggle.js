/**
 * ================================================
 * SMARTCARE - SIDEBAR TOGGLE (desktop + mobile)
 * Desktop (≥1024px): icon rail + localStorage preference
 * Mobile: slide-over drawer + overlay
 *
 * Collapsed state changes ONLY via .sidebar-toggle (hamburger).
 * Navigating via menu links never expands the rail.
 * ================================================
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'adminSidebarCollapsed';
    /** HR shell: body also gets .sidebarTucked (see hr-ui.css) */
    var COLLAPSED_CLASS_ADMIN = 'admin-sidebar-collapsed';
    var COLLAPSED_CLASS_HR = 'sidebarTucked';
    var INIT_FLAG = 'data-admin-sidebar-js-init';

    /**
     * Must match CSS @media (min-width: 1024px) for sidebar / main margins.
     */
    function isDesktop() {
        return window.matchMedia('(min-width: 1024px)').matches;
    }

    function readCollapsedPref() {
        try {
            return localStorage.getItem(STORAGE_KEY) === '1';
        } catch (e) {
            return false;
        }
    }

    function saveCollapsedPref(collapsed) {
        try {
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        } catch (e) { /* ignore */ }
    }

    /**
     * Apply body class from localStorage (desktop only). Does not toggle.
     */
    function setCollapsedClasses(body, collapsed) {
        if (!body) return;
        if (collapsed) {
            body.classList.add(COLLAPSED_CLASS_ADMIN, COLLAPSED_CLASS_HR);
        } else {
            body.classList.remove(COLLAPSED_CLASS_ADMIN, COLLAPSED_CLASS_HR);
        }
    }

    function applyCollapsedFromStorage(body) {
        if (!body) return;
        if (isDesktop()) {
            setCollapsedClasses(body, readCollapsedPref());
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
        initDropdowns();
    });

    function initSidebar() {
        var body = document.body;
        if (body.getAttribute(INIT_FLAG) === '1') {
            return;
        }
        body.setAttribute(INIT_FLAG, '1');

        var sidebar = document.querySelector('.sidebar');
        var sidebarToggle = document.querySelector('.sidebar-toggle');
        var sidebarOverlay = document.querySelector('.sidebar-overlay');

        if (!sidebar) {
            console.warn('Sidebar element not found');
            return;
        }

        if (!sidebarToggle) {
            var t = document.createElement('button');
            t.className = 'sidebar-toggle';
            t.type = 'button';
            t.innerHTML = '<i class="bx bx-menu"></i>';
            t.setAttribute('aria-label', 'Toggle sidebar menu');
            t.setAttribute('aria-expanded', 'true');
            document.body.appendChild(t);
        }

        if (!sidebarOverlay) {
            var o = document.createElement('div');
            o.className = 'sidebar-overlay';
            document.body.appendChild(o);
        }

        var toggle = document.querySelector('.sidebar-toggle');
        var overlay = document.querySelector('.sidebar-overlay');

        function hydrateSidebarLinkTitles() {
            sidebar.querySelectorAll('.sidebar-menu a[href]').forEach(function (a) {
                if (a.getAttribute('title')) return;
                var ml = a.querySelector('.menu-left > span');
                if (ml && !ml.classList.contains('sidebar-badge')) {
                    var label = ml.textContent.replace(/\s+/g, ' ').trim();
                    a.setAttribute('title', label);
                    if (!a.getAttribute('aria-label')) {
                        a.setAttribute('aria-label', label);
                    }
                    return;
                }
                var kids = a.children;
                for (var i = 0; i < kids.length; i++) {
                    var el = kids[i];
                    if (el.tagName === 'SPAN' && !el.classList.contains('menu-item-content')) {
                        var txt = el.textContent.replace(/\s+/g, ' ').trim();
                        if (txt) {
                            a.setAttribute('title', txt);
                            if (!a.getAttribute('aria-label')) {
                                a.setAttribute('aria-label', txt);
                            }
                            return;
                        }
                    }
                }
            });
        }

        hydrateSidebarLinkTitles();

        function syncToggleIcon() {
            if (!toggle) return;
            if (isDesktop()) {
                var collapsed = body.classList.contains(COLLAPSED_CLASS_ADMIN) || body.classList.contains(COLLAPSED_CLASS_HR);
                if (collapsed) {
                    toggle.innerHTML = '<i class="bx bx-menu"></i>';
                    toggle.setAttribute('aria-label', 'Expand sidebar');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    toggle.innerHTML = '<i class="bx bx-chevrons-left"></i>';
                    toggle.setAttribute('aria-label', 'Collapse sidebar');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            } else {
                if (sidebar.classList.contains('open')) {
                    toggle.innerHTML = '<i class="bx bx-x"></i>';
                    toggle.setAttribute('aria-label', 'Close sidebar menu');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    toggle.innerHTML = '<i class="bx bx-menu"></i>';
                    toggle.setAttribute('aria-label', 'Open sidebar menu');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }
        }

        function closeMobileDrawer() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
        }

        function openMobileDrawer() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            body.classList.add('sidebar-open');
        }

        var toggleCooldownUntil = 0;
        var TOGGLE_COOLDOWN_MS = 280;

        /** Only the hamburger / rail control may change desktop collapsed state. */
        function toggleSidebarFromControl() {
            var now = Date.now();
            if (now < toggleCooldownUntil) {
                return;
            }
            toggleCooldownUntil = now + TOGGLE_COOLDOWN_MS;

            if (isDesktop()) {
                closeMobileDrawer();
                var next = !(body.classList.contains(COLLAPSED_CLASS_ADMIN) || body.classList.contains(COLLAPSED_CLASS_HR));
                setCollapsedClasses(body, next);
                saveCollapsedPref(next);
                syncToggleIcon();
                window.dispatchEvent(new Event('smartcare-sidebar-layout'));
            } else {
                if (sidebar.classList.contains('open')) {
                    closeMobileDrawer();
                } else {
                    openMobileDrawer();
                }
                syncToggleIcon();
            }
        }

        function applyLayoutForViewport() {
            if (isDesktop()) {
                closeMobileDrawer();
                applyCollapsedFromStorage(body);
            } else {
                closeMobileDrawer();
            }
            syncToggleIcon();
            window.dispatchEvent(new Event('smartcare-sidebar-layout'));
        }

        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSidebarFromControl();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function () {
                if (!isDesktop()) {
                    closeMobileDrawer();
                    syncToggleIcon();
                }
            });
        }

        /**
         * Desktop: menu navigation must NOT change rail width.
         * Capture phase: re-apply stored preference before navigation / other handlers.
         */
        var navLinks = sidebar.querySelectorAll('a[href]:not(.dropdown-btn)');
        navLinks.forEach(function (link) {
            link.addEventListener(
                'click',
                function () {
                    if (!isDesktop()) {
                        closeMobileDrawer();
                        syncToggleIcon();
                        return;
                    }
                    applyCollapsedFromStorage(body);
                },
                true
            );
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeMobileDrawer();
                syncToggleIcon();
            }
        });

        applyLayoutForViewport();

        var mqDesktop = window.matchMedia('(min-width: 1024px)');
        function onDesktopBreakpointChange() {
            applyLayoutForViewport();
        }
        if (mqDesktop.addEventListener) {
            mqDesktop.addEventListener('change', onDesktopBreakpointChange);
        } else if (mqDesktop.addListener) {
            mqDesktop.addListener(onDesktopBreakpointChange);
        }

        window.addEventListener('pageshow', function (ev) {
            if (ev.persisted) {
                applyLayoutForViewport();
            }
        });

        window.addEventListener('storage', function (e) {
            if (e.key === STORAGE_KEY) {
                applyCollapsedFromStorage(body);
                syncToggleIcon();
                window.dispatchEvent(new Event('smartcare-sidebar-layout'));
            }
        });
    }

    function initDropdowns() {
        var dropdownButtons = document.querySelectorAll('.dropdown-btn');

        dropdownButtons.forEach(function (button) {
            var dropdownContainer = button.nextElementSibling;

            if (!dropdownContainer || !dropdownContainer.classList.contains('dropdown-container')) {
                return;
            }

            var activeLink = dropdownContainer.querySelector('a.active');
            if (activeLink) {
                dropdownContainer.classList.add('open');
                button.classList.add('active');
            }

            button.addEventListener('click', function (e) {
                e.preventDefault();

                var isOpen = dropdownContainer.classList.contains('open');

                if (isOpen) {
                    dropdownContainer.classList.remove('open');
                    button.classList.remove('active');
                } else {
                    dropdownContainer.classList.add('open');
                    button.classList.add('active');
                }
            });
        });
    }

    function setActiveMenuItem() {
        var currentPath = window.location.pathname;
        var menuLinks = document.querySelectorAll('.sidebar a');

        menuLinks.forEach(function (link) {
            var linkPath = new URL(link.href).pathname;

            link.classList.remove('active');

            if (currentPath === linkPath) {
                link.classList.add('active');

                var dropdownContainer = link.closest('.dropdown-container');
                if (dropdownContainer) {
                    dropdownContainer.classList.add('open');
                    var dropdownBtn = dropdownContainer.previousElementSibling;
                    if (dropdownBtn && dropdownBtn.classList.contains('dropdown-btn')) {
                        dropdownBtn.classList.add('active');
                    }
                }
            }
        });
    }

    setActiveMenuItem();

    window.SmartCare = window.SmartCare || {};
    window.SmartCare.Sidebar = {
        init: initSidebar,
        initDropdowns: initDropdowns,
        setActiveMenuItem: setActiveMenuItem,
        readCollapsedPref: readCollapsedPref,
        STORAGE_KEY: STORAGE_KEY
    };
})();
