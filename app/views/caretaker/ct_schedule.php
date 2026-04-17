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
      <div class="legend-item">
        <span class="legend-color" style="background-color: #ffebee; border: 1px solid #ef9a9a;"></span>
        <span class="legend-label">Leave</span>
      </div>
    </div>

    <div id="fullCalendar"></div>
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
      const calendarEl = document.getElementById('fullCalendar');
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
          const props = info.event.extendedProps;
          
          // Check if this is a leave event or booking event
          if (props.eventType === 'leave') {
            // Show leave popup
            showLeavePopup(info.event.startStr, props);
          } else {
            // Show booking details popup
            showBookingPopup(props, info.event.startStr);
          }
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

    function showLeavePopup(date, props) {
      const modal = document.getElementById("eventModal");
      const modalHeader = modal.querySelector(".modal-header h3");
      const modalBody = modal.querySelector(".modal-body");
      
      // Update modal header
      modalHeader.textContent = "Leave Information";
      
      // Format date for display
      const dateObj = new Date(date);
      const formattedDate = dateObj.toLocaleDateString("en-US", { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      });
      
      // Update modal content with only leave information
      modalBody.innerHTML = `
        <div class="booking-details-grid">
          <div class="detail-section">
            <h4 class="section-title">Leave Information</h4>
            <div class="detail-row">
              <span class="detail-label">Date:</span>
              <span class="detail-value">${formattedDate}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Leave Type:</span>
              <span class="detail-value">${props.leaveType || 'Leave'}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Status:</span>
              <span class="detail-value">
                <span class="status-badge" style="background: #ffebee; color: #c62828; border: 1px solid #ef9a9a;">Leave</span>
              </span>
            </div>
            ${props.reason ? `
            <div class="detail-row">
              <span class="detail-label">Reason:</span>
              <span class="detail-value">${props.reason}</span>
            </div>
            ` : ''}
          </div>
        </div>
      `;
      
      modal.style.display = "block";
    }

    function showBookingPopup(props, dateStr) {
      const modal = document.getElementById("eventModal");
      const modalHeader = modal.querySelector(".modal-header h3");
      const modalBody = modal.querySelector(".modal-body");
      
      // Update modal header
      modalHeader.textContent = "Booking Details";
      
      // Populate modal with booking details
      modalBody.innerHTML = `
        <div class="booking-details-grid">
          <!-- Booking Information Section -->
          <div class="detail-section">
            <h4 class="section-title">Booking Information</h4>
            <div class="detail-row">
              <span class="detail-label">Client Name:</span>
              <span class="detail-value">${props.client || '-'}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date:</span>
              <span class="detail-value">${props.dateRange || dateStr.split('T')[0] || '-'}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Time:</span>
              <span class="detail-value">${props.time || '-'}</span>
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
              <span class="detail-value">${props.service || '-'}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Duration:</span>
              <span class="detail-value">${props.duration || '-'}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Location:</span>
              <span class="detail-value">${props.location || '-'}</span>
            </div>
          </div>
        </div>
      `;

      // Format status with badge styling
      const statusEl = modalBody.querySelector("#bookingStatus");
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
  </script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>