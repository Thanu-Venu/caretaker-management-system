<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_reports.css">
</head>

<body>
  <div class="main-content">
    <h1>My Reports</h1>

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
</body>