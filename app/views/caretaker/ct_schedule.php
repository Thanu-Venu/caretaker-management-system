<?php
$caretakerPageTitle = 'Schedule - SmartCare';
$caretakerExtraCss = ['caretaker/ct_schedule.css'];
$caretakerHeadStylesheets = ['https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css'];
$caretakerHeadScripts = ['https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content schedule-container">
    <header class="page-header">
        <h1 class="page-title">My Schedule</h1>
    </header>

    <div class="card">

    <!-- Status Legend -->
    <div class="status-legend">
      <div class="legend-item">
        <span class="legend-color" style="background-color: #ffc107;"></span>
        <span class="legend-label">Payment Pending</span>
      </div>
      <div class="legend-item">
        <span class="legend-color" style="background-color: #17a2b8;"></span>
        <span class="legend-label">Payment Approved</span>
      </div>
      <div class="legend-item">
        <span class="legend-color" style="background-color: #4CAF50;"></span>
        <span class="legend-label">Accepted</span>
      </div>
      <div class="legend-item">
        <span class="legend-color" style="background-color: #6c757d;"></span>
        <span class="legend-label">Completed</span>
      </div>
    </div>

    <div id="calendar"></div>
    </div>
  </main>

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
            <div class="detail-row">
              <span class="detail-label">Status:</span>
              <span class="detail-value" id="bookingStatus">-</span>
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
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar');
      const modal = document.getElementById("eventModal");
      const closeBtn = document.querySelector(".close");

      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: {
          url: "<?= URLROOT ?>/public/?url=caretaker/getScheduleEvents",
          method: "GET"
        },
        eventClick: function(info) {
          // Populate modal with booking details
          const props = info.event.extendedProps;

          document.getElementById('clientName').textContent = props.client || '-';
          document.getElementById('serviceType').textContent = props.service || '-';
          document.getElementById('bookingTime').textContent = props.time || '-';
          document.getElementById('bookingDate').textContent = props.dateRange || info.event.startStr.split('T')[0] || '-';
          document.getElementById('serviceDuration').textContent = props.duration || '-';
          document.getElementById('serviceLocation').textContent = props.location || '-';

          // Format status with badge styling
          const statusEl = document.getElementById('bookingStatus');
          const status = props.status || '-';
          let statusClass = '';
          let statusText = status;

          // Apply appropriate styling based on status
          if (status === 'Completed') {
            statusClass = 'status-completed';
            statusText = 'Completed';
          } else if (status === 'Accepted') {
            statusClass = 'status-accepted';
            statusText = 'Accepted';
          } else if (status === 'Payment_Requested') {
            statusClass = 'status-payment-pending';
            statusText = 'Payment Pending';
          } else if (status === 'Advance_Paid') {
            statusClass = 'status-payment-approved';
            statusText = 'Payment Approved';
          } else {
            statusClass = 'status-default';
          }

          statusEl.innerHTML = `<span class="status-badge ${statusClass}">${statusText}</span>`;

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

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>