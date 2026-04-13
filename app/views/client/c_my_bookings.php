<?php
$clientPageTitle = 'My bookings — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_upcomingBookings.css', 'client/c_my_bookings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

if (!function_exists('client_booking_row_show_upcoming_link')) {
    /**
     * Same idea as upcoming list: future service date and a status that can still be managed there.
     */
    function client_booking_row_show_upcoming_link(array $b): bool
    {
        $st = (string) ($b['status'] ?? '');
        $upcomingStatuses = ['Requested', 'Payment_Requested', 'Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested'];
        if (!in_array($st, $upcomingStatuses, true)) {
            return false;
        }
        $svcStart = isset($b['service_start_date']) ? trim((string) $b['service_start_date']) : '';
        $bk = trim((string) ($b['booking_date'] ?? ''));
        $ref = ($svcStart !== '' && $svcStart !== '0000-00-00') ? $svcStart : $bk;
        if ($ref === '' || $ref === '0000-00-00') {
            return false;
        }
        $ts = strtotime($ref);

        return $ts !== false && $ts > strtotime('today');
    }
}

$bookings = $data['bookings'] ?? [];
$pendingAdvanceList = $data['pendingAdvance'] ?? [];
$hasPendingAdvance  = !empty($pendingAdvanceList);

$myBookingsStatusFilterOptions = [
    '' => 'All statuses',
    'Requested' => 'Requested',
    'Payment_Requested' => 'Payment requested',
    'Advance_Paid' => 'Advance paid',
    'Accepted' => 'Accepted',
    'Change_Requested' => 'Change requested',
    'Rejected' => 'Rejected',
    'Cancelled' => 'Cancelled',
    'Completed' => 'Completed',
    'Reschedule_Requested' => 'Reschedule requested',
];
?>

<?php if ($hasPendingAdvance): ?>
<div id="advanceModal" class="modal show" role="dialog" aria-modal="true" aria-labelledby="advanceModalTitle">
    <div class="modal-content" style="max-width:640px;">
        <button type="button" class="close" onclick="document.getElementById('advanceModal').classList.remove('show')" aria-label="Close">&times;</button>
        <h3 id="advanceModalTitle">Advance payments required</h3>
        <p class="text-muted">Complete advance payment to keep these bookings active.</p>
        <div class="advance-modal-list">
            <?php require APPROOT . '/views/client/partials/advance_pending_booking_cards.php'; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="main-content admin-dashboard-page">
    <header class="page-header">
        <div>
            <h1 class="page-title">My bookings</h1>
            <p class="text-muted">All bookings in one place, newest first.</p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Book a service</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="flash success"><?= htmlspecialchars((string) $_SESSION['success']);
        unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="flash error"><?= htmlspecialchars((string) $_SESSION['error']);
        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <p class="empty">You do not have any bookings yet.</p>
    <?php else: ?>
        <div class="filter-row my-bookings-filter" role="search" aria-label="Filter by status">
            <div class="filter-group">
                <label for="myBookingsStatusFilter">Filter by status</label>
                <select id="myBookingsStatusFilter" class="my-bookings-status-select">
                    <?php foreach ($myBookingsStatusFilterOptions as $val => $label): ?>
                        <option value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="myBookingsTable" class="client-table my-bookings-table">
                <thead>
                    <tr>
                        <th>Caregiver</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <?php
                        $bid = (int) ($b['booking_id'] ?? 0);
                        $rawStatus = (string) ($b['status'] ?? '');
                        $statusDisplay = str_replace('_', ' ', $rawStatus);
                        $canPayAdvance = ($rawStatus === 'Payment_Requested');
                        $showUpcomingShortcut = client_booking_row_show_upcoming_link($b);
                        ?>
                        <tr data-status="<?= htmlspecialchars($rawStatus, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) ($b['caretaker_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($b['service_type'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($b['booking_date'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) (($b['duration'] ?? '') . ' ' . ($b['basis'] ?? ''))) ?></td>
                            <td><span class="status <?= client_booking_status_class($rawStatus) ?>"><?= htmlspecialchars($statusDisplay) ?></span></td>
                            <td class="booking-actions-cell my-bookings-actions">
                                <?php if ($bid > 0): ?>
                                    <div class="booking-actions-toolbar" role="group" aria-label="Actions for booking <?= $bid ?>">
                                        <button type="button" class="btn btn-icon secondary" title="View details" aria-label="View details for booking <?= $bid ?>"
                                                onclick="SmartCareBookingDetail.open(<?= $bid ?>)">
                                            <i class="bx bx-show" aria-hidden="true"></i>
                                        </button>
                                        <?php if ($canPayAdvance): ?>
                                            <a class="btn tiny" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= $bid ?>">Pay advance</a>
                                        <?php endif; ?>
                                        <?php if ($showUpcomingShortcut): ?>
                                            <a class="btn btn-icon secondary" href="<?= URLROOT ?>/client/c_upcomingBookings" title="Open upcoming bookings" aria-label="Open upcoming bookings">
                                                <i class="bx bx-calendar" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="myBookingsEmptyFilter" hidden>
                        <td colspan="6">No bookings match this status.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php foreach ($bookings as $b): ?>
            <?php
            $bid = (int) ($b['booking_id'] ?? 0);
            if ($bid <= 0) {
                continue;
            }
            $bkDate = (string) ($b['booking_date'] ?? '');
            $svcStart = isset($b['service_start_date']) ? (string) $b['service_start_date'] : '';
            $showSvcStart = $svcStart !== '' && $svcStart !== '0000-00-00' && $svcStart !== $bkDate;
            $district = trim((string) ($b['district'] ?? ''));
            $cust = trim((string) ($b['customization'] ?? ''));
            $tp = (float) ($b['total_payment'] ?? 0);
            $adv = (float) ($b['advance_amount'] ?? 0);
            $advBal = (float) ($b['advance_balance'] ?? 0);
            $custHours = (int) ($b['customization_hours'] ?? 0);
            $custPrice = (float) ($b['customization_price'] ?? 0);
            $statusDisplay = str_replace('_', ' ', (string) $b['status']);
            $isCancelled = strcasecmp((string) ($b['status'] ?? ''), 'Cancelled') === 0;
            $cancelReason = trim((string) ($b['cancellation_reason'] ?? ''));
            $cancelledAt = trim((string) ($b['cancelled_at'] ?? ''));
            $svcLoc = trim((string) ($b['service_location'] ?? ''));
            $street = trim((string) ($b['street'] ?? ''));
            $addr1 = trim((string) ($b['address_line1'] ?? ''));
            $addr2 = trim((string) ($b['address_line2'] ?? ''));
            $postal = trim((string) ($b['postal_code'] ?? ''));
            $addrParts = array_filter([$street, $addr1, $addr2, $district !== '' ? $district : null, $postal !== '' ? $postal : null]);
            $createdAt = trim((string) ($b['created_at'] ?? ''));
            $advPaidAt = trim((string) ($b['advance_paid_date'] ?? ''));
            $advMonths = (int) ($b['advance_months'] ?? 0);
            $totMonths = (int) ($b['total_months'] ?? 0);
            $ctChanged = !empty($b['caretaker_changed_once']);
            $refundSt = trim((string) ($b['refund_status'] ?? 'none'));
            $svcDaysUsed = (int) ($b['service_days_used'] ?? 0);
            $ctEmail = trim((string) ($b['caretaker_email'] ?? ''));
            $ctPhone = trim((string) ($b['caretaker_phone'] ?? ''));
            $bookingStatusRaw = (string) ($b['status'] ?? '');
            $caregiverContactHiddenStages = ['Requested', 'Payment_Requested', 'Rejected'];
            $showCaregiverContact = !in_array($bookingStatusRaw, $caregiverContactHiddenStages, true);
            ?>
            <template id="booking-detail-template-<?= $bid ?>">
                <div class="booking-detail-panel">
                    <dl class="admin-row-detail-modal__dl">
                        <dt>Booking ID</dt>
                        <dd>#<?= $bid ?></dd>
                        <dt>Caregiver</dt>
                        <dd><?= htmlspecialchars((string) ($b['caretaker_name'] ?? '')) ?></dd>
                        <?php if ($showCaregiverContact && ($ctPhone !== '' || $ctEmail !== '')): ?>
                            <dt>Caregiver contact</dt>
                            <dd class="booking-detail-dd--multiline"><?php
                            $lines = [];
                            if ($ctPhone !== '') {
                                $lines[] = 'Phone: ' . $ctPhone;
                            }
                            if ($ctEmail !== '') {
                                $lines[] = 'Email: ' . $ctEmail;
                            }
                            echo htmlspecialchars(implode("\n", $lines));
                            ?></dd>
                        <?php endif; ?>
                        <dt>Service</dt>
                        <dd><?= htmlspecialchars((string) ($b['service_type'] ?? '')) ?></dd>
                        <?php if ($svcLoc !== ''): ?>
                            <dt>Named location</dt>
                            <dd><?= htmlspecialchars($svcLoc) ?></dd>
                        <?php endif; ?>
                        <?php if (!empty($addrParts)): ?>
                            <dt>Service address</dt>
                            <dd class="booking-detail-dd--multiline"><?= htmlspecialchars(implode("\n", $addrParts)) ?></dd>
                        <?php elseif ($district !== ''): ?>
                            <dt>Service area</dt>
                            <dd><?= htmlspecialchars($district) ?></dd>
                        <?php endif; ?>
                        <dt>Booking date</dt>
                        <dd><?= $bkDate !== '' ? htmlspecialchars(date('Y-m-d', strtotime($bkDate))) : '—' ?></dd>
                        <?php if ($showSvcStart): ?>
                            <dt>Service start</dt>
                            <dd><?= htmlspecialchars(date('Y-m-d', strtotime($svcStart))) ?></dd>
                        <?php endif; ?>
                        <dt>Preferred time</dt>
                        <dd><?= htmlspecialchars((string) ($b['preferred_time'] ?? '')) ?></dd>
                        <dt>Duration</dt>
                        <dd><?= htmlspecialchars((string) (($b['duration'] ?? '') . ' ' . ($b['basis'] ?? ''))) ?></dd>
                        <dt>Status</dt>
                        <dd><span class="status <?= client_booking_status_class($b['status'] ?? '') ?>"><?= htmlspecialchars($statusDisplay) ?></span></dd>
                        <?php if ($createdAt !== '' && $createdAt !== '0000-00-00 00:00:00'): ?>
                            <dt>Booked on</dt>
                            <dd><?= htmlspecialchars(date('Y-m-d H:i', strtotime($createdAt))) ?></dd>
                        <?php endif; ?>
                        <?php if ($isCancelled && $cancelReason !== ''): ?>
                            <dt>Cancellation reason</dt>
                            <dd class="booking-detail-dd--multiline"><?= htmlspecialchars($cancelReason) ?></dd>
                        <?php endif; ?>
                        <?php if ($isCancelled && $cancelledAt !== '' && $cancelledAt !== '0000-00-00 00:00:00'): ?>
                            <dt>Cancelled at</dt>
                            <dd><?= htmlspecialchars($cancelledAt) ?></dd>
                        <?php endif; ?>
                        <?php if ($cust !== ''): ?>
                            <dt>Notes</dt>
                            <dd class="booking-detail-dd--multiline"><?= htmlspecialchars($cust) ?></dd>
                        <?php endif; ?>
                        <?php if ($custHours > 0 || $custPrice > 0): ?>
                            <dt>Customization</dt>
                            <dd><?php
                            $custBits = [];
                            if ($custHours > 0) {
                                $custBits[] = (int) $custHours . ' extra hours';
                            }
                            if ($custPrice > 0) {
                                $custBits[] = 'LKR ' . number_format($custPrice, 2);
                            }
                            echo htmlspecialchars(implode(' · ', $custBits));
                            ?></dd>
                        <?php endif; ?>
                        <dt>Total payment</dt>
                        <dd class="booking-detail-money">LKR <?= number_format($tp, 2) ?></dd>
                        <?php if ($adv > 0): ?>
                            <dt>Advance amount</dt>
                            <dd class="booking-detail-money">LKR <?= number_format($adv, 2) ?></dd>
                        <?php endif; ?>
                        <?php if ($advPaidAt !== '' && $advPaidAt !== '0000-00-00 00:00:00'): ?>
                            <dt>Advance paid at</dt>
                            <dd><?= htmlspecialchars(date('Y-m-d H:i', strtotime($advPaidAt))) ?></dd>
                        <?php endif; ?>
                        <?php if ($advBal > 0): ?>
                            <dt>Advance balance</dt>
                            <dd class="booking-detail-money">LKR <?= number_format($advBal, 2) ?></dd>
                        <?php endif; ?>
                        <?php if ($advMonths > 0 || $totMonths > 0): ?>
                            <dt>Billing months</dt>
                            <dd><?php
                            $moBits = [];
                            if ($advMonths > 0) {
                                $moBits[] = 'Advance covers: ' . (int) $advMonths . ' mo';
                            }
                            if ($totMonths > 0) {
                                $moBits[] = 'Total plan: ' . (int) $totMonths . ' mo';
                            }
                            echo htmlspecialchars(implode(' · ', $moBits));
                            ?></dd>
                        <?php endif; ?>
                        <?php if ($svcDaysUsed > 0): ?>
                            <dt>Service days used</dt>
                            <dd><?= (int) $svcDaysUsed ?></dd>
                        <?php endif; ?>
                        <?php if ($refundSt !== '' && strtolower($refundSt) !== 'none'): ?>
                            <dt>Refund status</dt>
                            <dd><?= htmlspecialchars(ucfirst($refundSt)) ?></dd>
                        <?php endif; ?>
                        <?php if ($ctChanged): ?>
                            <dt>Caregiver change</dt>
                            <dd>A caregiver change has been recorded for this booking.</dd>
                        <?php endif; ?>
                    </dl>
                    <p class="booking-detail-footnote text-muted">
                        <?php if (($b['status'] ?? '') === 'Payment_Requested'): ?>
                            You can pay the advance while this booking is in <strong>Payment requested</strong> status.
                        <?php else: ?>
                            Advance payment is only available when the status is <strong>Payment requested</strong>.
                        <?php endif; ?>
                    </p>
                </div>
            </template>
        <?php endforeach; ?>

        <?php require_once APPROOT . '/views/client/partials/booking_detail_modal.php'; ?>
    <?php endif; ?>
</main>

<script src="<?= URLROOT ?>/public/js/client/booking-detail-modal.js"></script>
<?php if (!empty($bookings)): ?>
<script>
(function () {
    var sel = document.getElementById('myBookingsStatusFilter');
    var table = document.getElementById('myBookingsTable');
    var emptyRow = document.getElementById('myBookingsEmptyFilter');
    if (!sel || !table || !emptyRow) {
        return;
    }
    function applyFilter() {
        var v = sel.value;
        var rows = table.querySelectorAll('tbody tr[data-status]');
        var visible = 0;
        rows.forEach(function (tr) {
            var show = !v || tr.getAttribute('data-status') === v;
            tr.hidden = !show;
            if (show) {
                visible += 1;
            }
        });
        emptyRow.hidden = visible > 0;
    }
    sel.addEventListener('change', applyFilter);
})();
</script>
<?php endif; ?>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
