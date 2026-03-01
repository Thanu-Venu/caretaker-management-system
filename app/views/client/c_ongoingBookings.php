<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Ongoing Bookings</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_upcomingBookings.css">
</head>

<body>
<main class="content">
    <h1>My Ongoing Bookings</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (empty($data['bookings'])): ?>
        <p class="no-bookings">You don’t have any ongoing bookings at the moment.</p>
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
                    <td><?= $b['duration'].' '.$b['basis'] ?></td>
                    <td><?= htmlspecialchars($b['status']) ?></td>

                    <td class="actions">
                        <?php
$hasPendingChange = ($b['status'] === 'Change_Requested'); // simplest
// or better: check change_requests table and set a flag in your query
?>
                        
                        <?php if (!$hasPendingChange): ?>
  <button class="action-btn" id="change-btn" onclick="openChangeModal(<?= $b['booking_id'] ?>)">Change Caregiver</button>
<?php else: ?>
  <button class="action-btn" id="change-btn" disabled style="opacity:.6; cursor:not-allowed;">
    Change Requested
  </button>
<?php endif; ?>
                        <button class="action-btn" id="cancel-btn" onclick="openCancelModal(<?= $b['booking_id'] ?>)">Cancel</button>
                        <?php
                            // only allow reschedule when status permits and there is no pending reschedule request
                            $canReschedule = in_array($b['status'], ['Accepted','Advance_Paid','Payment_Requested','Change_Requested']);
                            if ($b['status'] === 'Reschedule_Requested') {
                                $canReschedule = false;
                            }
                        ?>
                        <?php if ($canReschedule): ?>
                            <button class="action-btn" id="reschedule-btn" onclick="openRescheduleModal(<?= $b['booking_id'] ?>)">Reschedule</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <?php endif; ?>
</main>

<!-- ================= CHANGE MODAL ================= -->
<div id="changeModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeChangeModal()">&times;</span>
    <h2>Request Caretaker Change</h2>

    <form method="POST" action="<?= URLROOT ?>/client/requestChangeCaretaker">
      <input type="hidden" name="booking_id" id="changeBookingId">

      <!-- this replaces <select> -->
      <input type="hidden" name="new_caretaker_id" id="selectedCaretakerId" required>

      <label>Select replacement caregiver</label>

      <div id="caretakerGrid" class="caretaker-grid"></div>

      <p id="noCaretakersMsg" class="error-msg" style="margin-top:8px; font-size:13px;"></p>

      <label>Reason for change</label>
      <textarea name="reason" rows="3" placeholder="Why would you like a different caregiver?" required></textarea>

      <button type="submit" class="modal-submit-btn">Submit Request</button>
    </form>
  </div>
</div>

<!-- include the same modals from upcoming page -->
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

        <form method="POST" action="<?= URLROOT ?>/client/rescheduleBooking">
            <input type="hidden" name="booking_id" id="rescheduleBookingId">

            <label>New Date</label>
            <input type="date" name="new_date" required>


            <label>Reason for rescheduling</label>
            <textarea name="reason" rows="3" placeholder="Optional (for HR)" ></textarea>

            <button type="submit" class="reschedule-btn">Save Changes</button>
        </form>
    </div>
</div>

<!-- minimal javascript for popups -->
<script src="<?= URLROOT ?>/public/js/client/c_ongoingBookings.js"></script>
<!-- modal HTML here (changeModal) -->

<script>
  async function openChangeModal(bookingId){
    // set hidden booking id in modal
    document.getElementById("changeBookingId").value = bookingId;

    // call your controller endpoint to get available caretakers
    const res = await fetch(`<?= URLROOT ?>/client/fetchAvailableCaretakers?booking_id=${bookingId}`);
    const list = await res.json();

    // if your endpoint returns {error: "..."}
    if (list.error) {
      document.getElementById("noCaretakersMsg").textContent = list.error;
      document.getElementById("caretakerGrid").innerHTML = "";
    } else {
      renderCaretakerCards(list); // this function renders the cards
    }

    // show modal
    document.getElementById("changeModal").style.display = "flex";;
  }

  function closeChangeModal(){
    document.getElementById("changeModal").style.display = "none";
  }
</script>
</body>
</html>