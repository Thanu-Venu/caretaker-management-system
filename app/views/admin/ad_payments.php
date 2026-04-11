<?php
$summary = $data['summary'] ?? [];
$payments = $data['payments'] ?? [];
$filters = $data['filters'] ?? [];
$filterOptions = $data['filterOptions'] ?? [];
$currentPage = (int)($data['currentPage'] ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$totalRecords = (int)($data['totalRecords'] ?? 0);

function esc($value)
{
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function money($value)
{
  return 'LKR ' . number_format((float)$value, 2);
}

$statuses = $filterOptions['statuses'] ?? [];
$paymentTypes = $filterOptions['payment_types'] ?? [];
$paymentMethods = $filterOptions['payment_methods'] ?? [];
$bookingStatuses = $filterOptions['booking_statuses'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Payment Summary</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_payments.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
  <div class="payments-page">
    <div class="payments-header">
      <div>
        <h1>Admin Payment Summary</h1>
        <p>View, filter, summarize, and export all payment details.</p>
      </div>
      <div class="header-actions">
        <a class="btn secondary" href="<?php echo URLROOT; ?>/admin/ad_payments?<?php echo http_build_query(array_merge($filters, ['export' => 1, 'format' => 'csv'])); ?>">Export CSV</a>
        <a class="btn secondary" href="<?php echo URLROOT; ?>/admin/ad_payments?<?php echo http_build_query(array_merge($filters, ['export' => 1, 'format' => 'pdf'])); ?>" target="_blank" rel="noopener">Export PDF</a>
      </div>
    </div>

    <section class="summary-grid">
      <article class="summary-card">
        <span>Total Records</span>
        <strong><?php echo esc($summary['total_records'] ?? 0); ?></strong>
      </article>
      <article class="summary-card">
        <span>Unique Clients</span>
        <strong><?php echo esc($summary['unique_clients'] ?? 0); ?></strong>
      </article>
      <article class="summary-card">
        <span>Total Collected</span>
        <strong><?php echo esc(money($summary['total_collected'] ?? 0)); ?></strong>
      </article>
      <article class="summary-card">
        <span>Pending Payments</span>
        <strong><?php echo esc($summary['pending_count'] ?? 0); ?></strong>
      </article>
      <article class="summary-card">
        <span>Rejected Payments</span>
        <strong><?php echo esc($summary['rejected_count'] ?? 0); ?></strong>
      </article>
      <article class="summary-card">
        <span>Outstanding Balance</span>
        <strong><?php echo esc(money($summary['outstanding_balance'] ?? 0)); ?></strong>
      </article>
    </section>

    <section class="filters-panel">
      <form method="GET" action="<?php echo URLROOT; ?>/admin/ad_payments" class="filters-grid">
        <input type="hidden" name="url" value="admin/ad_payments">
        <div class="field">
          <label for="search">Search</label>
          <input id="search" type="text" name="search" value="<?php echo esc($filters['search'] ?? ''); ?>" placeholder="Payment ID, booking ID, client, caretaker">
        </div>
        <div class="field">
          <label for="status">Payment Status</label>
          <select id="status" name="status">
            <option value="">All</option>
            <?php foreach ($statuses as $status): ?>
              <option value="<?php echo esc($status); ?>" <?php echo (($filters['status'] ?? '') === $status) ? 'selected' : ''; ?>><?php echo esc(ucfirst($status)); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="payment_type">Payment Type</label>
          <select id="payment_type" name="payment_type">
            <option value="">All</option>
            <?php foreach ($paymentTypes as $type): ?>
              <option value="<?php echo esc($type); ?>" <?php echo (($filters['payment_type'] ?? '') === $type) ? 'selected' : ''; ?>><?php echo esc(ucfirst($type)); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="payment_method">Method</label>
          <select id="payment_method" name="payment_method">
            <option value="">All</option>
            <?php foreach ($paymentMethods as $method): ?>
              <option value="<?php echo esc($method); ?>" <?php echo (($filters['payment_method'] ?? '') === $method) ? 'selected' : ''; ?>><?php echo esc(str_replace('_', ' ', ucfirst($method))); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="booking_status">Booking Status</label>
          <select id="booking_status" name="booking_status">
            <option value="">All</option>
            <?php foreach ($bookingStatuses as $bookingStatus): ?>
              <option value="<?php echo esc($bookingStatus); ?>" <?php echo (($filters['booking_status'] ?? '') === $bookingStatus) ? 'selected' : ''; ?>><?php echo esc(str_replace('_', ' ', $bookingStatus)); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="from">From</label>
          <input id="from" type="date" name="from" value="<?php echo esc($filters['from'] ?? ''); ?>">
        </div>
        <div class="field">
          <label for="to">To</label>
          <input id="to" type="date" name="to" value="<?php echo esc($filters['to'] ?? ''); ?>">
        </div>
        <div class="field actions">
          <button type="submit" class="btn primary">Apply Filters</button>
          <a class="btn ghost" href="<?php echo URLROOT; ?>/admin/ad_payments">Reset</a>
        </div>
      </form>
    </section>

    <section class="table-panel">
      <div class="table-wrap">
        <table class="payments-table">
          <thead>
            <tr>
              <th>Payment ID</th>
              <th>Booking ID</th>
              <th>Client</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($payments)): ?>
              <tr>
                <td colspan="6" class="empty">No payment records found for selected filters.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($payments as $payment): ?>
                <tr>
                  <td>#<?php echo esc($payment['payment_id']); ?></td>
                  <td>#<?php echo esc($payment['booking_id']); ?></td>
                  <td><?php echo esc($payment['client_name']); ?></td>
                  <td><?php echo esc(money($payment['amount'])); ?></td>
                  <td>
                    <span class="status status-<?php echo esc($payment['status']); ?>">
                      <?php echo esc(ucfirst($payment['status'])); ?>
                    </span>
                  </td>
                  <td class="action-cell actions">
                    <button
                      type="button"
                      class="btn secondary btn-sm action-view-btn action-view-btn--icon js-payment-detail"
                      aria-label="View full payment details"
                      title="View details"
                      data-payment="<?php echo esc(json_encode($payment, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)); ?>">
                      <i class="bx bx-show" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php
          $queryBase = $filters;
          if ($currentPage > 1):
          ?>
            <a href="<?php echo URLROOT; ?>/admin/ad_payments?<?php echo http_build_query(array_merge($queryBase, ['page' => $currentPage - 1])); ?>">Prev</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?php echo $i === $currentPage ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/ad_payments?<?php echo http_build_query(array_merge($queryBase, ['page' => $i])); ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
          <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo URLROOT; ?>/admin/ad_payments?<?php echo http_build_query(array_merge($queryBase, ['page' => $currentPage + 1])); ?>">Next</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_payments.js"></script>
</body>

</html>
