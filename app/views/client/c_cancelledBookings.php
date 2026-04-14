<?php
$clientPageTitle = 'Cancelled bookings — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_upcomingBookings.css', 'client/c_cancelledBookings.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/client/partials/client_booking_status_helper.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$bookings = $data['bookings'] ?? [];
$serviceFilterOptions = ['' => 'All service types'];
foreach ($bookings as $b) {
    $svc = trim((string) ($b['service_type'] ?? ''));
    if ($svc !== '') {
        $serviceFilterOptions[$svc] = $svc;
    }
}
?>

<main class="main-content admin-dashboard-page cancelled-bookings">
    <header class="page-header">
        <div>
            <h1 class="page-title">Cancelled bookings</h1>
            <p class="text-muted">History of cancelled services.</p>
        </div>
    </header>

    <?php if (empty($bookings)): ?>
        <p class="empty">You have no cancelled bookings.</p>
    <?php else: ?>
        <div class="filter-row bookings-service-filter" role="search" aria-label="Filter by service type">
            <div class="filter-group">
                <label for="cancelledBookingsServiceFilter">Filter by service type</label>
                <select id="cancelledBookingsServiceFilter" class="bookings-status-filter-select">
                    <?php foreach ($serviceFilterOptions as $val => $label): ?>
                        <option value="<?= htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="table-container">
            <table id="cancelledBookingsTable" class="client-table bookings-data-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>Caregiver</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Cancelled at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <?php
                        $bid = (int) ($b['booking_id'] ?? 0);
                        $svc = trim((string) ($b['service_type'] ?? ''));
                        ?>
                        <tr data-service-type="<?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= htmlspecialchars((string) $b['caretaker_name']) ?></td>
                            <td><?= htmlspecialchars($svc) ?></td>
                            <td><?= htmlspecialchars(date('Y-m-d', strtotime((string) $b['booking_date']))) ?></td>
                            <td><?= (int) $b['duration'] . ' ' . htmlspecialchars((string) $b['basis']) ?></td>
                            <td><span class="status <?= client_booking_status_class('Cancelled') ?>">Cancelled</span></td>
                            <?php
                            $reasonFull = (string) ($b['cancellation_reason'] ?? '');
                            $reasonPreview = $reasonFull;
                            if (function_exists('mb_strlen') && mb_strlen($reasonFull, 'UTF-8') > 72) {
                                $reasonPreview = mb_substr($reasonFull, 0, 72, 'UTF-8') . '…';
                            } elseif (strlen($reasonFull) > 80) {
                                $reasonPreview = substr($reasonFull, 0, 77) . '…';
                            }
                            ?>
                            <td class="cancelled-reason-cell" title="<?= htmlspecialchars($reasonFull) ?>">
                                <?= htmlspecialchars($reasonPreview) ?>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime((string) ($b['cancelled_at'] ?? '')))) ?></td>
                            <td class="booking-actions-cell">
                                <?php if ($bid > 0): ?>
                                    <div class="booking-actions-toolbar" role="group" aria-label="Actions for cancelled booking <?= $bid ?>">
                                        <button type="button" class="btn btn-icon secondary" title="Cancellation details" aria-label="View cancellation details for booking <?= $bid ?>"
                                                onclick="SmartCareBookingDetail.open(<?= $bid ?>)">
                                            <i class="bx bx-show" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="cancelledBookingsEmptyFilter" hidden>
                        <td colspan="8">No cancelled bookings match this service type.</td>
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
            $street = trim((string) ($b['street'] ?? ''));
            $addr1 = trim((string) ($b['address_line1'] ?? ''));
            $addr2 = trim((string) ($b['address_line2'] ?? ''));
            $postal = trim((string) ($b['postal_code'] ?? ''));
            $addrParts = array_filter([$street, $addr1, $addr2, $district !== '' ? $district : null, $postal !== '' ? $postal : null]);
            $cust = trim((string) ($b['customization'] ?? ''));
            $tp = (float) ($b['total_payment'] ?? 0);
            $cancelReason = trim((string) ($b['cancellation_reason'] ?? ''));
            $cancelledAt = trim((string) ($b['cancelled_at'] ?? ''));
            $refundSt = trim((string) ($b['refund_status'] ?? 'none'));
            ?>
            <template id="booking-detail-template-<?= $bid ?>">
                <div class="booking-detail-panel">
                    <dl class="admin-row-detail-modal__dl">
                        <dt>Booking ID</dt>
                        <dd>#<?= $bid ?></dd>
                        <dt>Caregiver</dt>
                        <dd><?= htmlspecialchars((string) ($b['caretaker_name'] ?? '')) ?></dd>
                        <dt>Service</dt>
                        <dd><?= htmlspecialchars((string) ($b['service_type'] ?? '')) ?></dd>
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
                        <dd><span class="status <?= client_booking_status_class('Cancelled') ?>">Cancelled</span></dd>
                        <dt>Cancellation reason</dt>
                        <dd class="booking-detail-dd--multiline"><?= $cancelReason !== '' ? htmlspecialchars($cancelReason) : '—' ?></dd>
                        <?php if ($cancelledAt !== '' && $cancelledAt !== '0000-00-00 00:00:00'): ?>
                            <dt>Cancelled at</dt>
                            <dd><?= htmlspecialchars($cancelledAt) ?></dd>
                        <?php endif; ?>
                        <?php if ($cust !== ''): ?>
                            <dt>Booking notes</dt>
                            <dd class="booking-detail-dd--multiline"><?= htmlspecialchars($cust) ?></dd>
                        <?php endif; ?>
                        <dt>Total (at cancellation)</dt>
                        <dd class="booking-detail-money">LKR <?= number_format($tp, 2) ?></dd>
                        <?php if ($refundSt !== '' && strtolower($refundSt) !== 'none'): ?>
                            <dt>Refund status</dt>
                            <dd><?= htmlspecialchars(ucfirst($refundSt)) ?></dd>
                        <?php endif; ?>
                    </dl>
                    <p class="booking-detail-footnote text-muted">This booking was cancelled. Contact support if you have questions about refunds or charges.</p>
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
    var sel = document.getElementById('cancelledBookingsServiceFilter');
    var table = document.getElementById('cancelledBookingsTable');
    var emptyRow = document.getElementById('cancelledBookingsEmptyFilter');
    if (!sel || !table || !emptyRow) {
        return;
    }
    function applyFilter() {
        var v = sel.value;
        var rows = table.querySelectorAll('tbody tr[data-service-type]');
        var visible = 0;
        rows.forEach(function (tr) {
            var st = tr.getAttribute('data-service-type') || '';
            var show = !v || st === v;
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
