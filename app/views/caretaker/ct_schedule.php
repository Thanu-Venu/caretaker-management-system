<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Schedule</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_schedule.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body>
  <div class="main-content">
    <h1>My Schedule</h1>
    <div id="calendar"></div>

    <!-- Stylish Modal -->
    <div id="scheduleModal" class="modal">
      <div class="modal-card">
        <div class="modal-header">
          <h3 id="modalDate">📅 Date</h3>
          <span class="close">&times;</span>
        </div>
        <div class="modal-body">
          <table id="scheduleTable">
            <thead>
              <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Dynamic Rows -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_schedule.js"></script>
</body>
</html>
