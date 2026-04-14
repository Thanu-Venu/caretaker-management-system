<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_dashboard.css">
</head>

<body>

  <?php
  $isProfileRequestPending = !empty($data['latestProfileChangeRequest']) &&
    (($data['latestProfileChangeRequest']['status'] ?? '') === 'Pending');
  ?>

  <div id="dashboard">

    <div class="content">

      <!-- Welcome -->
      <section class="welcome">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h1>

      </section>

      <!-- Dashboard Layout -->

      <main class="dashboard">
        <!-- Profile Overview -->
        <section class="card profile">
          <h3>Profile Overview</h3>
          <div class="profile-body">
            <img src="<?= URLROOT ?>/public/uploads/<?= $data['caretaker']['profile_image'] ?>" alt="Profile">
            <div>
              <div class="profile-header">
                <h4>
                  <?= htmlspecialchars($data['caretaker']['name']) ?>
                  <br>
                  <span class="rating">⭐ <?= number_format((float)($data['caretaker']['rating'] ?? 0), 1) ?></span>
                  <button class="btn-verify">
                    <?= htmlspecialchars($data['caretaker']['service_type']) ?>
                  </button>
                </h4>

                <?php if ($isProfileRequestPending): ?>
                  <button class="btn" disabled title="Profile update request is pending admin review">Edit Locked</button>
                <?php else: ?>
                  <a href="<?= URLROOT ?>/caretaker/ct_settings" class="btn" style="text-decoration: none; display: inline-block;">Edit profile</a>
                <?php endif; ?>
              </div>

              <?php if (!empty($data['latestProfileChangeRequest'])): ?>
                <p class="profile-request-status">
                  Latest profile update request status:
                  <span class="status <?= strtolower(htmlspecialchars($data['latestProfileChangeRequest']['status'])) ?>">
                    <?= htmlspecialchars($data['latestProfileChangeRequest']['status']) ?>
                  </span>
                </p>
              <?php endif; ?>

              <p class="profile-desc">
                <?= nl2br(htmlspecialchars($data['caretaker']['qualifications'])) ?>
              </p>
              <div class="tags">
                <span class="tag"><?= htmlspecialchars($data['caretaker']['service_type']) ?></span>
                <span class="tag"><?= htmlspecialchars($data['caretaker']['experience']) ?> Experience</span>
                <span class="tag"><?= htmlspecialchars($data['caretaker']['location']) ?></span>
              </div>
            </div>
          </div>
        </section>

        <!-- Availability -->
        <section class="card availability">
          <h3>Availability Status</h3>
          <br>

          <p id="availabilityText"><?= !empty($data['monthlyStats']['is_available']) ? "You're visible to clients and can receive new bookings" : "You're hidden from clients and won't receive new bookings" ?></p><br>
          <label class="switch">
            <input type="checkbox" id="availabilityToggle" <?= !empty($data['monthlyStats']['is_available']) ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>


        </section>

        <!-- Bookings -->
        <section class="card bookings">
          <h3>Upcoming Bookings</h3>
          <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Client</th>
                <th>Date & Time</th>
                <th>Service</th>
                <th>Location</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($data['upcoming'])): ?>
                <tr>
                  <td colspan="4" style="text-align:center;">No upcoming bookings</td>
                </tr>
              <?php else: ?>
                <?php foreach ($data['upcoming'] as $b): ?>
                  <tr>
                    <td><?= htmlspecialchars($b['client_name']) ?></td>
                    <td>
                      <?= htmlspecialchars($b['booking_date']) ?><br>
                      <?= htmlspecialchars($b['preferred_time']) ?>
                    </td>
                    <td><span><?= htmlspecialchars($b['service_type']) ?></span></td>
                    <td><?= htmlspecialchars($b['service_location']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>

          </table>
          </div>

          <div class="button-cont">
            <a class="btn-small" href="<?= URLROOT ?>/caretaker/ct_booking?tab=upcoming">See All</a>
          </div>
        </section>

        <!-- Schedule -->
        <section class="card schedule">
          <h3>Schedule</h3>
          <div class="calendar" onclick="window.location.href='<?= URLROOT ?>/caretaker/ct_schedule'" style="cursor: pointer;" title="Click to view full schedule">
            <p id="calendarMonthLabel">-</p>
            <div class="days">
              <span>Su</span><span>Mo</span><span>Tu</span><span>We</span>
              <span>Th</span><span>Fr</span><span>Sa</span>
            </div>
            <div class="dates" id="calendarDates"></div>
          </div>
        </section>

        <!-- Leave Management -->
        <section class="card leave">
          <?php $leaveSummary = $data['leaveMonthlySummary'] ?? ['limit' => 5, 'used' => 0, 'remaining' => 5, 'percentage' => 0, 'label' => '0 / 5 days used']; ?>
          <h3>Leave Management</h3>
          <div class="leave-progress-mini">
            <div class="mini-meta">
              <span>Used: <?= (int)$leaveSummary['used'] ?> days</span>
              <span>Remaining: <?= (int)$leaveSummary['remaining'] ?> days</span>
            </div>
            <div class="mini-track">
              <div class="mini-fill" style="width: <?= (int)$leaveSummary['percentage'] ?>%"></div>
            </div>
            <small><?= htmlspecialchars($leaveSummary['label']) ?></small>
          </div>
          <div class="button-container">
            <!-- Link to open leave add page -->
            <a href="<?= URLROOT ?>/LeaveCRUD/add" class="btn-le" style="text-decoration:none; display:inline-block; text-align:center; padding-top: 10px;">Request Leave</a>
          </div>
          <table>
            <thead>
              <tr>
                <th>Dates</th>
                <th>Reason</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data['leaves'] as $leave): ?>
                <tr>
                  <td><?= date("M d", strtotime($leave['start_date'])) ?> – <?= date("M d", strtotime($leave['end_date'])) ?></td>
                  <td><?= htmlspecialchars($leave['reason']) ?></td>
                  <td>
                    <span class="status <?= strtolower($leave['status']) ?>">
                      <?= $leave['status'] ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
          <div class="button-cont">
            <a class="btn-small" href="<?= URLROOT ?>/caretaker/ct_leave">See All</a>
          </div>

        </section>

        <!-- This Month -->
        <section class="card">
          <h3>This Month</h3>
          <div class="mon-bod">
            <p>Currently Available: <strong><?= !empty($data['monthlyStats']['is_available']) ? 'Yes' : 'No' ?></strong></p><br>
            <p>Active Bookings: <strong><?= (int)($data['monthlyStats']['active_bookings'] ?? 0) ?></strong></p><br>
            <p>Working Days: <strong><?= (int)($data['monthlyStats']['working_days'] ?? 0) ?></strong></p><br>
            <p>Completed This Month: <strong><?= (int)($data['monthlyStats']['completed_bookings'] ?? 0) ?></strong></p><br>
            <p>Rating: ⭐ <?= number_format((float)($data['monthlyStats']['rating'] ?? 0), 1) ?></p>
          </div>
        </section>
      </main>
    </div>

  </div>

  <!-- Profile Modal -->









  <script>
    window.dashboardData = {
      workingDates: <?= json_encode($data['workingDates'] ?? []) ?>,
      calendarMonth: <?= (int)($data['calendarMonth'] ?? date('n')) ?>,
      calendarYear: <?= (int)($data['calendarYear'] ?? date('Y')) ?>,
      updateAvailabilityUrl: "<?= URLROOT ?>/caretaker/updateAvailabilityStatus"
    };
  </script>
  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_dashboard.js"></script>
</body>

</html>