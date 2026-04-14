<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<?php
$form = $data['form'] ?? [];
$errors = $data['errors'] ?? [];
$warnings = $data['warnings'] ?? [];
$summary = $data['monthlySummary'] ?? ['limit' => 5, 'used' => 0, 'remaining' => 5, 'percentage' => 0, 'label' => '0 / 5 days used'];
$policy = $data['policy'] ?? ['advanceNoticeDays' => 3, 'maxPerRequest' => 7, 'monthlyLimit' => 5];
$impact = $data['impact'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Leave</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/leave_add.css">
</head>

<body>

  <main class="main-content">
    <section class="leave-layout">
      <div class="leave-summary-card">
        <h1>Request Leave</h1><br>
        <p class="subtitle">All requests are submitted as <strong>Pending</strong> and require HR approval.</p>

        <div class="summary-grid">
          <div class="metric">
            <span>Monthly Leave Limit</span>
            <strong><?= (int)$summary['limit'] ?> days</strong>
          </div>
          <div class="metric">
            <span>Used Leave Days</span>
            <strong><?= (int)$summary['used'] ?> days</strong>
          </div>
          <div class="metric">
            <span>Remaining Leave Days</span>
            <strong><?= (int)$summary['remaining'] ?> days</strong>
          </div>
        </div>

        <div class="progress-wrap">
          <div class="progress-label"><?= htmlspecialchars($summary['label']) ?></div>
          <div class="progress-track">
            <div class="progress-fill" style="width: <?= (int)$summary['percentage'] ?>%"></div>
          </div>
        </div>

        <ul class="policy-list">
          <li><strong>Sick Leave:</strong> Can start today; maximum 5 days duration.</li>
          <li><strong>Other Leaves:</strong> Request at least 3 days in advance.</li>
          <li><strong>Limits:</strong> Maximum 5 days per request and 5 days total per month.</li>
        </ul>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
              <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
          <div class="alert alert-warning">
            <?php foreach ($warnings as $warning): ?>
              <p><?= htmlspecialchars($warning) ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($impact['count'])): ?>
          <div class="booking-impact-box">
            <h3>This leave affects active bookings</h3>
            <p>Affected bookings: <strong><?= (int)$impact['count'] ?></strong></p>
            <?php if (!empty($impact['booking_ids'])): ?>
              <p>Booking IDs: <?= htmlspecialchars(implode(', ', $impact['booking_ids'])) ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="booking-impact-box booking-impact-preview" id="bookingImpactPreview" hidden>
          <h3>This leave affects active bookings</h3>
          <p id="impactMessage"></p>
          <p>Affected bookings: <strong id="impactCount">0</strong></p>
          <p id="impactIdsLine" hidden>Booking IDs: <span id="impactIds"></span></p>
        </div>

        <form method="POST" action="<?php echo URLROOT; ?>/leaveCRUD/add" id="leaveRequestForm">
          <label>Leave Type</label>
          <select name="leave_type" id="leave_type" required>
            <option value="">Select Type</option>
            <option value="Vacation" <?= (($form['leave_type'] ?? '') === 'Vacation') ? 'selected' : '' ?>>Vacation</option>
            <option value="Sick Leave" <?= (($form['leave_type'] ?? '') === 'Sick Leave') ? 'selected' : '' ?>>Sick Leave</option>
            <option value="Personal Leave" <?= (($form['leave_type'] ?? '') === 'Personal Leave') ? 'selected' : '' ?>>Personal Leave</option>
            <option value="Maternity Leave" <?= (($form['leave_type'] ?? '') === 'Maternity Leave') ? 'selected' : '' ?>>Maternity Leave</option>
          </select>
<br>
          <div class="row">
            <label>
              Start Date<br>
              <input
                type="date"
                name="start_date"
                id="start_date"
                min="<?= htmlspecialchars($data['today'] ?? date('Y-m-d')) ?>"
                value="<?= htmlspecialchars($form['start_date'] ?? '') ?>"
                data-min-start="<?= htmlspecialchars($data['minStartDate'] ?? '') ?>"
                required>
              <small id="start_date_hint"></small>
            </label>

            <label>
              End Date <br>
              <input
                type="date"
                name="end_date"
                id="end_date"
                min="<?= htmlspecialchars($data['today'] ?? date('Y-m-d')) ?>"
                value="<?= htmlspecialchars($form['end_date'] ?? '') ?>"
                required>
              <small id="end_date_hint"></small>
            </label>
          </div>

          

          <div class="inline-errors" id="inlineErrors"></div>

       
          <label>Reason for Leave</label>
          <textarea name="reason" id="reason" placeholder="Explain briefly why you need leave..." required><?= htmlspecialchars($form['reason'] ?? '') ?></textarea>

          <div class="form-actions">
            <button type="submit" class="submit-btn">Submit Leave Request</button>
            <a href="<?php echo URLROOT; ?>/leaveCRUD/index" class="cancel-btn">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>

  <script>
    window.leavePolicy = {
      advanceNoticeDays: <?= (int)$policy['advanceNoticeDays'] ?>,
      maxPerRequest: <?= (int)$policy['maxPerRequest'] ?>,
      monthlyLimit: <?= (int)$policy['monthlyLimit'] ?>,
      remainingThisMonth: <?= (int)$summary['remaining'] ?>
    };
    window.leavePreview = {
      impactUrl: '<?= htmlspecialchars($data['impactPreviewUrl'] ?? (URLROOT . '/LeaveCRUD/impactPreview')) ?>'
    };
  </script>
  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_leave.js"></script>

</body>

</html>