<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>

  <div class="main-content">
    <div class="booking-header">
      <h2>Announcements</h2>
      <a href="<?= URLROOT ?>/AnnouncementCRUD/create" class="btn-add">
        + Add Announcement
      </a>
    </div>

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
                <td>
                  <a href="<?= URLROOT ?>/AnnouncementCRUD/edit/<?= $ann['id'] ?>"><i class="bx bx-edit"></i></a>
                  |
                  <a href="<?= URLROOT ?>/AnnouncementCRUD/delete/<?= $ann['id'] ?>"
                    onclick="return confirm('Delete this announcement?')"><i class="bx bx-trash"></i></a>
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
  </div>

</body>

</html>
