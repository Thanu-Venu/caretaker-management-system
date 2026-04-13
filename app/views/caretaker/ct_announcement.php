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
        <h2 class="page-title">Announcements</h2>
        <?php if (empty($data)): ?>
        <div class="no-announcement">
            <p>No announcements available at the moment.</p>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date Published</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $announcement): ?>
                        <tr>
                            <td class="td-title"><strong><?= htmlspecialchars($announcement['title']); ?></strong></td>
                            <td class="td-message"><?= nl2br(htmlspecialchars($announcement['message'])); ?></td>
                            <td class="td-date"><?= date('M d, Y', strtotime($announcement['created_at'])); ?></td>
                            <td><span class="role-tag">Caregiver</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>