<?php
$clientPageTitle = 'Payments — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_payments.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

?>
    <?php
    $summary = $data['summary'] ?? [];
    $actionItems = $data['action_items'] ?? [];
    $bookingOverview = $data['booking_overview'] ?? [];
    $paymentHistory = $data['payment_history'] ?? [];
    $filters = $data['filters'] ?? [];

    $tab = $filters['tab'] ?? 'all';

    function statusClass($status)
    {
        $status = strtolower((string)$status);
        if (in_array($status, ['paid', 'approved'], true)) return 'status-paid';
        if (in_array($status, ['upcoming', 'pending'], true)) return 'status-upcoming';
        if (in_array($status, ['due_soon', 'advance_required'], true)) return 'status-due-soon';
        if ($status === 'overdue') return 'status-overdue';
        if (in_array($status, ['cancelled', 'rejected'], true)) return 'status-cancelled';
        return 'status-upcoming';
    }

    function effectivePaymentStatus($row)
    {
        $bookingStatus = strtolower(trim((string)($row['booking_status'] ?? '')));
        if (in_array($bookingStatus, ['cancelled', 'rejected'], true)) {
            return $bookingStatus;
        }

        return strtolower((string)($row['payment_status'] ?? $row['status'] ?? ''));
    }

    function statusLabel($row)
    {
        $status = effectivePaymentStatus($row);
        if ($status === 'advance_required') return 'Advance Required';
        if ($status === 'pending') {
            $days = (int)($row['days_delta'] ?? 99);
            if ($days <= 0) return 'Due Now';
            if ($days <= 7) return 'Due in ' . $days . ' day(s)';
            return 'Upcoming';
        }
        if ($status === 'overdue') {
            $days = abs((int)($row['days_delta'] ?? 0));
            return 'Overdue by ' . $days . ' day(s)';
        }
        if ($status === 'approved') return 'Completed';
        if ($status === 'paid') return 'Paid';
        if ($status === 'rejected') return 'Rejected';
        if ($status === 'cancelled') return 'Cancelled';
        return ucfirst($status);
    }

    // Data is filtered in controller by selected tab.
    $filteredActionItems = $actionItems;
    $filteredPaymentHistory = $paymentHistory;
    ?>

    <main class="main-content admin-dashboard-page client-payments-page">
    <div class="payments-page">
        <header class="page-header client-payments-header">
            <div>
                <h1 class="page-title">Payments</h1>
                <p class="page-subtitle text-muted">Check what you need to pay, what is coming soon, and what you have already paid</p>
            </div>
        </header>

        <p class="client-payments-lead" role="note">Start with the summary below, then use <strong>Pay now</strong> when payment is available, or <strong>View details</strong> for the full breakdown.</p>

        <div class="summary-grid" aria-label="Payment summary">
            <div class="summary-card">
                <h3>Money still to pay</h3>
                <p>LKR <?= number_format((float)($summary['pending_amount'] ?? 0), 2) ?></p>
            </div>
            <div class="summary-card">
                <h3>Due in the next 7 days</h3>
                <p><?= (int)($summary['due_this_week_count'] ?? 0) ?></p>
            </div>
            <div class="summary-card">
                <h3>Overdue</h3>
                <p><?= (int)($summary['overdue_count'] ?? 0) ?></p>
            </div>
            <div class="summary-card">
                <h3>Paid this month</h3>
                <p>LKR <?= number_format((float)($summary['paid_this_month'] ?? 0), 2) ?></p>
            </div>
            <div class="summary-card">
                <h3>Bookings with a payment plan</h3>
                <p><?= (int)($summary['active_bookings_with_payments'] ?? 0) ?></p>
            </div>
        </div>

        <form method="get" action="<?= URLROOT ?>/client/payments" class="filter-panel" id="clientPaymentsFilters" aria-label="Filter payments">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">

            <div class="filter-field">
                <label for="pay-filter-status">Payment status</label>
                <select id="pay-filter-status" name="status">
                <option value="all">All Status</option>
                <option value="advance_required" <?= (($filters['status'] ?? '') === 'advance_required') ? 'selected' : '' ?>>Advance Required</option>
                <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="overdue" <?= (($filters['status'] ?? '') === 'overdue') ? 'selected' : '' ?>>Overdue</option>
                <option value="approved" <?= (($filters['status'] ?? '') === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= (($filters['status'] ?? '') === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>



            <div class="filter-field">
                <label for="pay-filter-from">From date</label>
                <input type="date" id="pay-filter-from" name="from_date" value="<?= htmlspecialchars($filters['from_date'] ?? '') ?>">
            </div>
            <div class="filter-field">
                <label for="pay-filter-to">To date</label>
                <input type="date" id="pay-filter-to" name="to_date" value="<?= htmlspecialchars($filters['to_date'] ?? '') ?>">
            </div>

            <div class="filter-field filter-field--actions">
                <span class="filter-actions-label" aria-hidden="true">&nbsp;</span>
                <div class="filter-actions-btns">
                    <button type="submit" class="btn primary">Apply filters</button>
                    <a href="<?= URLROOT ?>/client/payments?tab=<?= urlencode($tab) ?>" class="reset-btn btn ghost">Clear filters</a>
                </div>
            </div>
        </form>

         <div class="filter-panel-top-actions">
                    <div class="filter-field filter-field--search filter-field--search-top">
                        <label for="pay-filter-search">Search</label>
                        <input type="text" id="pay-filter-search" name="search" form="clientPaymentsFilters" placeholder="Booking number, caregiver, or service"
                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>

                    <button type="submit" form="clientPaymentsFilters" class="btn primary">Search</button>
                </div>

        <div class="payments-toolbar">
            <p class="client-payments-tabs-hint text-muted">Choose a tab to change which list you are looking at. Your filters apply to the tables below.</p>

            <div class="tabs-and-search-row">
                <div class="tabs-wrap" aria-label="Quick views for payment lists">
                    <?php
                    $tabs = [
                        'all' => 'All Payments',
                        'due_now' => 'Due Now',
                        'upcoming' => 'Upcoming Payments',
                        'overdue' => 'Overdue Payments',
                        'paid_history' => 'Paid History',
                        
                    ];

                    foreach ($tabs as $key => $label):
                        $isActive = $tab === $key;
                    ?>
                        <a class="tab-link <?= $isActive ? 'active' : '' ?>" href="<?= URLROOT ?>/client/payments?tab=<?= urlencode($key) ?>">
                            <?= htmlspecialchars($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

               
            </div>
        </div>

        <section class="section-card payments-results-card" <?= in_array($tab, ['paid_history']) ? 'style="display: none;"' : '' ?>>
            <div class="section-header">
                <div>
                    <h2>Needs your attention</h2>
                    <p class="section-sub text-muted">Payments where something is due or waiting on you.</p>
                </div>
                <span class="section-count"><?= count($filteredActionItems) ?> row(s)</span>
            </div>

            <div class="table-wrap">
                <table class="client-payments-action-table" data-table-collapse="off">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Service</th>
                            <th>Caretaker</th>
                            <th>Amount Due</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filteredActionItems)): ?>
                            <tr>
                                <td colspan="7">No payment actions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filteredActionItems as $item): ?>
                                <?php
                                $effectiveStatus = effectivePaymentStatus($item);
                                $canPayNow = !empty($item['can_pay_now']) && !in_array($effectiveStatus, ['cancelled', 'rejected'], true);
                                ?>
                                <tr>
                                    <td>#<?= (int)$item['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($item['service_type']) ?> (<?= htmlspecialchars($item['basis']) ?>)</td>
                                    <td><?= htmlspecialchars($item['caretaker_name']) ?></td>
                                    <td>LKR <?= number_format((float)$item['amount_due'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['due_date'] ?? '-') ?></td>
                                    <td>
                                        <span class="pill <?= statusClass($effectiveStatus) ?>">
                                            <?= htmlspecialchars(statusLabel($item)) ?>
                                        </span>
                                    </td>
                                    <td class="payments-actions-cell">
                                        <div class="payment-row-actions" role="group" aria-label="Actions for booking <?= (int)$item['booking_id'] ?>">
                                            <?php if ($canPayNow): ?>
                                                <?php if (($item['source_type'] ?? '') === 'advance'): ?>
                                                    <a class="action-btn pay" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$item['booking_id'] ?>">Pay now</a>
                                                <?php else: ?>
                                                    <a class="action-btn pay" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$item['booking_id'] ?>&recurring_payment_id=<?= (int)$item['recurring_payment_id'] ?>">Pay now</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="action-btn disabled" disabled>
                                                    <?= in_array($effectiveStatus, ['cancelled', 'rejected'], true) ? 'Cancelled' : 'Pay unavailable' ?>
                                                </button>
                                            <?php endif; ?>
                                            <a class="action-btn details" href="<?= URLROOT ?>/client/paymentDetails/<?= (int)$item['booking_id'] ?>">
                                                <?= in_array($effectiveStatus, ['cancelled', 'rejected'], true) ? 'View cancelled details' : 'View details' ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    

        <section class="section-card" <?= in_array($tab, ['due_now', 'upcoming', 'overdue']) ? 'style="display: none;"' : '' ?>>
            <div class="section-header">
                <div>
                    <h2>Past payments</h2>
                    <p class="section-sub text-muted">Confirmed payments you have already made.</p>
                </div>
                <span class="section-count"><?= count($filteredPaymentHistory) ?> record(s)</span>
            </div>

            <div class="table-wrap">
                <table class="client-payments-history-table" data-table-collapse="off">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Service</th>
                            <th>Payment Date</th>
                            <th>Amount Paid</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filteredPaymentHistory)): ?>
                            <tr>
                                <td colspan="6">No payment history found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filteredPaymentHistory as $h): ?>
                                <tr>
                                    <td>#<?= (int)$h['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($h['service_type']) ?> (<?= htmlspecialchars($h['basis']) ?>)</td>
                                    <td><?= htmlspecialchars($h['paid_at'] ?? '-') ?></td>
                                    <td>LKR <?= number_format((float)$h['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', (string)($h['payment_method'] ?? '-')))) ?></td>
                                    <td>
                                        <span class="pill <?= statusClass($h['status'] ?? '') ?>">
                                            <?= htmlspecialchars(ucfirst((string)($h['status'] ?? '-'))) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    </main>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>