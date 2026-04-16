<?php
$caretakerPageTitle = 'Reports - SmartCare';
$caretakerExtraCss = ['caretaker/ct_reports.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<?php
$rows = (isset($services) && is_array($services)) ? $services : [];

$totalServices = count($rows);
$totalHours = 0;
$uniqueClients = [];

foreach ($rows as $row) {
  $totalHours += (int) ($row['duration'] ?? 0);
  $clientName = trim((string) ($row['client_name'] ?? ''));
  if ($clientName !== '') {
    $uniqueClients[$clientName] = true;
  }
}

$totalClients = count($uniqueClients);
?>

  <main class="content reports-container">
    <div class="page-header reports-page-header">
      <div class="page-header-main">
        <h1 class="page-title">My Reports</h1>
        <p class="page-subtitle">Track completed services with a clean and consistent table view.</p>
      </div>
      <div class="header-actions reports-header-actions">
        <button id="downloadReport" type="button" class="btn secondary">Download CSV</button>
      </div>
    </div>

    <section class="summary-cards" aria-label="Report summary">
      <article class="card">
        <span class="card-label">Total services</span>
        <span class="card-value"><?= htmlspecialchars((string) $totalServices, ENT_QUOTES, 'UTF-8') ?></span>
      </article>
      <article class="card">
        <span class="card-label">Total hours</span>
        <span class="card-value"><?= htmlspecialchars((string) $totalHours, ENT_QUOTES, 'UTF-8') ?></span>
      </article>
      <article class="card">
        <span class="card-label">Unique clients</span>
        <span class="card-value"><?= htmlspecialchars((string) $totalClients, ENT_QUOTES, 'UTF-8') ?></span>
      </article>
    </section>

    <section class="panel">
      <h2 class="reports-section-title">Service Details</h2>
      <div class="table-wrap report-table-wrap">
        <table class="table report-table">
          <thead>
            <tr>
              <th>Client</th>
              <th>Service</th>
              <th>Date</th>
              <th>Duration</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody id="serviceTableBody">
            <?php if (empty($rows)): ?>
              <tr>
                <td class="empty" colspan="5">No completed services found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $service): ?>
                <tr>
                  <td><?= htmlspecialchars((string) ($service['client_name'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string) ($service['service_type'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string) ($service['booking_date'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars((string) ($service['duration'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> hrs</td>
                  <td><?= htmlspecialchars((string) ($service['preferred_time'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <script>
    const services = <?= json_encode(array_map(static function ($service) {
      return [
        'client' => (string) ($service['client_name'] ?? ''),
        'service' => (string) ($service['service_type'] ?? ''),
        'date' => (string) ($service['booking_date'] ?? ''),
        'hours' => (string) ($service['duration'] ?? ''),
        'payment' => 'N/A'
      ];
    }, $rows), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
  </script>
  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_reports.js"></script>
<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>