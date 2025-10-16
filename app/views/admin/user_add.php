<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_users.css">
</head>
<body>
<main class="main-content">
  <section class="form-section">
    <h1>Add User</h1>
    <form method="POST">
      <label>Username</label>
      <input type="text" name="username" required placeholder="Enter username">

      <label>Email</label>
      <input type="email" name="email" required placeholder="Enter email">

      <label>Password</label>
      <input type="password" name="password" required placeholder="Enter password">

      <label>Role</label>
      <select name="role" required>
        <option value="">Select Role</option>
        <option value="Admin">Admin</option>
        <option value="Manager">Manager</option>
        <option value="Caretaker">Caretaker</option>
        <option value="Client">Client</option>
      </select>

      <label>Status</label>
      <select name="status">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>

      <button type="submit" class="submit-btn">Add User</button>
    </form>
  </section>
</main>
</body>
</html>
