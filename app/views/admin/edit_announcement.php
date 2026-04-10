<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";

// Safe assignment to avoid undefined index errors
$announcement = $data['announcement'] ?? [];
$title = htmlspecialchars($announcement['title'] ?? '', ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($announcement['message'] ?? '', ENT_QUOTES, 'UTF-8');
$target_role = $announcement['target_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Announcement</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
</head>

<body>
  <main class="main-content">
    <h2>Edit Announcement</h2>

    <form class="announcement-form" action="<?= URLROOT ?>/AnnouncementCRUD/edit/<?= $announcement['id'] ?? '' ?>" method="POST">
      <label>Title</label>
      <input type="text" name="title" value="<?= $title ?>" required>

      <label>Message</label>
      <textarea name="message" required><?= $message ?></textarea>

      <label>Target Role</label>
      <select name="target_role" required>
        <option value="All" <?= $target_role == 'All' ? 'selected' : '' ?>>All</option>
        <option value="users" <?= $target_role == 'users' ? 'selected' : '' ?>>Users</option>
        <option value="Caretaker" <?= $target_role == 'Caretaker' ? 'selected' : '' ?>>Caretaker</option>
        <option value="Client" <?= $target_role == 'Client' ? 'selected' : '' ?>>Client</option>
      </select>

      <div class="form-actions action-group">
        <button type="submit">Update</button>
        <a href="<?= URLROOT ?>/AnnouncementCRUD/index" class="btn-cancel">Cancel</a>
      </div>
    </form>
  </main>
</body>

</html>