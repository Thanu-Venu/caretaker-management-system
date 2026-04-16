<?php
$caretakerPageTitle = 'Bookings - SmartCare';
$caretakerExtraCss = ['caretaker/ct_booking.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content booking-container">
    <header class="page-header" style="margin-bottom: 24px;">
        <h1 class="page-title" style="color: #1e88e5; font-size: 30px; font-weight: 700; margin: 0; letter-spacing: -0.02em;">Bookings</h1>
    </header>
        <?php $selectedBookingId = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : null; ?>
        <div class="top">
          <button class="top-button active" onclick="switchTab('ongoing', event)">Ongoing Bookings</button>
          <button class="top-button" onclick="switchTab('upcoming', event)">Upcoming Bookings</button>
          <button class="top-button" onclick="switchTab('past', event)">Past Bookings</button>
        </div>

      <div class="card">
        <!-- Ongoing -->
        <div class="table-container">
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
      </div>

    </div>
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_booking.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
