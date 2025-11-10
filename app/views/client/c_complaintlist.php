<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintlist.css">
</head>
<body>
<h2>My Complaints</h2>

<?php if (!empty($complaints)): ?>
    <div class="main-content">
        <h1>Registered Complaints</h1>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Caretaker</th>
        <th>Category</th>
        <th>Details</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($complaints as $complaint): ?>
    <tr>
        <td><?= htmlspecialchars($complaint['Id']) ?></td>
        <td><?= htmlspecialchars($complaint['caretaker_name']) ?></td>
        <td><?= htmlspecialchars($complaint['category']) ?></td>
        <td><?= htmlspecialchars($complaint['details']) ?></td>
        <td><?= htmlspecialchars($complaint['status']) ?></td>
        <td><?= htmlspecialchars($complaint['complaint_date']) ?></td>
        <td>
            <?php if ($complaint['status'] != 'Resolved' && $complaint['status'] != 'Closed'): ?>
                <a href="<?= URLROOT ?>/index.php?url=Complaint/clientEdit/<?= $complaint['Id'] ?>">Edit</a>
|
                <a href="<?= URLROOT ?>/index.php?url=Complaint/clientDelete/<?= $complaint['Id'] ?>" onclick="return confirm('Delete this complaint?')">Delete</a>
            <?php else: ?>
                <em>Locked</em>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<?php else: ?>
<p>No complaints found.</p>
<?php endif; ?>

