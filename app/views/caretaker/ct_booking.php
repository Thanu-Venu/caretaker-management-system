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

    <div class="top">
      <button class="up-book active" onclick="showTab('upcoming', event)">Upcoming Bookings</button>
      <button class="past-book" onclick="showTab('past', event)">Past Bookings</button>
    </div>

    <!-- Upcoming -->
    <section class="card">
      <div id="upcoming" class="tab-content active">
        <table>
          <thead>
            <tr>
              <th>Client</th>
              <th>Service</th>
              <th>Location</th>
              <th>Date / Time</th>
              <th>Payment</th>
            </tr>
          </thead>
          <tbody>
<?php if (!empty($data['upcoming'])) : ?>
    <?php foreach ($data['upcoming'] as $b) : ?>
        <tr>
            <td><?= htmlspecialchars($b['client_name']) ?></td>
            <td><?= htmlspecialchars($b['service_type']) ?></td>
            <td><?= htmlspecialchars($b['service_location']) ?></td>
            <td>
              <?= $b['booking_date'] ?> - <?= $b['preferred_time'] ?>
             </td>
            <td>LKR <?= number_format($b['total_payment'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr><td colspan="5">No upcoming bookings</td></tr>
<?php endif; ?>
</tbody>

        </table>
      </div>
    </section>

    <!-- Past -->
    <section class="card">
      <div id="past" class="tab-content">
        <table>
          <thead>
            <tr>
              <th>Client</th>
              <th>Service</th>
              <th>Location</th>
              <th>Date / Time</th>
              <th>Payment</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($data['past'])) : ?>
              <?php foreach ($data['past'] as $b) : ?>
                <tr>
                  <td><?= htmlspecialchars($b['client_name']) ?></td>
                  <td><?= htmlspecialchars($b['service_type']) ?></td>
                  <td><?= htmlspecialchars($b['service_location']) ?></td>
                  <td>
                   <?= $b['booking_date'] ?> - <?= $b['preferred_time'] ?>

                  </td>
                  <td>LKR <?= number_format($b['total_payment'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="5">No past bookings</td></tr>
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
