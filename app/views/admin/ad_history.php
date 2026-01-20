<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Logs - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_history.css">
</head>
<body>
  

  <main class="content">
      <h1>Logs</h1>
  <div class="container">
    <section>
      <table id="logTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>User Name</th>
            <th>Role</th>
            <th>Action Description</th>
            <th>Affected Section</th>
          </tr>
        </thead>
       <tbody>
  <?php if (!empty($data['logs'])): ?>
    <?php foreach ($data['logs'] as $log): ?>
      <tr>
        <td><?= htmlspecialchars($log['created_at']) ?></td>
        <td><?= htmlspecialchars($log['username']) ?></td>
        <td><?= htmlspecialchars($log['role']) ?></td>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= htmlspecialchars($log['section']) ?></td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="5" style="text-align:center;">No admin logs found</td>
    </tr>
  <?php endif; ?>
</tbody>

      </table>
    </section>
  </div>
  </main>

</body>
</html>