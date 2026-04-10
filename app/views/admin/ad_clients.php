<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_clients.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <div class="content">


    <section class="client-header">
      <h1>Client Management</h1>
      <a class="btn-add" href="<?= URLROOT ?>/admin/ad_bookings">View Bookings</a>
    </section>

    <div class="table-container">
      <table class="client-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Client Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data['clients'])): ?>
            <?php foreach ($data['clients'] as $client): ?>
              <tr>
                <td><?= htmlspecialchars($client['id']) ?></td>
                <td><?= htmlspecialchars($client['name']) ?></td>
                <td><?= htmlspecialchars($client['email']) ?></td>
                <td><?= htmlspecialchars($client['phone']) ?></td>
                <td><?= htmlspecialchars($client['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">No clients found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</body>

</html>