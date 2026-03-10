<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_pending_request.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="main-content">
        <h1>Pending Service Requests</h1>
        <div class="table-container">
            <form method="get" action="<?= URLROOT ?>/hr/hr_pending_request" class="filter-bar">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <?php

                    $statuses = ['All', 'Requested', 'Payment_Requested', 'Advance_Paid', 'Accepted', 'Change_Requested', 'Rejected', 'Cancelled', 'Completed', 'Reschedule_Requested'];
                    foreach ($statuses as $s):
                    ?>
                        <option value="<?= $s ?>" <?= ($data['status'] === $s) ? 'selected' : '' ?>>
                            <?= str_replace('_', ' ', $s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Client</th>
                        <th>Caretaker ID</th>
                        <th>Service</th>
                        <th>Duration & Basis</th>
                        <th>Date & Time</th>
                        <th>Customization</th>
                        <th>Total Amount</th>
                        <th>Availability Check</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['bookings'])): ?>
                        <?php foreach ($data['bookings'] as $b): ?>
                            <tr class="<?= (!empty($b['caretaker_overlap'])) ? 'booking-overlap-alert' : '' ?>">
                                <?php

                                $rawStatus = $b['status'] ?? '';
                                $status = !empty($rawStatus) ? $rawStatus : 'Requested';
                                ?>
                                <td><?= $b['booking_id'] ?></td>
                                <td><?= htmlspecialchars($b['client_name']) ?></td>
                                <td>
                                    <span class="caretaker-id-badge">
                                        <?= $b['caretaker_id'] ?? '—' ?>
                                    </span>
                                    <?php if (!empty($b['caretaker_overlap'])): ?>
                                        <span class="overlap-warning" title="This caretaker has overlapping bookings">
                                            <i class="fas fa-exclamation-triangle"></i> Overlap
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="service-badge"><?= htmlspecialchars($b['service_type']) ?></span></td>
                                <td>
                                    <div class="duration-info">
                                        <strong><?= $b['duration'] ?? '—' ?></strong>
                                        <span class="basis-tag"><?= $b['basis'] ?? '—' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="datetime-info">
                                        <div class="date-val"><?= $b['booking_date'] ?></div>
                                        <div class="time-val"><?= $b['preferred_time'] ?></div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $customText = trim($b['customization'] ?? '');
                                    $customHours = (int) ($b['customization_hours'] ?? 0);
                                    $customPrice = (float) ($b['customization_price'] ?? 0);
                                    ?>
                                    <div class="customization-info">
                                        <?php if ($customText !== ''): ?>
                                            <div class="custom-text"><?= htmlspecialchars($customText) ?></div>
                                        <?php else: ?>
                                            <span class="no-custom">None</span>
                                        <?php endif; ?>
                                        <?php if ($customHours > 0): ?>
                                            <div class="custom-details">
                                                <i class="fas fa-clock"></i> <?= $customHours ?> hrs
                                                <i class="fas fa-money-bill-wave"></i> Rs <?= number_format($customPrice, 2) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-box">
                                        <span class="currency">Rs</span>
                                        <span class="amount-value"><?= number_format($b['total_payment'] ?? 0, 2) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($b['availability_ok'])): ?>
                                        <span class="availability-badge availability-ok">Available</span>
                                    <?php else: ?>
                                        <?php $conflict = $b['availability_conflict'] ?? null; ?>
                                        <span
                                            class="availability-badge availability-conflict"
                                            title="<?= !empty($conflict) ? htmlspecialchars('Conflicts with Booking #' . ($conflict['conflict_booking_id'] ?? 'N/A') . ' (' . ($conflict['start_date'] ?? 'N/A') . ' to ' . ($conflict['end_date'] ?? 'N/A') . ', status: ' . ($conflict['status'] ?? 'N/A') . ')') : 'Caregiver conflict detected' ?>">
                                            Conflict
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-<?= strtolower($status) ?>"><?= $status ?></span>
                                </td>
                                <td>
                                    <?php if (empty($rawStatus)): ?>
                                        <span class="badge badge-warning">Requested</span>
                                    <?php elseif ($status === 'Requested'): ?>
                                        <form method="post" action="<?= URLROOT ?>/hr/requestAdvancePayment"
                                            class="advance-payment-form">
                                            <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                            <input type="hidden" name="client_id" value="<?= $b['client_id'] ?>">
                                            <button type="submit" class="btn btn-primary">
                                                Request Advance Payment
                                            </button>
                                        </form>

                                    <?php elseif ($status === 'Payment_Requested'): ?>
                                        <span class="badge badge-warning">Waiting for payment</span>
                                    <?php else: ?>
                                        <span class="status-<?= strtolower($status) ?>"><?= $status ?></span>
                                    <?php endif; ?>

                                </td>


                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="empty-state">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>


            </table>
            <?php if ($data['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php

                    $current = $data['page'];
                    $status = urlencode($data['status']);
                    ?>

                    <?php if ($current > 1): ?>
                        <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current - 1 ?>&status=<?= $status ?>">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                        <a class="<?= ($i === $current) ? 'active' : '' ?>"
                            href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $i ?>&status=<?= $status ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($current < $data['totalPages']): ?>
                        <a href="<?= URLROOT ?>/hr/hr_pending_request?page=<?= $current + 1 ?>&status=<?= $status ?>">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


        </div>
    </div>

    <!-- Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalText">Are you sure you want to request advance payment?</p>
            <div class="modal-actions">
                <button id="confirmYes" class="approve">Yes</button>
                <button id="confirmNo" class="reject">No</button>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/public/js/hr/hr_pending_request.js"></script>
</body>

</html>