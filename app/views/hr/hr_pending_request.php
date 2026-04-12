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
        <div class="container">
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
            
            <div class="table-container">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>Client ID</th>
                        <th>Caregiver ID</th>
                        <th>Service</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>Total Amount</th>
                        <th>View</th>
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
                                <td>
                                    <?= $b['caretaker_id'] ?? '—' ?>
                                    <?php if (!empty($b['caretaker_overlap'])): ?>
                                        <span class="overlap-warning" title="This caretaker has overlapping bookings">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="service-badge"><?= htmlspecialchars($b['service_type']) ?></span></td>
                                <td>
                                    <?php 
                                        $basis = $b['basis'] ?? '—';
                                        $basisMap = ['Daily' => 'Day', 'Monthly' => 'Month', 'Hourly' => 'Hour', 'Weekly' => 'Week', 'Yearly' => 'Year'];
                                        $displayBasis = $basisMap[$basis] ?? ucfirst($basis);
                                        echo htmlspecialchars($b['duration'] ?? '—') . ' ' . htmlspecialchars($displayBasis);
                                    ?>
                                </td>
                                <td>
                                    <div class="datetime-info">
                                        <div class="date-val"><?= $b['booking_date'] ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-box">
                                        <span class="currency">Rs</span>
                                        <span class="amount-value"><?= number_format($b['total_payment'] ?? 0, 2) ?></span>
                                    </div>
                                </td>
                                <td class="view-cell">
                                    <button type="button" class="view-btn" onclick="openDetailModal(<?= htmlspecialchars(json_encode($b)) ?>)" title="View full details">
                                        <i class="bx bx-show"></i>
                                    </button>
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
                            <td colspan="8" class="empty-state">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>


            </table>
            </div>
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

    <!-- Confirmation Modal -->
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

    <!-- Detail View Modal -->
    <div id="detailModal" class="modal detail-modal">
        <div class="detail-modal-content">
            <span class="close-detail" onclick="closeDetailModal()">&times;</span>
            <h2>Booking Details</h2>
            
            <div class="detail-grid">
                <!-- Row 1 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Client ID</label>
                        <div class="detail-value" id="detailClientId">—</div>
                    </div>
                    <div class="detail-item">
                        <label>Client Name</label>
                        <div class="detail-value" id="detailClientName">—</div>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Caregiver ID</label>
                        <div class="detail-value" id="detailCaretakerId">—</div>
                    </div>
                    <div class="detail-item">
                        <label>Service Type</label>
                        <div class="detail-value" id="detailService">—</div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Duration</label>
                        <div class="detail-value" id="detailDuration">—</div>
                    </div>
                    <div class="detail-item">
                        <label>Basis</label>
                        <div class="detail-value" id="detailBasis">—</div>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Date & Time</label>
                        <div class="detail-value" id="detailDateTime">—</div>
                    </div>
                    <div class="detail-item">
                        <label>Availability</label>
                        <div class="detail-value" id="detailAvailability">—</div>
                    </div>
                </div>

                <!-- Row 5 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Customization</label>
                        <div class="detail-value" id="detailCustomization">—</div>
                    </div>
                </div>

                <!-- Row 6 -->
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Total Amount</label>
                        <div class="detail-value amount-highlight" id="detailAmount">—</div>
                    </div>
                </div>
            </div>

            <div class="detail-modal-footer">
                <button class="btn-close-detail" onclick="closeDetailModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openDetailModal(bookingData) {
            // Populate modal with booking data
            document.getElementById('detailClientId').textContent = bookingData.booking_id || '—';
            document.getElementById('detailClientName').textContent = bookingData.client_name || '—';
            document.getElementById('detailCaretakerId').textContent = bookingData.caretaker_id || '—';
            document.getElementById('detailService').textContent = bookingData.service_type || '—';
            document.getElementById('detailDuration').textContent = bookingData.duration || '—';
            document.getElementById('detailBasis').textContent = bookingData.basis || '—';
            
            const dateTime = (bookingData.booking_date || '—') + ' ' + (bookingData.preferred_time || '');
            document.getElementById('detailDateTime').textContent = dateTime.trim();
            
            // Availability
            const availabilityText = bookingData.availability_ok ? 'Available' : 'Conflict';
            const availabilityClass = bookingData.availability_ok ? 'availability-ok' : 'availability-conflict';
            document.getElementById('detailAvailability').innerHTML = `<span class="availability-badge ${availabilityClass}">${availabilityText}</span>`;
            
            // Customization
            const customText = (bookingData.customization || '').trim();
            const customHours = parseInt(bookingData.customization_hours) || 0;
            const customPrice = parseFloat(bookingData.customization_price) || 0;
            let customHTML = customText ? `<div class="custom-text">${customText}</div>` : '<span class="no-custom">None</span>';
            if (customHours > 0) {
                customHTML += `<div class="custom-details"><i class="fas fa-clock"></i> ${customHours} hrs <i class="fas fa-money-bill-wave"></i> Rs ${customPrice.toFixed(2)}</div>`;
            }
            document.getElementById('detailCustomization').innerHTML = customHTML;
            
            // Amount
            const amount = parseFloat(bookingData.total_payment) || 0;
            document.getElementById('detailAmount').textContent = 'Rs ' + amount.toFixed(2);
            
            // Show modal
            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close detail modal when clicking outside
        document.getElementById('detailModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeDetailModal();
            }
        });
    </script>

    <script src="<?php echo URLROOT; ?>/public/js/hr/hr_pending_request.js"></script>
</body>

</html>