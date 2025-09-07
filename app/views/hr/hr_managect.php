<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Availability</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_managect.css">
</head>
<body>
  <!-- Main Content -->
  <div class="content">
    <h1 class="page-title">Caretakers</h1>

    <!-- Search bar -->
    <div class="search-box">
      <i class='bx bx-search'></i>
      <input type="text" id="searchInput" placeholder="Search caretakers">
    </div>

    <!-- Table -->
    <h2 class="section-title">Caretaker Availability</h2>
    <div class="table-container">
      <table class="caretaker-table" id="caretakerTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Location</th>
            <th>Check-In</th>
            <th>Check-Out</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Emily Carter</td>
            <td><span class="status available">Available</span></td>
            <td>123 Maple Street, Anytown</td>
            <td>08:00 AM</td>
            <td>04:00 PM</td>
          </tr>
          <tr>
            <td>David Lee</td>
            <td><span class="status duty">On Duty</span></td>
            <td>456 Oak Avenue, Anytown</td>
            <td>09:00 AM</td>
            <td>05:00 PM</td>
          </tr>
          <tr>
            <td>Sarah Johnson</td>
            <td><span class="status unavailable">Unavailable</span></td>
            <td>789 Pine Lane, Anytown</td>
            <td>N/A</td>
            <td>N/A</td>
          </tr>
          <tr>
            <td>Michael Brown</td>
            <td><span class="status leave">On Leave</span></td>
            <td>101 Elm Road, Anytown</td>
            <td>N/A</td>
            <td>N/A</td>
          </tr>
          <tr>
            <td>Jessica Wilson</td>
            <td><span class="status available">Available</span></td>
            <td>222 Cedar Court, Anytown</td>
            <td>08:30 AM</td>
            <td>04:30 PM</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

    <script src="<?php echo URLROOT; ?>/public/js/hr/hr_managect.js"></script>
</body>
</html>
