<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reschedule Requests</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_rescheduleRequests.css">
</head>

<body>
    <main class="content">
        <h1>Booking Reschedule Requests</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="success-msg"><?php echo $_SESSION['success'];
                                    unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-button active" onclick="switchTab('pending')">
                Pending Requests
                <span>
                    <?= is_array($data['pending_requests']) ? count($data['pending_requests']) : 0 ?>
                </span>
            </button>
            <button class="tab-button" onclick="switchTab('completed')">
                Request History
                <span>
                    <?= is_array($data['completed_requests']) ? count($data['completed_requests']) : 0 ?>
                </span>
            </button>
        </div>

        <!-- PENDING TAB -->
        <div id="pending" class="tab-content active">
            <?php if (empty($data['pending_requests'])): ?>
                <div class="empty-state">
                    <p>No pending reschedule requests.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Booking ID</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Old Date</th>
                                <th>New Date</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['pending_requests'] as $req): ?>
                                <tr>
                                    <td><?= htmlspecialchars($req['request_id']) ?></td>
                                    <td><?= htmlspecialchars($req['booking_id']) ?></td>
                                    <td><?= htmlspecialchars($req['client_name']) ?></td>
                                    <td><?= htmlspecialchars($req['service_type']) ?></td>
                                    <td><?= date('Y-m-d', strtotime($req['old_date'])) ?></td>
                                    <td><?= date('Y-m-d', strtotime($req['new_date'])) ?></td>
                                    <td><?= htmlspecialchars($req['reason']) ?></td>
                                    <td>
                                        <button class="approve-btn" onclick="openModal('approve', <?= $req['request_id'] ?>)">
                                            Approve
                                        </button>

                                        <button class="reject-btn" onclick="openModal('reject', <?= $req['request_id'] ?>)">
                                            Reject
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- COMPLETED TAB -->
        <div id="completed" class="tab-content">
            <?php if (empty($data['completed_requests'])): ?>
                <div class="empty-state">
                    <p>No completed reschedule requests yet.</p>
                </div>
            <?php else: ?>
                <div>
                    <?php foreach ($data['completed_requests'] as $req): ?>
                        <div class="history-row">
                            <div class="history-row-header">
                                <div>
                                    <strong>Booking #<?= htmlspecialchars($req['booking_id']) ?> - <?= htmlspecialchars($req['client_name']) ?></strong>
                                </div>
                                <div>
                                    <span class="status-badge status-<?= strtolower($req['status']) ?>">
                                        <?= ucfirst($req['status']) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="history-row-content">
                                <div class="history-field">
                                    <span class="history-field-label">Old Date</span>
                                    <span class="history-field-value"><?= date('Y-m-d', strtotime($req['old_date'])) ?></span>
                                </div>
                                <div class="history-field">
                                    <span class="history-field-label">New Date</span>
                                    <span class="history-field-value"><?= date('Y-m-d', strtotime($req['new_date'])) ?></span>
                                </div>
                                <div class="history-field">
                                    <span class="history-field-label">Service</span>
                                    <span class="history-field-value"><?= htmlspecialchars($req['service_type']) ?></span>
                                </div>
                                <div class="history-field">
                                    <span class="history-field-label">Caregiver</span>
                                    <span class="history-field-value"><?= htmlspecialchars($req['caretaker_name']) ?></span>
                                </div>
                                <div class="history-field">
                                    <span class="history-field-label">Request Date</span>
                                    <span class="history-field-value"><?= date('Y-m-d H:i', strtotime($req['created_at'])) ?></span>
                                </div>
                                <div class="history-field">
                                    <span class="history-field-label">Decision Date</span>
                                    <span class="history-field-value"><?= $req['reviewed_at'] ? date('Y-m-d H:i', strtotime($req['reviewed_at'])) : 'N/A' ?></span>
                                </div>
                            </div>

                            <div>
                                <strong class="reason-title">Reason for Reschedule:</strong>
                                <p class="reason-text">
                                    <?= htmlspecialchars($req['reason']) ?>
                                </p>
                            </div>

                            <?php if (!empty($req['hr_note'])): ?>
                                <div class="hr-note-box">
                                    <div class="hr-note-label">HR Note (<?= ucfirst($req['status']) ?>)</div>
                                    <div class="hr-note-text"><?= htmlspecialchars($req['hr_note']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="modalOverlay" class="modal-overlay">
    <div class="modern-modal">

        <div class="modal-header">
            <h2 id="modalTitle"></h2>
            <button class="close-btn" onclick="closeModal()">×</button>
        </div>

        <div class="modal-body">
            <p id="modalMessage"></p>

            <textarea id="modalTextarea" name="hr_note"
                placeholder=""
                class="modal-textarea"></textarea>
        </div>

        <form id="modalForm" method="POST">
            <input type="hidden" name="request_id" id="modalRequestId">

            <div class="modal-actions">
                <button type="button" class="cancel-btn" onclick="closeModal()">
                    Cancel
                </button>

                <button type="submit" id="confirmBtn" class="confirm-btn">
                    Confirm
                </button>
            </div>
        </form>

    </div>
</div>
<script src="<?= URLROOT; ?>/public/js/hr/hr_rescheduleRequests.js"></script>
</body>

</html>