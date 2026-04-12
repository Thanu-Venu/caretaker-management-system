<?php
$hrPageTitle = 'Caregiver schedule — HR';
$hrExtraCss  = ['hr/hr_schedule.css'];
$hrHeadStylesheets = [
    'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css',
];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main
    id="hrScheduleApp"
    class="main-content"
    data-month-url="<?= URLROOT; ?>/hr/scheduleMonthAggregates"
    data-day-url="<?= URLROOT; ?>/hr/scheduleDayDetails"
    data-today="<?= date('Y-m-d'); ?>">
    <header class="page-header schedule-page-header">
        <div class="schedule-page-header__main">
            <h1 class="page-title">Caregiver schedule</h1>
            <p class="page-subtitle" id="selectedDateText">Loading selected date…</p>
        </div>
        <p class="schedule-rule-chip" role="note">Busy excludes caregivers on approved leave</p>
    </header>

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
        <button type="button" id="panelCloseBtn" class="panel-close" aria-label="Close panel"><i class="bx bx-x" aria-hidden="true"></i></button>
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
          <button type="button" id="summaryModalClose" class="panel-close" aria-label="Close modal"><i class="bx bx-x" aria-hidden="true"></i></button>
        </div>
        <div id="summaryModalBody" class="list-wrap"></div>
      </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="<?php echo URLROOT; ?>/public/js/hr/hr_schedule.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
