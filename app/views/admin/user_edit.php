<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
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
        <option value="Manager" <?php echo ($data['user']->role == 'Manager') ? 'selected' : ''; ?>>Manager</option>
        <option value="Caretaker" <?php echo ($data['user']->role == 'Caretaker') ? 'selected' : ''; ?>>Caretaker</option>
        <option value="Client" <?php echo ($data['user']->role == 'Client') ? 'selected' : ''; ?>>Client</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="Active" <?php echo ($data['user']->status == 'Active') ? 'selected' : ''; ?>>Active</option>
        <option value="Inactive" <?php echo ($data['user']->status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
      </select>

      <button type="submit" class="submit-btn">Update User</button>
    </form>
  </section>
</main>
</body>
</html>
