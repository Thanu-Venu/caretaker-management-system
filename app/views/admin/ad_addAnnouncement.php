<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Announcement</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_announcement.css">
</head>

<body>
<main class="content">
  <h2>Add Announcement</h2>

  <form class="announcement-form" action="<?= URLROOT ?>/AnnouncementCRUD/add" method="POST">

    <label>Title</label>
    <input type="text" name="title" required>
    <br>
    <label>Message</label>
    <textarea name="message" rows="4" required></textarea>

    <label>Target Audience</label>
    <select name="target_role" required>
      <option value="All">All</option>
      <option value="users">Admin / HR</option>
      <option value="Caretaker">Caretaker</option>
      <option value="Client">Client</option>
    </select>

    <div class="form-actions">
      <button type="submit">Add Announcement</button>
      <a href="<?= URLROOT ?>/AnnouncementCRUD/index" class="btn-cancel">Cancel</a>
    </div>

  </form>
</main>
</body>
</html>
