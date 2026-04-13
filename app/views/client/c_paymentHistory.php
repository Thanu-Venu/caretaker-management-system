<?php
$clientPageTitle = 'Payment history — SmartCare';
$clientExtraCss  = ['client/c_paymentHistory.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>
<main class="main-content">
  <div class="container">

    <?php
      $payments = $data['payments'] ?? [];
      $totalPayments = 0.0;
      $completedTotal = 0.0;
      $pendingTotal = 0.0;
      $completedCount = 0;
      $pendingCount = 0;
      $failedCount = 0;
      $pendingPayment = null;

      foreach ($payments as $p) {
        $amount = (float) ($p['amount'] ?? 0);
        $totalPayments += $amount;

        $status = strtolower($p['status'] ?? '');
        if ($status === 'approved') {
          $completedTotal += $amount;
          $completedCount++;
        } elseif ($status === 'pending') {
          $pendingTotal += $amount;
          $pendingCount++;
          if ($pendingPayment === null) {
            $pendingPayment = $p;
          }
        } elseif ($status === 'rejected') {
          $failedCount++;
        }
      }
    ?>

    <!-- Title -->
    <div class="header-row">
      <div class="header">
        <h1>Payment History</h1>
        <p>Track and manage all your payment transactions.</p>
      </div>

      <?php if (!empty($pendingPayment)): ?>
        <div class="payment-action">
          <a class="payment-btn" href="<?= URLROOT ?>/client/c_payment?booking_id=<?= $pendingPayment['booking_id'] ?>">Proceed to Payment</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Summary Cards -->
    <div class="cards">
      <div class="card">
        <h3>Total Payments</h3>
        <p class="amount">LKR <?= number_format($totalPayments, 2) ?></p>
        <span><?= count($payments) ?> transactions</span>
      </div>
      <div class="card">
        <h3>Completed</h3>
        <p class="amount green">LKR <?= number_format($completedTotal, 2) ?></p>
        <span><?= $completedCount ?> successful</span>
      </div>
      <div class="card">
        <h3>Pending</h3>
        <p class="amount orange">LKR <?= number_format($pendingTotal, 2) ?></p>
        <span><?= $pendingCount ?> awaiting</span>
      </div>
      <div class="card">
        <h3>Total Hours</h3>
        <p class="amount blue"><?= array_sum(array_map(function ($p) { return (int) ($p['duration'] ?? 0); }, $payments)) ?></p>
        <span>care hours provided</span>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="filters">
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search by description or payment ID...">
      </div>
      <select id="statusFilter">
        <option value="">All Status</option>
        <option value="Completed">Completed</option>
        <option value="Pending">Pending</option>
        <option value="Failed">Failed</option>
      </select>
      <select id="serviceFilter">
        <option value="">All Services</option>
        <option value="BabySitter">BabySitter</option>
        <option value="Elder Care">Elder Care</option>
        <option value="Maid">Maid</option>
      </select>
    </div>

    <!-- Table -->
    <table id="paymentTable">
      <thead>
        <tr>
          <th>Payment ID</th>
          <th>Caretaker</th>
          <th>Service Type</th>
          <th>Hours</th>
          <th>Rate</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Service Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($payments)): ?>
          <?php foreach ($payments as $p): ?>
            <?php
              $statusRaw = strtolower($p['status'] ?? '');
              $statusLabel = $statusRaw === 'approved' ? 'Completed' : ($statusRaw === 'pending' ? 'Pending' : 'Failed');
              $statusClass = $statusRaw === 'approved' ? 'completed' : ($statusRaw === 'pending' ? 'pending' : 'failed');
              $duration = (int) ($p['duration'] ?? 0);
              $basis = $p['basis'] ?? '';
              $totalPayment = (float) ($p['total_payment'] ?? 0);
              $rate = $duration > 0 ? ($totalPayment / $duration) : 0;
              $serviceType = $p['service_type'] ?? 'N/A';
            ?>
            <tr data-status="<?= htmlspecialchars($statusLabel) ?>" data-service="<?= htmlspecialchars($serviceType) ?>">
              <td><?= htmlspecialchars($p['id']) ?></td>
              <td><?= htmlspecialchars($p['caretaker_name'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($serviceType) ?></td>
              <td><?= $duration . ' ' . htmlspecialchars($basis) ?></td>
              <td>LKR <?= number_format($rate, 2) ?>/<?= htmlspecialchars($basis) ?></td>
              <td>LKR <?= number_format((float) ($p['amount'] ?? 0), 2) ?></td>
              <td><span class="status <?= $statusClass ?>"><?= $statusLabel ?></span></td>
              <td><?= htmlspecialchars($p['booking_date'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align:center;">No payments found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <p id="noResults" style="display:none; text-align:center; margin-top:20px; color:red;">
      No matching records found.
    </p>

    <!-- Footer -->
    <div class="footer">
      <p>Showing 1 to <?= count($payments) ?> of <?= count($payments) ?> results</p>
      <div class="pagination">
        <button>&lt; Previous</button>
        <button>Next &gt;</button>
      </div>
    </div>

  </div>
  <script src="<?php echo URLROOT; ?>/public/js/client/c_paymentHistory.js"></script>
</main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
