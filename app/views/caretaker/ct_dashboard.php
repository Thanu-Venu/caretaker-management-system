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

                <button onclick="<?= $isProfileRequestPending ? 'return false;' : 'openProfile()' ?>" class="btn" <?= $isProfileRequestPending ? 'disabled title="Profile update request is pending admin review"' : '' ?>>
                  <?= $isProfileRequestPending ? 'Edit Locked' : 'Edit profile' ?>
                </button>
              </div>

              <?php if (!empty($data['latestProfileChangeRequest'])): ?>
                <p class="profile-request-status">
                  Latest profile update request status:
                  <strong><?= htmlspecialchars($data['latestProfileChangeRequest']['status']) ?></strong>
                </p>
              <?php endif; ?>

              <p class="profile-desc">
                <?= nl2br(htmlspecialchars($data['caretaker']['qualifications'])) ?>
              </p>
              <div class="tags">
                <span class="tag"><?= htmlspecialchars($data['caretaker']['service_type']) ?></span>
                <span class="tag"><?= htmlspecialchars($data['caretaker']['experience']) ?> Years Experience</span>
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

          <div class="button-cont">
            <a class="btn-small" href="<?= URLROOT ?>/caretaker/ct_booking?tab=upcoming">See All</a>
          </div>
        </section>

        <!-- Schedule -->
        <section class="card schedule">
          <h3>Schedule</h3>
          <div class="calendar">
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
            <!-- Button to open modal -->
            <button id="openLeaveModal" class="btn-le">Request Leave</button>

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
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <h2 class="Edit">Request Profile Update</h2>
      <p class="subtext">Your request will be sent to admin for approval before changes appear publicly.</p>
      <form id="profileForm" method="POST" action="<?= URLROOT ?>/caretaker/editCaretakerDetails">
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['caretaker']['name'] ?? '') ?>" placeholder="Name" required <?= $isProfileRequestPending ? 'readonly' : '' ?>>
        <input type="email" name="email" value="<?= htmlspecialchars($data['caretaker']['email'] ?? '') ?>" placeholder="Email" required <?= $isProfileRequestPending ? 'readonly' : '' ?>>
        <input type="text" name="phone" value="<?= htmlspecialchars($data['caretaker']['phone'] ?? '') ?>" placeholder="Phone" required <?= $isProfileRequestPending ? 'readonly' : '' ?>>
        <input type="text" id="experience" name="experience" value="<?= htmlspecialchars($data['caretaker']['experience'] ?? '') ?>" placeholder="Experience" required <?= $isProfileRequestPending ? 'readonly' : '' ?>>
        <input type="text" name="profile_image" value="<?= htmlspecialchars($data['caretaker']['profile_image'] ?? 'default.png') ?>" hidden>
        <input type="text" name="location" value="<?= htmlspecialchars($data['caretaker']['location'] ?? '') ?>" placeholder="Location" required <?= $isProfileRequestPending ? 'readonly' : '' ?>>
        <textarea id="qualifications" name="qualifications" placeholder="Qualifications" required <?= $isProfileRequestPending ? 'readonly' : '' ?>><?= htmlspecialchars($data['caretaker']['qualifications'] ?? '') ?></textarea>
        <div class="button-container">
          <button type="submit" class="save-btn" <?= $isProfileRequestPending ? 'disabled' : '' ?>>
            <?= $isProfileRequestPending ? 'Request Pending' : 'Send Request' ?>
          </button>
          <button type="button" class="close-btn" onclick="closeProfile()">Close</button>
        </div>
      </form>
    </div>
  </div>


  <!-- Modal -->
  <div id="leaveModal" class="le-modal">
    <div class="le-modal-content">
      <span id="closeLeaveModal" class="close">&times;</span>
      <h2>Request Leave</h2>
      <p class="subtext">Submit a new leave request</p>

      <form id="leaveForm" method="POST" action="<?= URLROOT ?>/LeaveCRUD/add">
        <label>Leave Type
          <select name="leave_type" required>
            <option value="">Select</option>
            <option value="Vacation">Vacation</option>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Personal Leave">Personal Leave</option>
            <option value="Maternity Leave">Maternity Leave</option>
          </select>
        </label>

        <div class="row">
          <label>Start Date <input type="date" name="start_date" required></label>
          <label>End Date <input type="date" name="end_date" required></label>
        </div>

        <div class="row">
          <label>Start Time <input type="time" name="start_time" value="09:00" required></label>
          <label>End Time <input type="time" name="end_time" value="17:00" required></label>
        </div>

        <label>Reason
          <textarea name="reason" placeholder="Please provide a reason for your leave request..." required></textarea>
        </label>

        <button type="submit" class="submit-btn">Submit Request</button>
      </form>
    </div>
  </div>





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