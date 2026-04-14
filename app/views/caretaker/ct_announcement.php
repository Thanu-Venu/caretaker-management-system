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

<div class="main-content">
        <h2>Announcements</h2>
        <?php if (empty($data)): ?>
        <div class="no-announcement">
            <p>No announcements available at the moment.</p>
        </div>
        <?php else: ?>
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
                            <td><?= htmlspecialchars($announcement['title']); ?></td>
                            <td><?= nl2br(htmlspecialchars($announcement['message'])); ?></td>
                            <td><?= date('Y-m-d', strtotime($announcement['created_at'])); ?></td>
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