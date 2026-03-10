/**
 * ================================================
 * SMARTCARE - SIDEBAR MOBILE TOGGLE
 * JavaScript for responsive sidebar functionality
 * ================================================
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        initSidebar();
        initDropdowns();
    });

    /**
     * Initialize sidebar toggle functionality
     */
    function initSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        // Exit if sidebar doesn't exist
        if (!sidebar) {
            console.warn('Sidebar element not found');
            return;
        }

        // Create toggle button if it doesn't exist
        if (!sidebarToggle) {
            const toggle = document.createElement('button');
            toggle.className = 'sidebar-toggle';
            toggle.innerHTML = '<i class="bx bx-menu"></i>';
            toggle.setAttribute('aria-label', 'Toggle sidebar menu');
            document.body.appendChild(toggle);
        }

        // Create overlay if it doesn't exist
        if (!sidebarOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        // Get elements (they now definitely exist)
        const toggle = document.querySelector('.sidebar-toggle');
        const overlay = document.querySelector('.sidebar-overlay');

        // Toggle sidebar open/close
        function toggleSidebar() {
            const isOpen = sidebar.classList.contains('open');

            if (isOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // Open sidebar
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            body.classList.add('sidebar-open');
            toggle.innerHTML = '<i class="bx bx-x"></i>'; // Change to X icon
            toggle.setAttribute('aria-label', 'Close sidebar menu');
        }

        // Close sidebar
        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            body.classList.remove('sidebar-open');
            toggle.innerHTML = '<i class="bx bx-menu"></i>'; // Change back to menu icon
            toggle.setAttribute('aria-label', 'Toggle sidebar menu');
        }

        // Event listeners
        if (toggle) {
            toggle.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking a link (on mobile)
        const sidebarLinks = sidebar.querySelectorAll('a:not(.dropdown-btn)');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function () {
                // Only close on mobile (when toggle is visible)
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                // Close sidebar when resizing to desktop view
                if (window.innerWidth >= 768 && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            }, 250);
        });
    }

    /**
     * Initialize dropdown menus in sidebar
     */
    function initDropdowns() {
        const dropdownButtons = document.querySelectorAll('.dropdown-btn');

        dropdownButtons.forEach(button => {
            // Get the dropdown container (next sibling)
            const dropdownContainer = button.nextElementSibling;

            if (!dropdownContainer || !dropdownContainer.classList.contains('dropdown-container')) {
                return;
            }

            // Check if dropdown should be open (if any child link is active)
            const activeLink = dropdownContainer.querySelector('a.active');
            if (activeLink) {
                dropdownContainer.classList.add('open');
                button.classList.add('active');
            }

            // Toggle dropdown on click
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const isOpen = dropdownContainer.classList.contains('open');

                // Close all other dropdowns (optional - comment out for accordion behavior)
                // closeAllDropdowns();

                // Toggle current dropdown
                if (isOpen) {
                    dropdownContainer.classList.remove('open');
                    button.classList.remove('active');
                } else {
                    dropdownContainer.classList.add('open');
                    button.classList.add('active');
                }
            });
        });

        /**
         * Close all dropdowns in sidebar
         */
        function closeAllDropdowns() {
            const allDropdowns = document.querySelectorAll('.dropdown-container');
            const allButtons = document.querySelectorAll('.dropdown-btn');

            allDropdowns.forEach(dropdown => {
                dropdown.classList.remove('open');
            });

            allButtons.forEach(btn => {
                btn.classList.remove('active');
            });
        }
    }

    /**
     * Set active menu item based on current URL
     */
    function setActiveMenuItem() {
        const currentPath = window.location.pathname;
        const menuLinks = document.querySelectorAll('.sidebar a');

        menuLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            // Remove active class from all links first
            link.classList.remove('active');

            // Add active class if paths match
            if (currentPath === linkPath) {
                link.classList.add('active');

                // If link is inside a dropdown, open the dropdown
                const dropdownContainer = link.closest('.dropdown-container');
                if (dropdownContainer) {
                    dropdownContainer.classList.add('open');
                    const dropdownBtn = dropdownContainer.previousElementSibling;
                    if (dropdownBtn && dropdownBtn.classList.contains('dropdown-btn')) {
                        dropdownBtn.classList.add('active');
                    }
                }
            }
        });
    }

    // Set active menu item on page load
    setActiveMenuItem();

    // Export functions for global access if needed
    window.SmartCare = window.SmartCare || {};
    window.SmartCare.Sidebar = {
        init: initSidebar,
        initDropdowns: initDropdowns,
        setActiveMenuItem: setActiveMenuItem
    };

})();
