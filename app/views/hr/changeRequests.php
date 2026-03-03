<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Caregiver Requests</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/hr/hr_requests.css">
</head>

<body>
    <main class="content">
        <h1>Caregiver Change Requests</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="success-msg"><?php echo $_SESSION['success'];
                                    unset($_SESSION['success']); ?></p>
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
                            <th>Date</th>
                            <th>Time</th>
                            <th>Old Caregiver</th>
                            <th>Requested Caregiver</th>
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
                                <td><?= date('Y-m-d', strtotime($req['booking_date'])) ?></td>
                                <td><?= htmlspecialchars($req['preferred_time']) ?></td>
                                <td><?= htmlspecialchars($req['old_caretaker']) ?></td>
                                <td><?= htmlspecialchars($req['new_caretaker']) ?></td>
                                <td><?= htmlspecialchars($req['reason']) ?></td>
                                <td>
                                    <form method="POST" action="<?= URLROOT ?>/hr/approveChange" style="margin-bottom:6px;">
                                        <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">

                                        <textarea name="hr_note"
                                            placeholder="Optional HR note (visible to client)"
                                            style="width:180px; height:50px; font-size:12px; margin-bottom:4px;"></textarea>

                                        <div>
                                            <button type="submit" class="approve-btn">Approve</button>
                                        </div>
                                    </form>

                                    <form method="POST" action="<?= URLROOT ?>/hr/rejectChange">
                                        <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">

                                        <textarea name="hr_note"
                                            placeholder="Reason for rejection (recommended)"
                                            required
                                            style="width:180px; height:50px; font-size:12px; margin-bottom:4px;"></textarea>

                                        <div>
                                            <button type="submit" class="reject-btn">Reject</button>
                                        </div>
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