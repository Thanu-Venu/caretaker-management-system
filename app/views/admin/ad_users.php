<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Users</title>

  <!-- Boxicons -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_users.css">
</head>
<body>
<main class="main-content">
<div class="content">
  <h2>Staff Roles and Access Control</h2>
  <!-- Add User Button -->
  <button class="add-btn" onclick="window.location.href='<?php echo URLROOT; ?>/userCRUD/add'">Add User</button>
</div>

<!-- Users Table -->
  <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
            
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['users'])): ?>
            <?php foreach ($data['users'] as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['status'] ?? 'N/A'); ?></td>
                <td>
                  <a href="<?php echo URLROOT; ?>/userCRUD/edit/<?php echo $user['id'] ?? 0; ?>"><i class="bx bx-edit"></i></a> |
                  <a href="<?php echo URLROOT; ?>/userCRUD/delete/<?php echo $user['id'] ?? 0; ?>" onclick="return confirm('Are you sure?');"><i class="bx bx-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
</main>
<!-- JS for Search Filter -->
<script>
  const searchInput = document.getElementById('searchInput');
  searchInput.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
  });
</script>

<!-- Custom JS -->
<script src="<?php echo URLROOT; ?>/public/js/admin/ad_users.js"></script>

</body>
</html>