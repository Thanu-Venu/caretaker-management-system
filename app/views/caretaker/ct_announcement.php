<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Caretaker</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_announcement.css">
</head>

<body>

<div class="announcement-container">
    <div class="card">
        <h2 class="page-title">📢 Announcements</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Target</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--announcement-muted); padding: 24px;">No announcements available at the moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $announcement): ?>
                            <tr>
                                <td>
                                    <span class="announcement-date">
                                        <?= date('M d, Y', strtotime($announcement['created_at'])); ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($announcement['title']); ?></strong></td>
                                <td><?= nl2br(htmlspecialchars($announcement['message'])); ?></td>
                                <td><span class="role-tag">For Caretakers</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>