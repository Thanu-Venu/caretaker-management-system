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
        <p class="success-msg"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <?php if (empty($data['requests'])): ?>
        <p>No pending requests.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>#</th>
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
                    <?php foreach ($data['requests'] as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['request_id']) ?></td>
                            <td><?= htmlspecialchars($req['booking_id']) ?></td>
                            <td><?= htmlspecialchars($req['client_name']) ?></td>
                            <td><?= htmlspecialchars($req['service_type']) ?></td>
                            <td><?= date('Y-m-d', strtotime($req['old_date'])) ?></td>
                            <td><?= date('Y-m-d', strtotime($req['new_date'])) ?></td>
                            <td><?= htmlspecialchars($req['reason']) ?></td>
                            <td>
                                <form method="POST" action="<?= URLROOT ?>/hr/approveReschedule" style="display:inline-block;">
                                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                    <button type="submit" class="approve-btn">Approve</button>
                                </form>
                                <form method="POST" action="<?= URLROOT ?>/hr/rejectReschedule" style="display:inline-block; margin-left:5px;">
                                    <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                    <button type="submit" class="reject-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
