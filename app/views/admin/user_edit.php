<?php  
include_once APPROOT . "/views/templates/admin/ad_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 

$user = $data['user']; // object
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/user_edit.css">
</head>

<body>
<main class="main-content">
  <section class="form-section">
    <h1>Edit User</h1>

    <form action="<?php echo URLROOT; ?>/UserCRUD/edit/<?php echo $user->id; ?>" method="POST" class="user-form">

      <div class="form-grid">

        <div class="field">
          <label>Username</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($user->username); ?>" required>
        </div>

        <div class="field">
          <label>Email</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
        </div>

        <div class="field">
          <label>Role</label>
          <select name="role" required>
            <option value="Admin" <?php echo ($user->role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="Manager" <?php echo ($user->role == 'Manager') ? 'selected' : ''; ?>>Manager</option>
          </select>
        </div>

        <div class="field">
          <label>Status</label>
          <select name="status">
            <option value="Active" <?php echo ($user->status == 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo ($user->status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
          </select>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" class="submit-btn">Update User</button>
        <a href="<?php echo URLROOT; ?>/UserCRUD/list" class="btn-cancel">Cancel</a>
      </div>

    </form>
  </section>
</main>
</body>
</html>
