<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

  <main class="main-content">
    <section class="announcement-header page-header">
      <h1 class="page-title">Announcements</h1>
      <div class="header-actions">
        <a href="<?= URLROOT ?>/AnnouncementCRUD/create" class="btn-add">
          + Add Announcement
        </a>
      </div>
    </section>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Target</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($data['announcements'])): ?>
            <?php foreach ($data['announcements'] as $ann): ?>
              <tr>
                <td><?= htmlspecialchars($ann['title']) ?></td>
                <td><?= htmlspecialchars($ann['message']) ?></td>
                <td><?= htmlspecialchars($ann['target_role']) ?></td>
                <td><?= htmlspecialchars($ann['created_at']) ?></td>
                <td class="actions">
                  <a href="<?= URLROOT ?>/AnnouncementCRUD/edit/<?= $ann['id'] ?>" title="Edit"><i class="bx bx-edit" aria-hidden="true"></i></a>
                  <a href="<?= URLROOT ?>/AnnouncementCRUD/delete/<?= $ann['id'] ?>"
                    onclick="return confirm('Delete this announcement?')" title="Delete"><i class="bx bx-trash" aria-hidden="true"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">No announcements found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>

</html>
