<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add User</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/caretaker_add.css">
</head>
<body>
<main class="main-content">
    <section class="form-section">
        <h1>Add User</h1>
        <form method="POST" class="caretaker-form">

            <label>Name</label>
            <input type="text" name="name" required placeholder="Enter full name">

            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter email">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter password">

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
