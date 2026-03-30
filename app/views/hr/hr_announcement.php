<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Management - SmartCare</title>

  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_announcement.css" />
</head>
<body>
    <div class="announcement-container">

    <h2 class="page-title">Announcements</h2>

    <?php if (empty($data)): ?>
        <div class="no-announcement">
            <p>No announcements available at the moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($data as $announcement): ?>
            <div class="announcement-card">

                <div class="announcement-header">
                    <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                    <span class="announcement-date">
                        <?= date('M d, Y', strtotime($announcement['created_at'])); ?>
                    </span>
                </div>

                <div class="announcement-body">
                    <p><?= nl2br(htmlspecialchars($announcement['message'])); ?></p>
                </div>

                <div class="announcement-footer">
                    <span class="role-tag">For HR</span>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
