<?php
$caretakerPageTitle = 'Bookings - SmartCare';
$caretakerExtraCss = ['caretaker/ct_booking.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<?php
$bookingFilters = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : [];
$bookingServiceOptions = (isset($data['serviceTypeOptions']) && is_array($data['serviceTypeOptions'])) ? $data['serviceTypeOptions'] : [];
$selectedBookingService = trim((string) ($bookingFilters['service_type'] ?? ''));
$selectedBookingFrom = trim((string) ($bookingFilters['date_from'] ?? ''));
$selectedBookingTo = trim((string) ($bookingFilters['date_to'] ?? ''));
?>
<main class="content booking-container">
    <header class="page-header" style="margin-bottom: 24px;">
        <h1 class="page-title">Bookings</h1>
    </header>
    <form class="filter-section filters-inline ct-page-filters" method="get" action="<?= htmlspecialchars(URLROOT . '/public', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="url" value="caretaker/ct_booking">
      <div class="filter-group">
        <label for="bookingServiceFilter">Service</label>
        <select id="bookingServiceFilter" name="booking_service">
          <option value="">All services</option>
          <?php foreach ($bookingServiceOptions as $service): ?>
            <option value="<?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedBookingService, (string) $service) === 0 ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-group">
            <label for="bookingDateFromFilter">From</label>
            <input type="date" id="bookingDateFromFilter" name="booking_from" value="<?= htmlspecialchars($selectedBookingFrom, ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="filter-group">
            <label for="bookingDateToFilter">To</label>
            <input type="date" id="bookingDateToFilter" name="booking_to" value="<?= htmlspecialchars($selectedBookingTo, ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="filter-group filter-group--actions">
            <button type="submit" class="btn primary">Apply</button>
            <a class="btn ghost" href="<?= htmlspecialchars(URLROOT . '/public?url=caretaker/ct_booking', ENT_QUOTES, 'UTF-8') ?>">Reset</a>
          </div>
        </form>
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
