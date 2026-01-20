<?php
include_once APPROOT . "/views/templates/admin/ad_header.php";
include_once APPROOT . "/views/templates/admin/ad_sidebar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Booking Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
</head>
<body>
<h2>Announcements</h2>

<form action="<?= URLROOT ?>/AnnouncementCRUD/add" method="POST">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="message" placeholder="Message" required></textarea>
    <select name="target_role" required>
        <option value="All">All</option>
        <option value="Users">Admin/Hr</option>
        <option value="Caretaker">Caretaker</option>
        <option value="Client">Client</option>
    </select>
    <button type="submit">Add Announcement</button>
</form>

<table border="1" width="100%" style="margin-left: 300px;">
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
                        <a href="<?= URLROOT ?>/AnnouncementCRUD/edit/<?= $ann['id'] ?>"><i class="bx bx-edit" style="font-size: 20px;"></i></a>
                        <a href="<?= URLROOT ?>/AnnouncementCRUD/delete/<?= $ann['id'] ?>"
                           onclick="return confirm('Delete this announcement?')"><i class="bx bx-trash" style="font-size: 20px;"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No announcements found</td></tr>
        <?php endif; ?>
    </tbody>
</table>
