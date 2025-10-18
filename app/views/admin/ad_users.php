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
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  

</head>
<body>
    <div class="content">
       <button class="add-btn" onclick="window.location.href='/CMA/public/userCRUD/add'">Add User</button>

  <div class="card">
    <h2>User Roles and Access Control</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['users'])): ?>
            <?php foreach ($data['users'] as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                  <a href="<?php echo URLROOT; ?>/userCRUD/edit/<?php echo $user['id']; ?>"><i class="bx bx-edit"></i></a>
 |
              <a href="<?php echo URLROOT; ?>/userCRUD/delete/<?php echo $user['id']; ?>" onclick="return confirm('Are you sure?');"><i class="bx bx-trash"></i></a>

                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    
</main>
<!-- JS for search filter -->
<script>
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
  });
});
</script>
    
  </div>
</div>
<script src="<?php echo URLROOT; ?>/public/js/admin/ad_users.js"></script>
</body>
</html>