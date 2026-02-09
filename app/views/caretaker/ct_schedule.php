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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    events: {
      url: "<?= URLROOT ?>/Caretaker/getScheduleEvents",
      method: "GET"
    },
    eventClick: function(info) {
      alert(
        "Client: " + info.event.extendedProps.client +
        "\nService: " + info.event.extendedProps.service +
        "\nTime: " + info.event.extendedProps.time +
        "\nStatus: " + info.event.extendedProps.status
      );
    }
  });

  calendar.render();
});
</script>



</body>
</html>
