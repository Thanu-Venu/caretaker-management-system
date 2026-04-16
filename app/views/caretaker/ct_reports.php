<?php
$caretakerPageTitle = 'Reports - SmartCare';
$caretakerExtraCss = ['caretaker/ct_reports.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content reports-container">
    <header class="page-header" style="margin-bottom: 24px;">
      <h1 class="page-title" style="color: #1e88e5; font-size: 30px; font-weight: 700; margin: 0; letter-spacing: -0.02em;">My Reports</h1>
    </header>

<?php
$totalServices = count($data["services"] ?? []);
$totalHours = 0;
$uniqueClients = [];

foreach ($data["services"] ?? [] as $service) {
  $totalHours += (int)($service['duration'] ?? 0);
  if (!empty($service['client_name'])) {
      $uniqueClients[$service['client_name']] = true;
  }
}

$totalClients = count($uniqueClients);
?>
    <!-- Monthly Summary -->
    <section class="report-summary">
      <h2>Monthly Summary</h2>
      <div class="summary-cards">
        <div class="card">
          <h3>Total Services</h3>
          <p id="totalServices"><?= $totalServices ?></p>
        </div>
        <div class="card">
          <h3>Total Hours</h3>
          <p id="totalHours"><?= $totalHours ?></p>
        </div>
        <div class="card">
          <h3>Total Clients</h3>
          <p id="totalClients"><?= $totalClients ?></p>
        </div>
      </div>
    </section>
    <br><br>
    <!-- Service Details Table -->
    <section class="report-table-section">
      <div class="card-wrapper">
        <h2>Service Details</h2>
        <div class="report-table-container">
          <table class="report-table">
            <thead>
              <tr>
                <th>Client</th>
                <th>Service</th>
                <th>Date</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody id="serviceTableBody">
              <?php foreach ($data["services"] ?? [] as $service): ?>
                <tr>
                  <td><?= htmlspecialchars($service['client_name']) ?></td>
                  <td><?= htmlspecialchars($service['service_type']) ?></td>
                  <td><?= htmlspecialchars($service['booking_date']) ?></td>
                  <td><?= htmlspecialchars($service['duration']) ?> hrs</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <button id="downloadReport" class="btn-download">Download Report</button>
      </div>
    </section>
  </div>
  <script>
    const services = <?= json_encode(array_map(function($service) {
        return [
            'client' => $service['client_name'],
            'service' => $service['service_type'],
            'date' => $service['booking_date'],
            'hours' => $service['duration'],
            'payment' => 'N/A'
        ];
    }, $data['services'] ?? [])); ?>;
  </script>
  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_reports.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
