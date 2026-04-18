<?php
$clientPageTitle = 'Ongoing bookings — SmartCare';
$clientExtraCss  = ['client/c_upcomingBookings.css', 'client/c_ongoingBookings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$ongoingStatusFilterOptions = [
    '' => 'All statuses',
    'Accepted' => 'Accepted',
    'Advance_Paid' => 'Advance paid',
    'Reschedule_Requested' => 'Reschedule requested',
    'Change_Requested' => 'Change requested',
];
?>

<main class="main-content">
    <header class="page-header">
        <div>
            <h1 class="page-title">Ongoing bookings</h1>
            <p class="text-muted">Services active for the current period.</p>
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
        <p class="empty">You do not have any ongoing bookings at the moment.</p>
    <?php else: ?>
        <div class="filter-row bookings-status-filter" role="search" aria-label="Filter by status">
            <div class="filter-group">
                <label for="ongoingBookingsStatusFilter">Filter by status</label>
                <select id="ongoingBookingsStatusFilter" class="bookings-status-filter-select">
                    <?php foreach ($ongoingStatusFilterOptions as $val => $label): ?>
                        <option value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="ongoingBookingsTable" class="client-table bookings-data-table">
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
                        $status = strtolower(trim((string) ($b['status'] ?? '')));
                        $changedOnce = ((int) ($b['caretaker_changed_once'] ?? 0) === 1);
                        $isAccepted = ($status === 'accepted');
                        $isChangeRequested = ($status === 'change_requested');
                        // Only allow contact for Accepted status
                        $canViewContact = ($status === 'accepted');
                        $statusDisplay = str_replace('_', ' ', (string) $b['status']);
                        $rawStatus = (string) $b['status'];
                        $caregiverChangeHint = '';
                        if ($isChangeRequested) {
                            $caregiverChangeHint = 'A caregiver change is already in progress for this booking.';
                        } elseif (!$isAccepted) {
                            $caregiverChangeHint = 'Caregiver change is available only after the booking is Accepted by your caregiver.';
                        }
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
                                    <?php if ($canViewContact): ?>
                                        <a class="btn tiny approve" href="<?= URLROOT ?>/client/c_contactCT?booking_id=<?= $bid ?>">Contact</a>
                                    <?php endif; ?>
                                    <?php if ($isChangeRequested): ?>
                                        <button type="button" class="btn tiny secondary" disabled aria-disabled="true">Change requested</button>
                                    <?php elseif ($isAccepted && $changedOnce): ?>
                                        <button type="button" class="btn tiny secondary" disabled aria-disabled="true">Already changed</button>
                                    <?php elseif ($isAccepted && !$changedOnce): ?>
                                        <button type="button" class="btn tiny secondary" onclick="openChangeModal(<?= $bid ?>)">Change caregiver</button>
                                    <?php else: ?>
                                        <button type="button" class="btn tiny secondary" disabled aria-disabled="true">Change caregiver</button>
                                    <?php endif; ?>
                                    <button type="button" class="btn tiny reject" onclick="openCancelModal(<?= $bid ?>)">Cancel</button>
                                </div>
                                <?php if ($caregiverChangeHint !== ''): ?>
                                    <p class="booking-row-hint" role="note">
                                        <i class="bx bx-info-circle" aria-hidden="true"></i>
                                        <span><?= htmlspecialchars($caregiverChangeHint) ?></span>
                                    </p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="ongoingBookingsEmptyFilter" hidden>
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
                    <p class="booking-detail-footnote text-muted">Contact opens messaging with your assigned caregiver for this period.</p>
                </div>
            </template>
        <?php endforeach; ?>

        <?php require_once APPROOT . '/views/client/partials/booking_detail_modal.php'; ?>
    <?php endif; ?>

    <div id="changeModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="changeModalTitle">
        <div class="modal-content change-caregiver-modal-content">
            <button type="button" class="close" onclick="closeChangeModal()" aria-label="Close">&times;</button>
            <h3 id="changeModalTitle">Request caregiver change</h3>
            <form method="POST" action="<?= URLROOT ?>/client/requestCaretakerChange">
                <input type="hidden" name="booking_id" id="changeBookingId">
                <input type="hidden" name="new_caretaker_id" id="selectedCaretakerId" required>
                <div class="field">
                    <label>Select replacement caregiver</label>
                    <div id="caretakerGrid" class="caretaker-select-grid"></div>
                    <p id="noCaretakersMsg" class="field-error" style="min-height:1.25em;"></p>
                </div>
                <div class="field">
                    <label for="changeReason">Reason for change</label>
                    <textarea id="changeReason" name="reason" rows="3" placeholder="Why would you like a different caregiver?" required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn secondary" onclick="closeChangeModal()">Back</button>
                    <button type="submit" class="btn">Submit request</button>
                </div>
            </form>
        </div>
    </div>

    <div id="cancelModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="ongoingCancelTitle">
        <div class="modal-content">
            <button type="button" class="close" onclick="closeCancelModal()" aria-label="Close">&times;</button>
            <h3 id="ongoingCancelTitle">Cancel booking</h3>
            <?php require_once APPROOT . '/views/client/partials/cancel_fee_notice.php'; ?>
            <form method="POST" action="<?= URLROOT ?>/client/cancelBooking">
                <input type="hidden" name="booking_id" id="cancelBookingId">
                <div class="field">
                    <label for="ongoingCancelReason">Reason for cancellation</label>
                    <textarea id="ongoingCancelReason" name="reason" rows="3" required></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn secondary" onclick="closeCancelModal()">Back</button>
                    <button type="submit" class="btn reject">Confirm cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rescheduleModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="ongoingRescheduleTitle">
        <div class="modal-content">
            <button type="button" class="close" onclick="closeRescheduleModal()" aria-label="Close">&times;</button>
            <h3 id="ongoingRescheduleTitle">Reschedule booking</h3>
            <div class="reschedule-warning">
                <strong>Important</strong>
                <ul>
                    <li>Only the <strong>date</strong> can be changed.</li>
                    <li>Service type, duration, and caregiver stay the same.</li>
                    <li>One reschedule per booking.</li>
                    <li>At least <strong>5 days</strong> in advance.</li>
                </ul>
            </div>
            <form method="POST" action="<?= URLROOT ?>/client/rescheduleBooking">
                <input type="hidden" name="booking_id" id="rescheduleBookingId">
                <div class="field">
                    <label for="ongoingNewDate">New date</label>
                    <input id="ongoingNewDate" type="date" name="new_date" required
                        min="<?= date('Y-m-d', strtotime('+5 days')) ?>">
                </div>
                <div class="field">
                    <label for="ongoingResReason">Reason (optional)</label>
                    <textarea id="ongoingResReason" name="reason" rows="3"></textarea>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn secondary" onclick="closeRescheduleModal()">Back</button>
                    <button type="submit" class="btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    window.CLIENT_UPLOAD_BASE = "<?= URLROOT ?>/public/uploads/";
</script>
<script src="<?= URLROOT ?>/public/js/client/booking-detail-modal.js"></script>
<script src="<?= URLROOT ?>/public/js/client/c_ongoingBookings.js"></script>
<script>
    async function openChangeModal(bookingId) {
        document.getElementById('changeBookingId').value = bookingId;
        const res = await fetch('<?= URLROOT ?>/client/fetchAvailableCaretakers?booking_id=' + encodeURIComponent(bookingId));
        const list = await res.json();
        if (list.error) {
            document.getElementById('noCaretakersMsg').textContent = list.error;
            document.getElementById('caretakerGrid').innerHTML = '';
        } else {
            renderCaretakerCards(list);
        }
        var m = document.getElementById('changeModal');
        if (m) m.classList.add('show');
    }
</script>
<?php if (!empty($data['bookings'])): ?>
    <script>
        (function() {
            var sel = document.getElementById('ongoingBookingsStatusFilter');
            var table = document.getElementById('ongoingBookingsTable');
            var emptyRow = document.getElementById('ongoingBookingsEmptyFilter');
            if (!sel || !table || !emptyRow) {
                return;
            }

            function applyFilter() {
                var v = sel.value;
                var rows = table.querySelectorAll('tbody tr[data-status]');
                var visible = 0;
                rows.forEach(function(tr) {
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