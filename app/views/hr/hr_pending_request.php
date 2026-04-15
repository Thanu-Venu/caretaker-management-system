<?php
$hrPageTitle = 'Pending service requests — HR';
$hrExtraCss  = ['hr/hr_pending_request.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

/**
 * @return string JSON for data-booking (safe in HTML attribute).
 */
function hr_booking_row_json(array $row): string
{
    return htmlspecialchars(
        json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<main class="main-content">
    <header class="page-header">
        <h1 class="page-title">Pending service requests</h1>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" role="status"><?= htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="container">
        <form method="get" action="<?= URLROOT ?>/hr/hr_pending_request" class="filter-bar">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" class="form-input" onchange="this.form.submit()">
                <?php
                $statuses = ['All', 'Requested', 'Payment_Requested', 'Advance_Paid', 'Accepted', 'Change_Requested', 'Rejected', 'Cancelled', 'Completed', 'Reschedule_Requested'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?>" <?= (($data['status'] ?? 'All') === $s) ? 'selected' : '' ?>>
                        <?= htmlspecialchars(str_replace('_', ' ', $s), ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <div class="table-container">
            <table class="table booking-table requests-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Caregiver ID</th>
                        <th>Service type</th>
                        <th>Duration</th>
                        <th>Total amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['bookings'])): ?>
                        <?php foreach ($data['bookings'] as $b): ?>
                            <?php
                            $rawStatus = (string) ($b['status'] ?? '');
                            $status = $rawStatus !== '' ? $rawStatus : 'Requested';
                            $basis = $b['basis'] ?? '—';
                            $basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
                            $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                            $durationText = htmlspecialchars((string) ($b['duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                            $actionsLocked = ($status !== 'Requested');
                            ?>
                            <tr class="<?= (!empty($b['caretaker_overlap'])) ? 'booking-overlap-alert' : '' ?>">
                                <td><?= (int) ($b['client_id'] ?? 0) ?></td>
                                <td>
                                    <?= (int) ($b['caretaker_id'] ?? 0) ?>
                                    <?php if (!empty($b['caretaker_overlap'])): ?>
                                        <span class="overlap-warning" title="This caregiver has overlapping bookings">
                                            <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="service-badge"><?= htmlspecialchars((string) ($b['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><?= $durationText ?></td>
                                <td>
                                    <div class="amount-box">
                                        <span class="currency">Rs</span>
                                        <span class="amount-value"><?= number_format((float) ($b['total_payment'] ?? 0), 2) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill" data-booking-status="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', $status), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="actions booking-row-actions">
                                    <button type="button"
                                        class="btn secondary btn-sm iconBtn bkView"
                                        data-booking="<?= hr_booking_row_json($b) ?>"
                                        title="View details"
                                        aria-label="View full booking details">
                                        <i class="bx bx-show" aria-hidden="true"></i>
                                    </button>
                                    <form method="post" action="<?= URLROOT ?>/hr/requestAdvancePayment" class="bkForm bkAccept">
                                        <input type="hidden" name="booking_id" value="<?= (int) ($b['booking_id'] ?? $b['id'] ?? 0) ?>">
                                        <input type="hidden" name="client_id" value="<?= (int) ($b['client_id'] ?? 0) ?>">
                                        <input type="hidden" name="return_status" value="<?= htmlspecialchars((string) ($data['status'] ?? 'All'), ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="return_page" value="<?= (int) ($data['page'] ?? 1) ?>">
                                        <button type="submit"
                                            class="btn secondary btn-sm iconBtn"
                                            title="Accept and request advance payment"
                                            aria-label="Accept and request advance payment"
                                            <?= $actionsLocked ? 'disabled' : '' ?>>
                                            <i class="bx bx-check" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                    <form method="post" action="<?= URLROOT ?>/hr/rejectBookingRequest" class="bkForm bkReject">
                                        <input type="hidden" name="booking_id" value="<?= (int) ($b['booking_id'] ?? $b['id'] ?? 0) ?>">
                                        <input type="hidden" name="return_status" value="<?= htmlspecialchars((string) ($data['status'] ?? 'All'), ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="return_page" value="<?= (int) ($data['page'] ?? 1) ?>">
                                        <button type="submit"
                                            class="btn secondary btn-sm iconBtn"
                                            title="Reject request"
                                            aria-label="Reject booking request"
                                            <?= $actionsLocked ? 'disabled' : '' ?>>
                                            <i class="bx bx-x" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($data['totalPages'] ?? 1) > 1): ?>
            <div class="pagination">
                <?php
                $current = (int) ($data['page'] ?? 1);
                $statusEnc = urlencode((string) ($data['status'] ?? 'All'));
                ?>
                <?php if ($current > 1): ?>
                    <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current - 1 ?>&status=<?= $statusEnc ?>">&laquo;</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= (int) $data['totalPages']; $i++): ?>
                    <a class="<?= ($i === $current) ? 'active' : '' ?>"
                        href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $i ?>&status=<?= $statusEnc ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($current < (int) $data['totalPages']): ?>
                    <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current + 1 ?>&status=<?= $statusEnc ?>">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="bookingDetailModal" class="modal readModal" aria-hidden="true">
    <div class="modal-content readPanel readWide" role="dialog" aria-modal="true"
        aria-labelledby="bookingDetailTitle">
        <button type="button" class="modal-close readClose" data-close-booking-modal aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="readHead">
            <span class="readIcon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="bookingDetailTitle" class="readTitle">Booking details</h3>
        </header>
        <dl class="pairList" id="bookingDetailDl"></dl>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_pending_request.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
