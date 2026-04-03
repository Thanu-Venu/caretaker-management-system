<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookings Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_booking.css">
</head>

<body>
  <main class="content">
    <div class="booking">
      <h2>Bookings</h2>

      <?php $selectedBookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null; ?>
      <div class="top">
        <button class="ongoing-book active" data-tab="ongoing" onclick="showTab('ongoing', event)">Ongoing Bookings</button>
        <button class="up-book" data-tab="upcoming" onclick="showTab('upcoming', event)">Upcoming Bookings</button>
        <button class="past-book" data-tab="past" onclick="showTab('past', event)">Past Bookings</button>
      </div>

      <section class="card">
        <!-- Ongoing -->
        <div id="ongoing" class="tab-content active">
          <table>
            <thead>
              <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Location</th>
                <th>Date / Time</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data['ongoing'])) : ?>
                <?php foreach ($data['ongoing'] as $b) : ?>
                  <?php $isSelected = ($selectedBookingId && (int)$b['booking_id'] === $selectedBookingId); ?>
                  <tr class="booking-row<?= $isSelected ? ' highlight' : '' ?>" data-booking-id="<?= (int)$b['booking_id'] ?>">
                    <td><?= htmlspecialchars($b['client_name']) ?></td>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= htmlspecialchars($b['service_location']) ?></td>
                    <td>
                      <?= $b['booking_date'] ?> - <?= $b['preferred_time'] ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="4">No ongoing bookings</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Upcoming -->
        <div id="upcoming" class="tab-content">
          <table>
            <thead>
              <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Location</th>
                <th>Date / Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data['upcoming'])) : ?>
                <?php foreach ($data['upcoming'] as $b) : ?>
                  <?php $isSelected = ($selectedBookingId && (int)$b['booking_id'] === $selectedBookingId); ?>
                  <tr class="booking-row<?= $isSelected ? ' highlight' : '' ?>" data-booking-id="<?= (int)$b['booking_id'] ?>">
                    <td><?= htmlspecialchars($b['client_name']) ?></td>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= htmlspecialchars($b['service_location']) ?></td>
                    <td>
                      <?= $b['booking_date'] ?> - <?= $b['preferred_time'] ?>
                    </td>
                    <td>
                      <?php 
                        $status = $b['status'] ?? 'Accepted';
                        $badgeClass = '';
                        $displayStatus = str_replace('_', ' ', $status);
                        
                        if ($status === 'Payment_Requested') {
                          $badgeClass = 'status-pending';
                          $displayStatus = '⏳ Payment Pending';
                        } elseif ($status === 'Advance_Paid') {
                          $badgeClass = 'status-approved';
                          $displayStatus = '✓ Payment Approved';
                        } elseif ($status === 'Accepted') {
                          $badgeClass = 'status-active';
                          $displayStatus = '✓ Accepted';
                        }
                      ?>
                      <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($displayStatus) ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="5">No upcoming bookings</td>
                </tr>
              <?php endif; ?>
            </tbody>

          </table>
        </div>

        <!-- Past -->
        <div id="past" class="tab-content">
          <table>
            <thead>
              <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Location</th>
                <th>Date / Time</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($data['past'])) : ?>
                <?php foreach ($data['past'] as $b) : ?>
                  <tr class="booking-row" data-booking-id="<?= (int)$b['booking_id'] ?>">
                    <td><?= htmlspecialchars($b['client_name']) ?></td>
                    <td><?= htmlspecialchars($b['service_type']) ?></td>
                    <td><?= htmlspecialchars($b['service_location']) ?></td>
                    <td>
                      <?= $b['booking_date'] ?> - <?= $b['preferred_time'] ?>

                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan="4">No past bookings</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </div>
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_booking.js"></script>
</body>

</html>