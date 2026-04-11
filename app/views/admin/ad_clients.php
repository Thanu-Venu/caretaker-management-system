<?php
$filters = $data['filters'] ?? ['q' => '', 'date_from' => '', 'date_to' => ''];
$clientsListUrl = URLROOT . '/public?url=admin/ad_clients';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Management</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_clients.css">
  <!-- Design System Override (ensures consistency) -->
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
  <main class="main-content">

    <section class="client-header page-header">
      <h1 class="page-title">Client Management</h1>
      <div class="header-actions">
        <a class="btn-add" href="<?= URLROOT ?>/public?url=admin/ad_bookings">View Bookings</a>
      </div>
    </section>

    <div class="filter-bar">
      <form method="get" action="<?= htmlspecialchars($clientsListUrl, ENT_QUOTES, 'UTF-8') ?>" class="filters-inline">
        <input type="hidden" name="url" value="admin/ad_clients">
        <div class="filter-group">
          <label for="clientSearch">Search</label>
          <input type="search" id="clientSearch" name="q" placeholder="Name, email, or phone"
            value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            autocomplete="off">
        </div>
        <div class="filter-group">
          <label for="date_from">Registered from</label>
          <input type="date" id="date_from" name="date_from"
            value="<?= htmlspecialchars($filters['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="filter-group">
          <label for="date_to">Registered to</label>
          <input type="date" id="date_to" name="date_to"
            value="<?= htmlspecialchars($filters['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <button type="submit" class="submit-btn">Apply</button>
        <a class="reset-btn" href="<?= htmlspecialchars($clientsListUrl, ENT_QUOTES, 'UTF-8') ?>">Reset</a>
      </form>
    </div>

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
                <td><?= htmlspecialchars((string)($client['id'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($client['name'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($client['email'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($client['phone'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($client['created_at'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted">No clients match your filters.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>

</html>
