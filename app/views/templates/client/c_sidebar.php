<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Dashboard Sidebar</title>
  <link rel="stylesheet" href="<?php echo URLROOT;?>/public/css/client/c_sidebar.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="sidebar">

    <ul class="menu">
      <li><a href="http://localhost/CMA/public?url=client/c_dashboard"><i class="bx bxs-dashboard"></i> Dashboard</a>
      </li>

      <li><a href="http://localhost/CMA/public?url=client/c_find1"><i class="bx bx-search"></i> Find Caregivers</a></li>

      <!-- My Bookings Dropdown -->
      <li class="submenu">
        <a href="#" class="dropdown-btn"><i class="bx bx-calendar"></i> My Bookings <i
            class="bx bx-chevron-down arrow"></i></a>
        <ul class="dropdown-container">
          <li><a href="http://localhost/CMA/public?url=client/c_upcomingBookings">Upcoming Bookings</a></li>
          <li><a href="http://localhost/CMA/public?url=client/c_ongoingBookings">Ongoing Bookings</a></li>
          <li><a href="http://localhost/CMA/public?url=client/c_pastBookings">Past Bookings</a></li>
          <li><a href="http://localhost/CMA/public?url=client/c_cancelledBookings">Cancelled Bookings</a></li>
        </ul>
      </li>

      <!-- Payment Dropdown -->
      <li>
        <a href="http://localhost/CMA/public?url=client/c_paymentHistory"><i class="bx bx-dollar-circle"></i> Payment
          History </a>
      </li>

      <li>
        <a href="http://localhost/CMA/public?url=client/c_complaintlist"><i class="fa-solid fa-file-circle-exclamation"></i> Complaints </i></a>
      </li>

      <li>
        <a href="http://localhost/CMA/public?url=client/c_announcement"><i class='bx bxs-megaphone'></i> Announcements </i></a>

      </li>


      <li>
        <a href="http://localhost/CMA/public?url=client/c_settings"><i class="bx bx-cog"></i> Settings </i></a>

      </li>

      <li class="logout"><a href="<?= URLROOT?>/index.php?url=auth/logout"><i class="bx bx-log-out"></i> Logout</a>
      </li>
    </ul>
  </div>

  <script>
    // 🔹 Dropdown toggle
    document.querySelectorAll(".dropdown-btn").forEach(button => {
      button.addEventListener("click", e => {
        e.preventDefault();
        const dropdown = button.nextElementSibling;
        dropdown.classList.toggle("show");

        // Rotate arrow
        const arrow = button.querySelector(".arrow");
        if (arrow) arrow.classList.toggle("rotate");
      });
    });

    // 🔹 Highlight the current active menu item
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get("url"); // e.g., "client/c_upcomingBookings"

    // Select all sidebar links (EXCEPT dropdown toggle buttons)
    const menuLinks = document.querySelectorAll(".menu a:not(.dropdown-btn)");

    menuLinks.forEach(link => {
      const linkURL = new URL(link.href, window.location.origin).searchParams.get("url");

      if (linkURL === currentPage) {
        // Highlight the active link
        link.classList.add("active");

        // Expand its parent dropdown if nested
        const dropdownContainer = link.closest(".dropdown-container");
        if (dropdownContainer) {
          dropdownContainer.classList.add("show");

          // Highlight only the dropdown button (not all parent <li>)
          const dropdownBtn = dropdownContainer.previousElementSibling;
          if (dropdownBtn) {
            dropdownBtn.classList.add("active-parent");
            const arrow = dropdownBtn.querySelector(".arrow");
            if (arrow) arrow.classList.add("rotate");
          }
        }
      }
    });
  </script>


</body>

</html>