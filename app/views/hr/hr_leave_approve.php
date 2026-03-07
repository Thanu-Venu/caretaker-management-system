<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>
<?php if (!empty($data['error'])): ?>
  <div style="background:#ffebee;color:#b71c1c;padding:12px;border-radius:10px;margin-bottom:14px;">
    <?= htmlspecialchars($data['error']) ?>
  </div>
<?php endif; ?>

<?php
$leaveDetails = $data['leaveDetails'] ?? [];
$impact = $data['impact'] ?? [];
$usage = $data['monthlyUsage'] ?? ['used_before' => 0, 'request_days' => 0, 'used_after' => 0, 'limit' => 5];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Logs - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_leave_approve.css">
</head>

<body>
  <main class="content">
    <div class="leave-approve-card">

      <h1>Approve Leave (Reassign Required)</h1>

      <?php if (!empty($impact['count'])): ?>
        <div class="impact-banner">
          This leave request affects active bookings. Please review and assign a replacement caretaker if required.
          <br>
          Affected bookings: <strong><?= (int)$impact['count'] ?></strong>
          <?php if (!empty($impact['booking_ids'])): ?>
            | IDs: <?= htmlspecialchars(implode(', ', $impact['booking_ids'])) ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- ✅ Leave Info -->
      <div class="leave-info">
        <p><b>Leave ID:</b> <?= (int)$data['leave']->id ?></p>
        <p><b>Caregiver Name:</b> <?= htmlspecialchars($leaveDetails['caretaker_name'] ?? 'Unknown') ?></p>
        <p><b>Caregiver ID:</b> <?= (int)$data['leave']->user_id ?></p>
        <p><b>Leave Type:</b> <?= htmlspecialchars($data['leave']->leave_type) ?></p>
        <p><b>Date Range:</b> <?= htmlspecialchars($data['leave']->start_date) ?> → <?= htmlspecialchars($data['leave']->end_date) ?></p>
        <p><b>Total Leave Days:</b> <?= (int)$usage['request_days'] ?> day(s)</p>
        <p><b>Monthly Usage:</b> <?= (int)$usage['used_before'] ?> + <?= (int)$usage['request_days'] ?> = <?= (int)$usage['used_after'] ?> / <?= (int)$usage['limit'] ?></p>
        <p><b>Time:</b>
          <?= htmlspecialchars($data['leave']->start_time ?? '') ?>
          <?= ($data['leave']->start_time && $data['leave']->end_time) ? '→' : '' ?>
          <?= htmlspecialchars($data['leave']->end_time ?? '') ?>
        </p>
        <p><b>Reason:</b><br><?= nl2br(htmlspecialchars($data['leave']->reason)) ?></p>
        <p><b>Status:</b> <?= htmlspecialchars($data['leave']->status) ?></p>
      </div>

      <hr class="hr-divider" />

      <!-- ✅ Affected bookings -->
      <h3>Affected Bookings</h3>

      <?php if (empty($data['affected'])): ?>
        <p class="no-bookings">
          ✅ No active bookings during this leave period. You can approve directly.
        </p>
      <?php else: ?>
        <div style="overflow-x:auto;">
          <table class="booking-table">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Client ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Service</th>
                <th>Basis</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data['affected'] as $b): ?>
                <tr>
                  <td><?= (int)$b['id'] ?></td>
                  <td><?= (int)$b['client_id'] ?></td>
                  <td><?= htmlspecialchars($b['booking_date']) ?></td>
                  <td><?= htmlspecialchars($b['status']) ?></td>
                  <td><?= htmlspecialchars($b['service_type']) ?></td>
                  <td><?= htmlspecialchars($b['basis']) ?></td>
                  <td><?= htmlspecialchars($b['duration']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <hr class="hr-divider" />

      <!-- ✅ Approve + Reassign Form -->
      <form class="approve-form" method="POST" action="<?= URLROOT ?>/HrLeave/approve_submit">
        <input type="hidden" name="leave_id" value="<?= (int)$data['leave']->id ?>" />

        <?php $hasAffected = !empty($data['affected']); ?>

        <label><b>Select Replacement Caregiver</b></label>
        <select name="replacement_caretaker_id" <?= $hasAffected ? 'required' : '' ?>>
          <option value="">-- <?= $hasAffected ? 'required' : 'not required' ?> --</option>
          <?php foreach ($data['caretakers'] as $ct): ?>
            <option value="<?= (int)$ct['id'] ?>">
              <?= htmlspecialchars($ct['name']) ?> (ID: <?= (int)$ct['id'] ?>)
            </option>
          <?php endforeach; ?>
        </select>

        <?php if (!$hasAffected): ?>
          <p class="no-bookings">✅ No bookings affected — replacement not needed.</p>
        <?php else: ?>
          <p class="requires-replacement">Replacement caretaker selection is required for this leave.</p>
        <?php endif; ?>


        <label for="hr_note"><b>HR Note (Optional)</b></label>
        <textarea id="hr_note" name="hr_note" rows="3" placeholder="Add a note (optional)..."></textarea>

        <div class="action-buttons">
          <button type="submit" class="approve-btn" <?= !empty($data['error']) ? 'disabled' : '' ?>
            onclick="return confirm('Approve this leave and reassign all affected bookings to the selected caregiver?')">
            <i class='bx bx-check-circle'></i> Approve + Reassign
          </button>

          <a href="<?= URLROOT ?>/HrLeave/index" class="back-btn">
            <i class='bx bx-arrow-back'></i> Back
          </a>
        </div>
      </form>

    </div>
  </main>
</body>

</html>