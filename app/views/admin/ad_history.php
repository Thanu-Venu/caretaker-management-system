<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Logs - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_history.css">
  <!-- Design System Override (ensures consistency) -->
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/system/legacy-overrides.css">
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
        <?php
        $currentPage = $data['currentPage'] ?? 1;
        $totalPages = $data['totalPages'] ?? 1;
        $query = $_GET;
        ?>

        <div class="pagination">
          <?php if ($currentPage > 1): ?>
            <?php $query['page'] = $currentPage - 1; ?>
            <a href="<?= URLROOT ?>/admin/ad_history?<?= http_build_query($query) ?>">Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $query['page'] = $i; ?>
            <a class="<?= ($i == $currentPage) ? 'active' : '' ?>"
              href="<?= URLROOT ?>/admin/ad_history?<?= http_build_query($query) ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if ($currentPage < $totalPages): ?>
            <?php $query['page'] = $currentPage + 1; ?>
            <a href="<?= URLROOT ?>/admin/ad_history?<?= http_build_query($query) ?>">Next</a>
          <?php endif; ?>
        </div>

      </section>
    </div>
  </main>

</body>

</html>