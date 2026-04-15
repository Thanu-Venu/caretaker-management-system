<?php
$hrPageTitle = 'Payment Management — HR';
$hrExtraCss  = ['hr/hr_pendingPayments.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

/**
 * @return string JSON for data-payment (safe in HTML attribute).
 */
function hr_payment_row_json(array $row): string
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
        <h1 class="page-title">Payment Management</h1>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message" role="status"><?= htmlspecialchars((string) $_SESSION['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div id="hr-payments-config"
        data-approve-url="<?= htmlspecialchars(URLROOT . '/hr/approvePayment', ENT_QUOTES, 'UTF-8') ?>"
        data-reject-url="<?= htmlspecialchars(URLROOT . '/hr/rejectPayment', ENT_QUOTES, 'UTF-8') ?>"
        hidden></div>

    <?php if (!empty($data['payments'])): ?>
        <div class="table-container">
            <table class="table booking-table payments-table" data-table-collapse="off">
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
                    <?php foreach ($data['payments'] as $payment): ?>
                        <?php
                        $payStatus = strtolower((string) ($payment['status'] ?? 'pending'));
                        $basis = $payment['basis'] ?? '—';
                        $basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
                        $displayBasis = $basisMap[$basis] ?? ucfirst((string) $basis);
                        $durationText = htmlspecialchars((string) ($payment['duration'] ?? '—'), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($displayBasis, ENT_QUOTES, 'UTF-8');
                        $actionsLocked = ($payStatus !== 'pending');
                        $statusLabel = ucfirst($payStatus);
                        ?>
                        <tr>
                            <td><?= (int) ($payment['client_id'] ?? 0) ?></td>
                            <td><?= (int) ($payment['caretaker_id'] ?? 0) ?></td>
                            <td><?= htmlspecialchars((string) ($payment['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $durationText ?></td>
                            <td>
                                <span class="amount-cell">Rs <?= number_format((float) ($payment['total_booking_amount'] ?? 0), 2) ?></span>
                            </td>
                            <td>
                                <span class="status-pill" data-payment-status="<?= htmlspecialchars($payStatus, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="actions payActions">
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn payView"
                                    data-payment="<?= hr_payment_row_json($payment) ?>"
                                    title="View details"
                                    aria-label="View payment and booking details">
                                    <i class="bx bx-show" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn payOK"
                                    data-payment-id="<?= (int) ($payment['id'] ?? 0) ?>"
                                    title="Approve payment"
                                    aria-label="Approve payment"
                                    <?= $actionsLocked ? 'disabled' : '' ?>>
                                    <i class="bx bx-check" aria-hidden="true"></i>
                                </button>
                                <button type="button"
                                    class="btn secondary btn-sm iconBtn payNo"
                                    data-payment-id="<?= (int) ($payment['id'] ?? 0) ?>"
                                    title="Reject payment"
                                    aria-label="Reject payment"
                                    <?= $actionsLocked ? 'disabled' : '' ?>>
                                    <i class="bx bx-x" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">No payments at this time.</p>
    <?php endif; ?>
</main>

<!-- Detail (HR row modal) -->
<div id="paymentDetailModal" class="modal readModal" aria-hidden="true">
    <div class="modal-content readPanel readWide" role="dialog" aria-modal="true"
        aria-labelledby="paymentDetailTitle">
        <button type="button" class="modal-close readClose" data-close-payment-detail aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="readHead">
            <span class="readIcon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="paymentDetailTitle" class="readTitle">Payment details</h3>
        </header>
        <dl class="pairList" id="paymentDetailDl"></dl>
    </div>
</div>

<!-- Approve / reject confirm -->
<div id="paymentConfirmModal" class="modal" aria-hidden="true">
    <div class="modal-content confirmBox" role="dialog" aria-modal="true" aria-labelledby="paymentConfirmTitle">
        <h3 id="paymentConfirmTitle">Confirm</h3>
        <p id="paymentConfirmText"></p>
        <textarea id="paymentRejectReason" class="rejectNote" rows="3"
            placeholder="Rejection reason (required)" style="display: none;"></textarea>
        <div class="modal-buttons">
            <button type="button" class="btn ghost" id="paymentConfirmCancel">Cancel</button>
            <button type="button" class="btn primary" id="paymentConfirmSubmit">Confirm</button>
        </div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/hr/hr_pendingPayments.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
