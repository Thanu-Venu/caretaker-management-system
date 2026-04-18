<?php
$caretakerPageTitle = 'Leave History - SmartCare';
$caretakerExtraCss = ['caretaker/ct_leaveHistory.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content leavehistory-container">
  <header class="page-header">
    <h1 class="page-title">Leave History</h1>
  </header>

  <table class="leave-history-table">
    <thead>
      <tr>
        <th>Leave Type</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Reason</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="leaveTableBody">
      <!-- Data will be injected dynamically via JS -->
    </tbody>
  </table>

  <div class="pagination">
    <button id="prevPage" class="btn">Previous</button>
    <span id="pageInfo"></span>
    <button id="nextPage" class="btn">Next</button>
  </div>
</main>
<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_leaveHistory.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
