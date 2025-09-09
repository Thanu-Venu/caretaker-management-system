<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_schedule.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


</head>
<body>
    <!-- Sidebar and Navbar already exist in main dashboard -->

<div class="main-content">
  <h1>Caregiver Scheduling</h1>
  <div id="calendar"></div>

  <!-- Modal for selected date -->
  <div id="scheduleModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3 id="modalDate">Date: </h3>
      <table id="scheduleTable">
        <thead>
          <tr>
            <th>Caretaker</th>
            <th>Service</th>
            <th>Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Filled dynamically -->
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_schedule.js"></script>
</body>
</html>
