<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR Schedule - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_schedule.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>

<body>
  <div
    id="hrScheduleApp"
    class="main-content"
    data-month-url="<?= URLROOT; ?>/hr/scheduleMonthAggregates"
    data-day-url="<?= URLROOT; ?>/hr/scheduleDayDetails"
    data-today="<?= date('Y-m-d'); ?>">
    <div class="schedule-topbar">
      <div>
        <h1>Caregiver Schedule</h1>
        <p id="selectedDateText">Loading selected date...</p>
      </div>
      <div class="rule-chip">
        Busy excludes caregivers on approved leave
      </div>
    </div>

    <div class="summary-grid" id="summaryCards">
      <button type="button" class="summary-card" data-type="active_bookings">
        <span class="summary-label">Active Bookings</span>
        <span class="summary-count" id="count-active">0</span>
      </button>
      <button type="button" class="summary-card" data-type="pending_requests">
        <span class="summary-label">Pending Requests</span>
        <span class="summary-count" id="count-pending">0</span>
      </button>
      <button type="button" class="summary-card" data-type="caregiver_leaves">
        <span class="summary-label">Caregiver Leaves</span>
        <span class="summary-count" id="count-leave">0</span>
      </button>
      <button type="button" class="summary-card" data-type="busy_caregivers">
        <span class="summary-label">Busy Caregivers</span>
        <span class="summary-count" id="count-busy">0</span>
      </button>
      <button type="button" class="summary-card" data-type="available_caregivers">
        <span class="summary-label">Available Caregivers</span>
        <span class="summary-count" id="count-available">0</span>
      </button>
    </div>

    <div class="calendar-shell">
      <div id="calendar"></div>
    </div>

    <!-- Details Panel (right side) -->
    <aside id="dayDetailsPanel" class="details-panel" aria-hidden="true">
      <div class="panel-header">
        <h3 id="panelTitle">Schedule Details</h3>
        <button type="button" id="panelCloseBtn" class="panel-close" aria-label="Close panel">&times;</button>
      </div>

      <div class="panel-content">
        <section class="panel-section">
          <h4>Active Bookings</h4>
          <div id="panel-active-bookings" class="list-wrap"></div>
        </section>

        <section class="panel-section">
          <h4>Pending Requests</h4>
          <div id="panel-pending-requests" class="list-wrap"></div>
        </section>

        <section class="panel-section">
          <h4>Leave List</h4>
          <div id="panel-leave-list" class="list-wrap"></div>
        </section>

        <section class="panel-section">
          <h4>Available Caregivers</h4>
          <div id="panel-available-caregivers" class="list-wrap"></div>
        </section>
      </div>
    </aside>

    <!-- Summary List Modal -->
    <div id="summaryListModal" class="schedule-modal" aria-hidden="true">
      <div class="schedule-modal-content">
        <div class="schedule-modal-header">
          <h3 id="summaryModalTitle">List</h3>
          <button type="button" id="summaryModalClose" class="panel-close" aria-label="Close modal">&times;</button>
        </div>
        <div id="summaryModalBody" class="list-wrap"></div>
      </div>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/hr/hr_schedule.js"></script>
</body>