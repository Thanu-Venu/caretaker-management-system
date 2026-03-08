<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Payments</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_pendingPayments.css">
</head>

<body>

    <div class="container">
        <h1>Payment Management</h1>

        <?php if (!empty($data['payments'])): ?>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Client Name</th>
                        <th>Caretaker</th>
                        <th>Service Type</th>
                        <th>Booking Date</th>
                        <th>Full Payment</th>
                        <th>Advance Payment</th>
                        <th>Remaining Balance</th>
                        <th>Payment Method</th>
                        <th>Booking Status</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['payments'] as $payment): ?>
                        <tr>
                            <td>#<?= $payment['booking_id'] ?></td>
                            <td><?= htmlspecialchars($payment['client_name']) ?></td>
                            <td><?= htmlspecialchars($payment['caretaker_name']) ?></td>
                            <td><?= htmlspecialchars($payment['service_type']) ?></td>
                            <td><?= $payment['booking_date'] ?></td>
                            <td>Rs. <?= number_format($payment['total_booking_amount'], 2) ?></td>
                            <td>Rs. <?= number_format($payment['amount'], 2) ?></td>
                            <td>Rs. <?= number_format($payment['remaining_balance'], 2) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $payment['payment_method'])) ?></td>
                            <td>
                                <?php
                                $bookingStatusRaw = (string)($payment['booking_status'] ?? 'Unknown');
                                $bookingStatusClass = strtolower(str_replace(' ', '_', $bookingStatusRaw));
                                ?>
                                <span class="booking-status-badge booking-status-<?= htmlspecialchars($bookingStatusClass) ?>">
                                    <?= htmlspecialchars(str_replace('_', ' ', $bookingStatusRaw)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower($payment['status']) ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($payment['status'] === 'pending'): ?>
                                    <form method="post" action="<?= URLROOT ?>/hr/approvePayment" class="confirm-action-form" data-action-label="approve" style="display: inline;">
                                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                    <form method="post" action="<?= URLROOT ?>/hr/rejectPayment" class="confirm-action-form" data-action-label="reject" data-requires-reason="1" style="display: inline;">
                                        <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                        <input type="text" name="reason" placeholder="Reason" style="width: 100px; padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <span class="no-action">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No payments at this time.</p>
        <?php endif; ?>
    </div>

    <div id="actionConfirmModal" class="confirm-modal" aria-hidden="true">
        <div class="confirm-modal-content" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
            <h3 id="confirmModalTitle">Confirm Action</h3>
            <p id="confirmModalText">Are you sure you want to continue?</p>
            <div class="confirm-modal-actions">
                <button type="button" id="confirmModalCancel" class="btn btn-secondary">Cancel</button>
                <button type="button" id="confirmModalProceed" class="btn btn-danger">Yes, Continue</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('actionConfirmModal');
            const modalText = document.getElementById('confirmModalText');
            const proceedBtn = document.getElementById('confirmModalProceed');
            const cancelBtn = document.getElementById('confirmModalCancel');
            const forms = document.querySelectorAll('.confirm-action-form');
            let targetForm = null;

            function openModal(message, actionLabel) {
                modalText.textContent = message;
                proceedBtn.textContent = actionLabel === 'approve' ? 'Yes, Approve' : 'Yes, Reject';
                proceedBtn.classList.toggle('btn-success', actionLabel === 'approve');
                proceedBtn.classList.toggle('btn-danger', actionLabel !== 'approve');
                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeModal() {
                modal.classList.remove('show');
                modal.setAttribute('aria-hidden', 'true');
                targetForm = null;
            }

            forms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const actionLabel = (form.dataset.actionLabel || '').toLowerCase();
                    const requiresReason = form.dataset.requiresReason === '1';

                    if (requiresReason) {
                        const reasonInput = form.querySelector('input[name="reason"]');
                        if (reasonInput && !reasonInput.value.trim()) {
                            alert('Please enter a reason before rejecting the payment.');
                            reasonInput.focus();
                            return;
                        }
                    }

                    targetForm = form;

                    const message = actionLabel === 'approve' ?
                        'Are you sure you want to approve this payment?' :
                        'Are you sure you want to reject this payment?';

                    openModal(message, actionLabel);
                });
            });

            proceedBtn.addEventListener('click', function() {
                if (targetForm) {
                    const formToSubmit = targetForm;
                    closeModal();
                    formToSubmit.submit();
                }
            });

            cancelBtn.addEventListener('click', closeModal);

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('show')) {
                    closeModal();
                }
            });
        });
    </script>

</body>

</html>