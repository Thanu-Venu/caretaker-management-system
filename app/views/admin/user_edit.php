<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_users.css">
</head>
<body>
<main class="main-content">
  <section class="form-section">
    <h1>Edit User</h1>
    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" value="<?php echo $data['user']->username; ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?php echo $data['user']->email; ?>" required>

      <label>Role</label>
      <select name="role" required>
        <option value="Admin" <?php echo ($data['user']->role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
        <option value="HR Manager" <?php echo ($data['user']->role == 'HR Manager') ? 'selected' : ''; ?>>HR Manager</option>
        <option value="Caregiver" <?php echo ($data['user']->role == 'Caregiver') ? 'selected' : ''; ?>>Caregiver</option>
      </select>

      <button type="submit" class="submit-btn">Update User</button>
    </form>
  </section>
</main>
</body>
</html>
