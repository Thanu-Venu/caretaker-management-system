<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Sidebar</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_sidebar.css">
</head>
<body>
  <div class="sidebar">
    <br><br><br>
    <ul class="nav-links">

      <li><a href="http://localhost/CMA/public?url=caretaker/ct_dashboard"><i class='bx bxs-dashboard'></i><span class="link_name">Dashboard</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_schedule"><i class='bx bxs-calendar'></i><span class="link_name">My Schedule</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_booking"><i class='bx bx-book-alt'></i><span class="link_name">Bookings</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_leave"><i class='bx bxs-calendar-check'></i><span class="link_name">Leave Request</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_complaints"><i class='bx bxs-error'></i><span class="link_name">Complaints</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_reviews"><i class='bx bxs-message-dots'></i><span class="link_name">Reviews</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_reports"><i class='bx bxs-report'></i><span class="link_name">Reports</span></a></li>
      <li><a href="http://localhost/CMA/public?url=caretaker/ct_settings"><i class='bx bxs-cog'></i><span class="link_name">Settings</span></a></li>
      <li class="logout"><a href="http://localhost/CMA/public"><i class='bx bx-log-out'></i><span class="link_name">Logout</span></a></li>

    </ul>
  </div>

  <script>
   // Select all links that have a submenu (contain .arrow)
let dropdownLinks = document.querySelectorAll(".nav-links li > a .arrow");

dropdownLinks.forEach(arrow => {
  let parentLink = arrow.parentElement; // the <a> containing arrow and text
  let parentLi = parentLink.parentElement; // the <li>

  parentLink.addEventListener("click", e => {
    e.preventDefault(); // prevent default link behavior
    parentLi.classList.toggle("showMenu");
  });
});

   // Highlight active page link
    const currentURL = new URL(window.location.href);
const currentPath = currentURL.searchParams.get("url"); // gets "caretaker/ct_dashboard"

const menuItems = document.querySelectorAll('.nav-links li a');

menuItems.forEach(link => {
  const linkURL = new URL(link.href).searchParams.get("url");
  if (linkURL === currentPath) {
    link.parentElement.classList.add('active');
  }
});






  </script>
</body>
</html>
