<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php
if (isset($_SESSION['flash_message'])) {
    echo "<script>alert('" . addslashes($_SESSION['flash_message']) . "');</script>";
    unset($_SESSION['flash_message']);
}

$complaintsList = $data['complaints'] ?? ($complaints ?? []);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintlist.css">
</head>
<body>

<main class="content">
    <div class="header-row">
        <h1>Registered Complaints</h1>
        <a class="register-btn" href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintReg">+ Register Complaint</a>
    </div>

    <div class="table-wrapper">
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Caretaker</th>
                    <th>Complaint Category</th>
                    <th>Complaint Description</th>
                    <th>Complaint Registered Date</th>
                </tr>
            </thead>

            <tbody>
            <?php if (!empty($complaintsList)): ?>
                <?php foreach ($complaintsList as $complaint): ?>
                    <tr>
                        <td><?= htmlspecialchars($complaint['client_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['caretaker_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['category']) ?></td>
                        <td><?= htmlspecialchars($complaint['details']) ?></td>
                        <td>
                            <?php $registeredAt = strtotime((string)($complaint['complaint_date'] ?? '')); ?>
                            <?php if ($registeredAt !== false): ?>
                                <span class="complaint-date"><?= date('Y-m-d', $registeredAt) ?></span>
                                <span class="complaint-time"><?= date('h:i A', $registeredAt) ?></span>
                            <?php else: ?>
                                <?= htmlspecialchars((string)($complaint['complaint_date'] ?? '')) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-row">No complaint registered.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>