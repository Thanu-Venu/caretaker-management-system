<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 

$user = $data['user']; // object
?>

<main class="main-content">
  <section class="form-section">
    <h1>Edit User</h1>
    <form action="<?php echo URLROOT; ?>/UserCRUD/edit/<?php echo $user->id; ?>" method="POST">
      <label>Username</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($user->username); ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>

      <label>Role</label>
      <select name="role" required>
        <option value="Admin" <?php echo ($user->role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
        <option value="Manager" <?php echo ($user->role == 'Manager') ? 'selected' : ''; ?>>Manager</option>
        <option value="Caretaker" <?php echo ($user->role == 'Caretaker') ? 'selected' : ''; ?>>Caretaker</option>
        <option value="Client" <?php echo ($user->role == 'Client') ? 'selected' : ''; ?>>Client</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="Active" <?php echo ($user->status == 'Active') ? 'selected' : ''; ?>>Active</option>
        <option value="Inactive" <?php echo ($user->status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
      </select>

      <button type="submit" class="submit-btn">Update User</button>
      <a href="<?php echo URLROOT; ?>/userCRUD/list" class="cancel-btn">Cancel</a>
    </form>
  </section>
</main>

<!-- Include CSS separately -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/user_edit.css">
