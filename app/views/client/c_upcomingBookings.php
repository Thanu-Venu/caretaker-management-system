<?php
$clientPageTitle = 'Upcoming bookings — SmartCare';
$clientExtraCss  = ['client/c_upcomingBookings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$pendingAdvanceList = $data['pendingAdvance'] ?? [];
$hasPendingAdvance  = !empty($pendingAdvanceList);

require_once APPROOT . '/models/RescheduleRequestModel.php';
$rrModelForUpcoming = new RescheduleRequestModel();

$upcomingStatusFilterOptions = [
    '' => 'All statuses',
    'Requested' => 'Requested',
    'Payment_Requested' => 'Payment requested',
    'Advance_Paid' => 'Advance paid',
    'Accepted' => 'Accepted',
    'Reschedule_Requested' => 'Reschedule requested',
    'Change_Requested' => 'Change requested',
];
?>

<?php if ($hasPendingAdvance): ?>
<div id="advanceModal" class="modal show" role="dialog" aria-modal="true" aria-labelledby="advanceModalTitle">
    <div class="modal-content" style="max-width:640px;">
        <button type="button" class="close" onclick="document.getElementById('advanceModal').classList.remove('show')" aria-label="Close">&times;</button>
        <h3 id="advanceModalTitle">Advance payments required</h3>
        <p class="text-muted">You have pending advance payments for the following bookings.</p>
        <div class="advance-modal-list">
            <?php require APPROOT . '/views/client/partials/advance_pending_booking_cards.php'; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Upcoming bookings</h1>
            <p class="text-muted">Bookings with a future start date that are still in progress.</p>
        </div>
        <div class="header-actions">
            <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_myBookings">All bookings</a>
        </div>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="flash success"><?php echo htmlspecialchars((string) $_SESSION['success']);
        unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="flash error"><?php echo htmlspecialchars((string) $_SESSION['error']);
        unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($data['bookings'])): ?>
        <p class="empty">You do not have any upcoming bookings yet.</p>
    <?php else: ?>
        <div class="client-page-note" role="note">
            <i class="bx bx-info-circle" aria-hidden="true"></i>
            <span>Reschedule is only available while the booking is in <strong>Requested</strong> status (before advance payment and confirmation).</span>
        </div>

        <div class="filter-row bookings-status-filter" role="search" aria-label="Filter by status">
            <div class="filter-group">
                <label for="upcomingBookingsStatusFilter">Filter by status</label>
                <select id="upcomingBookingsStatusFilter" class="bookings-status-filter-select">
                    <?php foreach ($upcomingStatusFilterOptions as $val => $label): ?>
                        <option value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="upcomingBookingsTable" class="client-table bookings-data-table">
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
                    <?php foreach ($data['bookings'] as $b): ?>
                        <?php
                        $bid = (int) $b['booking_id'];
                        $canShowReschedule = false;
                        $rescheduleHint = '';
                        if ($b['status'] === 'Requested') {
                            if ($rrModelForUpcoming->hasRescheduleRequest($bid)) {
                                $rescheduleHint = 'A reschedule request has already been submitted for this booking.';
                            } else {
                                $canShowReschedule = true;
                            }
                        }
                        $advancePaidStatuses = ['Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested'];
                        $statusDisplay = str_replace('_', ' ', (string) $b['status']);
                        $rawStatus = (string) $b['status'];
                        ?>
                        <tr data-status="<?= htmlspecialchars($rawStatus, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) $b['caretaker_name']) ?></td>
                            <td><?= htmlspecialchars((string) $b['service_type']) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime((string) $b['booking_date']))) ?></td>
                            <td><?= htmlspecialchars((string) ($b['duration'] . ' ' . $b['basis'])) ?></td>
                            <td>
                                <span class="status <?= client_booking_status_class($b['status'] ?? '') ?>"><?= htmlspecialchars($statusDisplay) ?></span>
                            </td>
                            <td class="booking-actions-cell">
                                <div class="booking-actions-toolbar" role="group" aria-label="Actions for booking <?= $bid ?>">
                                    <button type="button" class="btn btn-icon secondary booking-detail-open" title="View full details" aria-label="View full details for booking <?= $bid ?>"
                                            onclick="SmartCareBookingDetail.open(<?= $bid ?>)">
                                        <i class="bx bx-show" aria-hidden="true"></i>
                                    </button>
                                    <?php if (in_array($b['status'], $advancePaidStatuses, true)): ?>
                                        <a class="btn tiny approve" href="<?= URLROOT ?>/client/c_contactCT?booking_id=<?= $bid ?>">Contact</a>
                                    <?php endif; ?>
                                    <?php if ($b['status'] === 'Payment_Requested'): ?>
                                        <a class="btn tiny" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= $bid ?>">Pay now</a>
                                    <?php endif; ?>
                                    <button type="button" class="btn tiny reject" onclick="openCancelModal(<?= $bid ?>)">Cancel</button>
                                    <?php if ($canShowReschedule): ?>
                                        <button type="button" class="btn tiny secondary" onclick="openRescheduleModal(<?= $bid ?>)">Reschedule</button>
                                    <?php elseif ($b['status'] === 'Requested'): ?>
                                        <button type="button" class="btn tiny secondary" disabled aria-disabled="true">Reschedule</button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($rescheduleHint !== ''): ?>
                                    <p class="booking-row-hint" role="note">
                                        <i class="bx bx-info-circle" aria-hidden="true"></i>
                                        <span><?= htmlspecialchars($rescheduleHint) ?></span>
                                    </p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="upcomingBookingsEmptyFilter" hidden>
                        <td colspan="6">No bookings match this status.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php foreach ($data['bookings'] as $b): ?>
            <?php
            $bid = (int) $b['booking_id'];
            $bkDate = (string) ($b['booking_date'] ?? '');
            $svcStart = isset($b['service_start_date']) ? (string) $b['service_start_date'] : '';
            $showSvcStart = $svcStart !== '' && $svcStart !== '0000-00-00' && $svcStart !== $bkDate;
            $district = trim((string) ($b['district'] ?? ''));
            $cust = trim((string) ($b['customization'] ?? ''));
            $tp = (float) ($b['total_payment'] ?? 0);
            $adv = (float) ($b['advance_amount'] ?? 0);
            $statusDisplay = str_replace('_', ' ', (string) $b['status']);
            ?>
            <template id="booking-detail-template-<?= $bid ?>">
                <div class="booking-detail-panel">
                    <dl class="admin-row-detail-modal__dl">
                        <dt>Booking ID</dt>
                        <dd>#<?= $bid ?></dd>
                        <dt>Caregiver</dt>
                        <dd><?= htmlspecialchars((string) $b['caretaker_name']) ?></dd>
                        <dt>Service</dt>
                        <dd><?= htmlspecialchars((string) $b['service_type']) ?></dd>
                        <?php if ($district !== ''): ?>
                            <dt>Service area</dt>
                            <dd><?= htmlspecialchars($district) ?></dd>
                        <?php endif; ?>
                        <dt>Booking date</dt>
                        <dd><?= htmlspecialchars(date('Y-m-d', strtotime($bkDate))) ?></dd>
                        <?php if ($showSvcStart): ?>
                            <dt>Service start</dt>
                            <dd><?= htmlspecialchars(date('Y-m-d', strtotime($svcStart))) ?></dd>
                        <?php endif; ?>
                        <dt>Preferred time</dt>
                        <dd><?= htmlspecialchars((string) $b['preferred_time']) ?></dd>
                        <dt>Duration</dt>
                        <dd><?= htmlspecialchars((string) ($b['duration'] . ' ' . $b['basis'])) ?></dd>
                        <dt>Status</dt>
                        <dd><span class="status <?= client_booking_status_class($b['status'] ?? '') ?>"><?= htmlspecialchars($statusDisplay) ?></span></dd>
                        <?php if ($cust !== ''): ?>
                            <dt>Notes</dt>
                            <dd class="booking-detail-dd--multiline"><?= htmlspecialchars($cust) ?></dd>
                        <?php endif; ?>
                        <dt>Total payment</dt>
                        <dd class="booking-detail-money">LKR <?= number_format($tp, 2) ?></dd>
                        <?php if ($adv > 0): ?>
                            <dt>Advance recorded</dt>
                            <dd class="booking-detail-money">LKR <?= number_format($adv, 2) ?></dd>
                        <?php endif; ?>
                    </dl>
                    <p class="booking-detail-footnote text-muted">Use Pay now or Payments when your booking requires a payment.</p>
                </div>
            </template>
        <?php endforeach; ?>

        <?php require_once APPROOT . '/views/client/partials/booking_detail_modal.php'; ?>
    <?php endif; ?>

    <div id="cancelModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">
        <div class="modal-content">
            <button type="button" class="close" onclick="closeCancelModal()" aria-label="Close">&times;</button>
            <h3 id="cancelModalTitle">Cancel booking</h3>
            <form method="POST" action="<?= URLROOT ?>/client/cancelBooking">
                <input type="hidden" name="booking_id" id="cancelBookingId">
                <div class="field">
                    <label for="cancelReason">Reason for cancellation</label>
                    <textarea id="cancelReason" name="reason" rows="3" placeholder="Enter reason" required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn secondary" onclick="closeCancelModal()">Back</button>
                    <button type="submit" class="btn reject">Confirm cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rescheduleModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="rescheduleModalTitle">
        <div class="modal-content">
            <button type="button" class="close" onclick="closeRescheduleModal()" aria-label="Close">&times;</button>
            <h3 id="rescheduleModalTitle">Reschedule booking</h3>
            <div class="reschedule-warning">
                <strong>Important</strong>
                <ul>
                    <li>Only the <strong>date</strong> can be changed through reschedule.</li>
                    <li>Service type, duration, and caregiver stay the same.</li>
                    <li>You can only reschedule <strong>once per booking</strong>.</li>
                    <li>Requests must be at least <strong>5 days in advance</strong>.</li>
                    <li>Status must be <strong>Requested</strong> to allow reschedule.</li>
                </ul>
            </div>
            <form method="POST" action="<?= URLROOT ?>/client/rescheduleBooking">
                <input type="hidden" name="booking_id" id="rescheduleBookingId">
                <div class="field">
                    <label for="rescheduleNewDate">New date <span class="required-mark">*</span></label>
                    <input id="rescheduleNewDate" type="date" name="new_date" required
                        min="<?= date('Y-m-d', strtotime('+5 days')) ?>"
                        title="Must be at least 5 days from now">
                </div>
                <div class="field">
                    <label for="rescheduleReason">Reason (optional)</label>
                    <textarea id="rescheduleReason" name="reason" rows="3" placeholder="Optional note for HR"></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn secondary" onclick="closeRescheduleModal()">Back</button>
                    <button type="submit" class="btn">Submit request</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="<?= URLROOT ?>/public/js/client/booking-detail-modal.js"></script>
<script src="<?= URLROOT ?>/public/js/client/c_upcomingBookings.js"></script>
<?php if (!empty($data['bookings'])): ?>
<script>
(function () {
    var sel = document.getElementById('upcomingBookingsStatusFilter');
    var table = document.getElementById('upcomingBookingsTable');
    var emptyRow = document.getElementById('upcomingBookingsEmptyFilter');
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
