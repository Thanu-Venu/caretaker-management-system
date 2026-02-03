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

  <!-- Modal CSS removed - using external stylesheet -->
</head>
<body>

<div class="main-content">
  <h1>My Schedule</h1>
  <div id="calendar"></div>
</div>

<!-- Booking Details Modal -->
<div id="eventModal" class="modal">
  <div class="modal-card">
    <div class="modal-header">
      <h3>Booking Details</h3>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <div class="booking-details-grid">
        <!-- Booking Information Section -->
        <div class="detail-section">
          <h4 class="section-title">Booking Information</h4>
          <div class="detail-row">
            <span class="detail-label">Client Name:</span>
            <span class="detail-value" id="clientName">-</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date:</span>
            <span class="detail-value" id="bookingDate">-</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Time:</span>
            <span class="detail-value" id="bookingTime">-</span>
          </div>
        </div>

        <!-- Service Information Section -->
        <div class="detail-section">
          <h4 class="section-title">Service Details</h4>
          <div class="detail-row">
            <span class="detail-label">Service Type:</span>
            <span class="detail-value" id="serviceType">-</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Duration:</span>
            <span class="detail-value" id="serviceDuration">-</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Location:</span>
            <span class="detail-value" id="serviceLocation">-</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Total Payment:</span>
            <span class="detail-value payment" id="totalPayment">-</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const modal = document.getElementById("eventModal");
  const closeBtn = document.querySelector(".close");

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
   events: {
  url: "<?= URLROOT ?>/public/?url=Caretaker/getScheduleEvents",
  method: "GET"
}
,
   eventClick: function(info) {
      // Populate modal with booking details
      const props = info.event.extendedProps;
      
      document.getElementById('clientName').textContent = props.client || '-';
      document.getElementById('serviceType').textContent = props.service || '-';
      document.getElementById('bookingTime').textContent = props.time || '-';
      document.getElementById('bookingDate').textContent = info.event.startStr.split('T')[0] || '-';
      document.getElementById('serviceDuration').textContent = props.duration || '-';
      document.getElementById('serviceLocation').textContent = props.location || '-';
      document.getElementById('totalPayment').textContent = props.payment ? '₨ ' + props.payment : '-';
      
      modal.style.display = "block";
    }

  });

  calendar.render();

  // Close modal
  closeBtn.onclick = function() {
    modal.style.display = "none";
  }
  window.onclick = function(event) {
    if (event.target == modal) modal.style.display = "none";
  }
});
</script>

</body>
</html>
