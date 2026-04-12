<?php
$hrPageTitle = 'History logs — HR';
$hrExtraCss  = ['hr/hr_logs.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content">
  <header class="page-header">
    <h1 class="page-title">Logs</h1>
  </header>

  <div class="table-container">
    <table class="table" id="logTable">
      <thead>
        <tr>
          <th>Timestamp</th>
          <th>Action description</th>
          <th>Affected section</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($data['logs'])): ?>
          <?php foreach ($data['logs'] as $log): ?>
            <tr>
              <td><?= htmlspecialchars((string) ($log['created_at'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($log['action'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($log['section'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="empty">No manager logs found</td>
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
        <a href="<?= URLROOT ?>/hr/hr_logs?<?= http_build_query($query) ?>">Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php $query['page'] = $i; ?>
        <a class="<?= ($i == $currentPage) ? 'active' : '' ?>"
          href="<?= URLROOT ?>/hr/hr_logs?<?= http_build_query($query) ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($currentPage < $totalPages): ?>
        <?php $query['page'] = $currentPage + 1; ?>
        <a href="<?= URLROOT ?>/hr/hr_logs?<?= http_build_query($query) ?>">Next</a>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
