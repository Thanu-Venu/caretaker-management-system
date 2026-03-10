<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Upcoming Bookings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_upcomingBookings.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-requested {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-payment_requested {
            background-color: #ffe5cc;
            color: #cc6600;
            border: 1px solid #ffccaa;
        }

        .status-advance_paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <?php if (!empty($data['pendingAdvance'])): ?>
        <div id="advanceModal" class="modal" style="display:flex;">
            <div class="modal-content" style="max-width:640px;">
                <span class="close" onclick="document.getElementById('advanceModal').style.display='none'">&times;</span>
                <h2 style="margin-bottom:12px; color:#1e88e5; font-family: 'Poppins', sans-serif; font-weight:700; font-size:24px;">Advance Payments Required</h2>
                <p>You have pending advance payments for the following bookings:</p>

                <?php foreach ($data['pendingAdvance'] as $p): ?>
                    <div class="advance-details" style="margin-bottom:15px;">
                        <div><b>Booking #:</b> <?= $p['booking_id'] ?></div>
                        <div><b>Service:</b> <?= htmlspecialchars($p['service_type']) ?></div>
                        <div><b>Date:</b> <?= htmlspecialchars($p['booking_date']) ?></div>
                        <div><b>Time:</b> <?= htmlspecialchars($p['preferred_time']) ?></div>
                        <div><b>Duration:</b> <?= htmlspecialchars($p['duration'] . ' ' . $p['basis']) ?></div>

                        <a class="modal-submit-btn"
                            style="margin-top:10px;"
                            href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= $p['booking_id'] ?>">
                            Pay Now
                        </a>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>
    <main class="content">
        <h1>My Upcoming Bookings</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="success-msg"><?php echo $_SESSION['success'];
                                    unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="error-msg"><?php echo $_SESSION['error'];
                                    unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (empty($data['bookings'])): ?>
            <p class="no-bookings">You don’t have any upcoming bookings yet.</p>
        <?php else: ?>


            <div class="table-wrapper">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Caregiver</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['bookings'] as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['caretaker_name']) ?></td>
                                <td><?= htmlspecialchars($b['service_type']) ?></td>
                                <td><?= date('Y-m-d', strtotime($b['booking_date'])) ?></td>
                                <td><?= htmlspecialchars($b['preferred_time']) ?></td>
                                <td><?= $b['duration'] . ' ' . $b['basis'] ?></td>

                                <td>
                                    <span class="status-badge status-<?= strtolower($b['status']) ?>">
                                        <?php
                                        $statusDisplay = str_replace('_', ' ', $b['status']);
                                        echo htmlspecialchars($statusDisplay);
                                        ?>
                                    </span>
                                </td>

                                <td class="actions">
                                    <!-- VIEW CONTACT (only after advance payment) -->
                                    <?php
                                    $advancePaidStatuses = ['Advance_Paid', 'Accepted', 'Reschedule_Requested', 'Change_Requested'];
                                    if (in_array($b['status'], $advancePaidStatuses)):
                                    ?>
                                        <a class="action-btn" id="contact-btn"
                                            href="<?= URLROOT ?>/client/c_contactCT?booking_id=<?= (int)$b['booking_id'] ?>"
                                            style="background-color: #28a745; border-color: #28a745;">
                                            View Contact
                                        </a>
                                    <?php endif; ?>

                                    <button class="action-btn" id="cancel-btn"
                                        onclick="openCancelModal(<?= $b['booking_id'] ?>)">
                                        Cancel
                                    </button>

                                    <!-- PAY NOW (only when Payment_Requested) -->
                                    <?php if ($b['status'] === 'Payment_Requested'): ?>
                                        <a class="action-btn" id="paynow-btn"
                                            href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= (int)$b['booking_id'] ?>">
                                            Pay Now
                                        </a>
                                    <?php endif; ?>

                                    <?php
                                    // ========== RESCHEDULE BUTTON LOGIC ==========
                                    // Only show reschedule button if:
                                    // 1. Booking status is 'Requested' (only this status allows reschedule)
                                    // 2. No existing reschedule request (pending/approved)

                                    $canShowReschedule = false;
                                    $rescheduleTooltip = '';

                                    if ($b['status'] === 'Requested') {
                                        // Check if reschedule request already exists
                                        require_once APPROOT . '/models/RescheduleRequestModel.php';
                                        $rrCheck = new RescheduleRequestModel();
                                        $hasReschedule = $rrCheck->hasRescheduleRequest($b['booking_id']);

                                        if ($hasReschedule) {
                                            $canShowReschedule = false;
                                            $rescheduleTooltip = 'A reschedule request has already been submitted for this booking';
                                        } else {
                                            $canShowReschedule = true;
                                        }
                                    } else {
                                        $rescheduleTooltip = "Only bookings with 'Requested' status can be rescheduled";
                                    }
                                    ?>

                                    <?php if ($canShowReschedule): ?>
                                        <button class="action-btn" id="reschedule-btn"
                                            onclick="openRescheduleModal(<?= $b['booking_id'] ?>)">
                                            Reschedule
                                        </button>
                                    <?php elseif ($b['status'] === 'Requested'): ?>
                                        <!-- Show disabled button with tooltip for 'Requested' bookings that already have a reschedule request -->
                                        <button class="action-btn" id="reschedule-btn"
                                            disabled
                                            style="opacity: 0.5; cursor: not-allowed;"
                                            title="<?= htmlspecialchars($rescheduleTooltip) ?>">
                                            Reschedule
                                        </button>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>


        <!-- ================= CANCEL MODAL ================= -->
        <div id="cancelModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCancelModal()">&times;</span>
                <h2>Cancel Booking</h2>

                <form method="POST" action="<?= URLROOT ?>/client/cancelBooking">
                    <input type="hidden" name="booking_id" id="cancelBookingId">

                    <label>Reason for cancellation</label>
                    <textarea name="reason" rows="3" placeholder="Enter reason" required></textarea>

                    <button type="submit" class="cancel1-btn">Confirm Cancel</button>
                </form>
            </div>
        </div>

        <!-- ================= RESCHEDULE MODAL ================= -->
        <div id="rescheduleModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeRescheduleModal()">&times;</span>
                <h2>Reschedule Booking</h2>

                <div class="warning-box" style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 15px; border-radius: 4px;">
                    <strong>⚠️ Important:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        <li>Only the <strong>date</strong> can be changed through reschedule</li>
                        <li>Service type, duration, and caregiver remain the same</li>
                        <li>You can only reschedule <strong>once per booking</strong></li>
                        <li>Requests must be made at least <strong>24 hours in advance</strong></li>
                        <li>Status must be 'Requested' to allow reschedule</li>
                    </ul>
                </div>

                <form method="POST" action="<?= URLROOT ?>/client/rescheduleBooking">
                    <input type="hidden" name="booking_id" id="rescheduleBookingId">

                    <label>New Date <span style="color: red;">*</span></label>
                    <input type="date" name="new_date" required
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                        title="Must be at least 24 hours from now">

                    <label>Reason for Rescheduling <span style="color: #666; font-size: 0.9em;">(Optional)</span></label>
                    <textarea name="reason" rows="3" placeholder="Provide a reason for HR review (optional)"></textarea>

                    <button type="submit" class="reschedule-btn">Submit Reschedule Request</button>
                </form>
            </div>
        </div>

        <!-- ================= MINIMAL JS (ONLY FOR POPUPS) ================= -->

        <script src="<?= URLROOT ?>/public/js/client/c_upcomingBookings.js"></script>



</body>

</html>