<?php
$complaintFilters = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : [];
$complaintServiceOptions = (isset($data['serviceTypeOptions']) && is_array($data['serviceTypeOptions'])) ? $data['serviceTypeOptions'] : [];
$complaintStatusOptions = (isset($data['statusOptions']) && is_array($data['statusOptions'])) ? $data['statusOptions'] : [];
$selectedComplaintService = trim((string) ($complaintFilters['service_type'] ?? ''));
$selectedComplaintStatus = trim((string) ($complaintFilters['status'] ?? ''));
$caretakerServiceType = trim((string) ($data['caretaker_service_type'] ?? ''));

$caretakerPageTitle = 'Complaints - SmartCare';
$caretakerExtraCss = ['caretaker/ct_complaints.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content complaint-container">
      <header class="page-header">
        <h1 class="page-title">Register a Complaint</h1>
      </header>

  <form id="complaintForm" action="<?php echo URLROOT; ?>/public/index.php?url=caretaker/saveComplaint" method="POST" data-caretaker-service="<?= htmlspecialchars($caretakerServiceType, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="form_token" value="<?php echo htmlspecialchars($data['form_token'] ?? ''); ?>">
    <input type="hidden" name="complaint_ajax" value="1">
    <label for="clientBooking">Client &amp; active booking</label>
   <select id="clientBooking" name="booking_id" required>
    <option value="">— Select —</option>

    <?php foreach ($data['clients'] as $client):
        $bd = (string) ($client['booking_date'] ?? '');
        $ed = (string) ($client['booking_end_date'] ?? $bd);
        $optLabel = (string) ($client['client_name'] ?? '');
        if ($bd !== '') {
            $tsS = strtotime($bd);
            $tsE = strtotime($ed !== '' ? $ed : $bd);
            if ($tsS !== false && $tsE !== false) {
                if ($bd === $ed) {
                    $optLabel .= ' — ' . date('M j, Y', $tsS);
                } else {
                    $optLabel .= ' — ' . date('M j', $tsS) . ' – ' . date('M j, Y', $tsE);
                }
            }
        }
        ?>
        <option
            value="<?= (int) ($client['booking_id'] ?? 0); ?>"
            data-booking-start="<?= htmlspecialchars($bd, ENT_QUOTES, 'UTF-8'); ?>"
            data-booking-end="<?= htmlspecialchars($ed !== '' ? $ed : $bd, ENT_QUOTES, 'UTF-8'); ?>"
        >
            <?= htmlspecialchars($optLabel, ENT_QUOTES, 'UTF-8'); ?>
        </option>
    <?php endforeach; ?>

</select>



    <input type="hidden" name="service_type" id="caretakerServiceType" value="<?= htmlspecialchars($caretakerServiceType, ENT_QUOTES, 'UTF-8') ?>">
    <div class="complaint-field-readonly">
      <label for="caretakerServiceDisplay">Your service type</label>
      <p id="caretakerServiceDisplay" class="complaint-service-readonly" role="status">
        <?= $caretakerServiceType !== '' ? htmlspecialchars($caretakerServiceType, ENT_QUOTES, 'UTF-8') : '— Not set on your profile —' ?>
      </p>
      <?php if ($caretakerServiceType === ''): ?>
        <p class="complaint-service-missing">Set your service type in profile settings before submitting a complaint.</p>
      <?php else: ?>
        <p class="complaint-service-hint">Taken from your caregiver profile (cannot be changed here).</p>
      <?php endif; ?>
    </div>

    <div class="complaint-date-section">
      <label id="complaintDateLabel">Date of service</label>
      <p class="complaint-period-hint" id="complaintPeriodHint">Select a booking to see the service period on the calendar.</p>
      <div class="complaint-calendar-wrap" id="complaintCalendarWrap" hidden>
        <div class="complaint-calendar-head">
          <button type="button" class="complaint-cal-nav" id="complaintCalPrev" aria-label="Previous month">‹</button>
          <span class="complaint-cal-title" id="complaintCalTitle" aria-live="polite"></span>
          <button type="button" class="complaint-cal-nav" id="complaintCalNext" aria-label="Next month">›</button>
        </div>
        <div class="complaint-cal-weekdays" aria-hidden="true">
          <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
        </div>
        <div class="complaint-cal-grid" id="complaintCalGrid" role="grid" aria-labelledby="complaintDateLabel"></div>
        <p class="complaint-selected-date">Selected day: <strong id="complaintSelectedDateLabel">—</strong></p>
      </div>
      <input type="hidden" name="service_date" id="dateOfService" value="" required>
    </div>

    <label for="complaintDesc">Complaint Description</label>
    <textarea id="complaintDesc" name="description" placeholder="Describe the issue..." required></textarea>

    <button type="submit" class="btn-submit"<?= $caretakerServiceType === '' ? ' disabled' : '' ?>>Submit Complaint</button>
  </form>

  <div class="card">
    <h2 class="complaints-past-heading">Past Complaints</h2>
    <form class="filter-section filters-inline ct-page-filters" method="get" action="<?= htmlspecialchars(URLROOT . '/public', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="url" value="caretaker/ct_complaints">
        <div class="filter-group">
            <label for="complaintStatusFilter">Status</label>
            <select id="complaintStatusFilter" name="complaint_status">
                <option value="">All statuses</option>
                <?php foreach ($complaintStatusOptions as $status): ?>
                    <option value="<?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedComplaintStatus, (string) $status) === 0 ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="complaintServiceFilter">Service</label>
            <select id="complaintServiceFilter" name="complaint_service">
                <option value="">All services</option>
                <?php foreach ($complaintServiceOptions as $service): ?>
                    <option value="<?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedComplaintService, (string) $service) === 0 ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= htmlspecialchars(URLROOT . '/public?url=caretaker/ct_complaints', ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </div>
    </form>
    <div class="table-container">
    <table class="complaint-table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Date</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="complaintTableBody">
            <?php if (!empty($data['complaints'])): ?>
                <?php foreach ($data['complaints'] as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['client_name']) ?></td>
                        <td><?= htmlspecialchars($c['service_type']) ?></td>
                        <td><?= htmlspecialchars($c['service_date']) ?></td>
                        <td><?= htmlspecialchars($c['description']) ?></td>
                        <td>
                            <?php
                                $statusClass = 'status';
                                if ($c['status'] === 'Open') {
                                    $statusClass .= ' pending';
                                } elseif ($c['status'] === 'Resolved' || $c['status'] === 'Closed') {
                                    $statusClass .= ' resolved';
                                } elseif ($c['status'] === 'In Progress') {
                                    $statusClass .= ' InProgress';
                                }
                            ?>
                            <span class="<?= $statusClass ?>"><?= htmlspecialchars($c['status']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--complaint-muted); padding: 24px;">No complaints yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
  </div>

</main>

<div id="successPopup" class="complaint-popup" style="display: none;">
    <div class="complaint-popup__card">
        <h3 class="complaint-popup__title">Success!</h3>
        <p class="complaint-popup__message">Your complaint has been submitted successfully.</p>
        <button class="complaint-popup__btn btn btn-primary" onclick="closeSuccessPopup()">Close</button>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_complaints.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>