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
  <title>announcement</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
  <style>
  /* ========================
   Heading (reduced spacing)
======================== */
h2 {
  text-align: center;
  margin: 15px 0 10px;   /* 👈 reduced */
  font-size: 24px;
  font-weight: 600;
  color: #1e88e5;
}

/* ========================
   Form Container (no top gap)
======================== */
form {
  width: 90%;
  max-width: 600px;
  margin: 0 auto 20px;   /* 👈 removed top margin */
  background: #ffffff;
  padding: 18px 20px;    /* 👈 slightly tighter */
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

/* ========================
   Labels
======================== */
form label {
  display: block;
  font-weight: 600;
  margin-bottom: 4px;
  margin-top: 10px;      /* 👈 controlled spacing */
  color: #374151;
}

/* ========================
   Inputs / Textarea / Select
======================== */
form input[type="text"],
form textarea,
form select {
  width: 100%;
  padding: 9px 10px;
  margin-bottom: 8px;    /* 👈 smaller gap */
  border-radius: 6px;
  border: 1px solid #d1d5db;
  font-size: 14px;
  background: #f9fafb;
}

form textarea {
  resize: vertical;
  min-height: 100px;
}

/* ========================
   Buttons
======================== */
/* ========================
   Button Row
======================== */
.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 12px;
  align-items: center;
}

/* Update button */
.form-actions button {
  padding: 9px 16px;
  background: #1e88e5;
  color: #fff;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

/* Cancel button */
.btn-cancel {
  padding: 9px 16px;
  background: #e5e7eb;
  color: #111;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
}

/* ========================
   Mobile only (stack buttons)
======================== */
@media (max-width: 500px) {
  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .form-actions button,
  .btn-cancel {
    width: 100%;
    text-align: center;
  }
}

</style>
</head>
<body></body>
<h2>Edit Announcement</h2>

<form action="<?= URLROOT ?>/AnnouncementCRUD/edit/<?= $announcement['id'] ?? '' ?>" method="POST">
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

    <div class="form-actions">
    <button type="submit">Update</button>
    <a href="<?= URLROOT ?>/AnnouncementCRUD/index" class="btn-cancel">Cancel</a>
</div>

</form>
