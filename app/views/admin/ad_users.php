<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_users.css">
</head>
<body>
    <div class="content">
        <button class="add-btn">Add User</button><br><br><br><br><br><br>

        
    <!-- Add User Modal -->
    <div class="modal" id="addUserModal">
      <div class="modal-content">
        <h3>Add New User</h3>
        <form id="addUserForm">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" placeholder="Enter name" required>

          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter email" required>

          <label for="phone">Phone</label>
          <input type="text" id="phone" name="phone" placeholder="Enter phone" required>

          <label for="role">User Type</label>
          <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="Admin">Admin</option>
            <option value="HR Manager">HR Manager</option>
          </select>

          <div class="modal-buttons">
            <button type="button" class="cancel-btn">Cancel</button>
            <button type="submit" class="save-btn">Save Changes</button>
          </div>
        </form>
      </div>
    </div>

  <div class="card">
    <h2>User Roles and Access Control</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>emma.smith</td>
            <td>Admin</td>
            <td>
              <button class="link-btn">Edit Role</button> |
              <button class="link-btn">Manage Permissions</button>
            </td>
          </tr>
          <tr>
            <td>david.jones</td>
            <td>HR Manager</td>
            <td>
              <button class="link-btn">Edit Role</button> |
              <button class="link-btn">Manage Permissions</button>
            </td>
          </tr>
          <tr>
            <td>olivia.brown</td>
            <td>HR Manager</td>
            <td>
              <button class="link-btn">Edit Role</button> |
              <button class="link-btn">Manage Permissions</button>
            </td>
          </tr>
         
        </tbody>
      </table>
    </div>
    
  </div>
</div>
<script src="<?php echo URLROOT; ?>/public/js/admin/ad_users.js"></script>
</body>
</html>