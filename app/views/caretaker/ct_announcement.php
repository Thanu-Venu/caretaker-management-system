<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_announcement.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
<div class="main-content announcement-container">
    <div class="card">
        <h1 class="page-title">Announcements</h1>
        <p style="color: #5b7288; margin-bottom: 20px; font-size: 14px;">Updates published for caretakers and general audiences.</p>
        
        <?php if (empty($data)): ?>
        <div class="no-announcement" style="text-align: center; padding: 40px; color: #5b7288; background: #f8fbff; border-radius: 12px; margin-top: 20px; border: 1px dashed #d8e6f4;">
            <p style="font-size: 16px; font-weight: 500;">No announcements available at the moment.</p>
        </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date Published</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Audience</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $announcement): ?>
                        <tr>
                            <td><span class="announcement-date"><?= date('M j, Y', strtotime($announcement['created_at'])); ?></span></td>
                            <td style="font-weight: 600; color: #17324d; font-size: 15px;"><?= htmlspecialchars($announcement['title']); ?></td>
                            <td style="max-width: 350px; line-height: 1.5; color: #333e50; font-size: 14px;"><?= nl2br(htmlspecialchars($announcement['message'])); ?></td>
                            <td><span class="role-tag">Caregiver</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>