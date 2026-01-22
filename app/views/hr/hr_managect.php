<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Availability</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_managect.css">
</head>
<body>
  <!-- Main Content -->
  <div class="content">
    <h1 class="page-title">Caregiver Management</h1>

    <!-- Search bar -->
    <div class="search-box">
      <i class='bx bx-search'></i>
      <input type="text" id="searchInput" placeholder="Search caregivers">
    </div>

    <!-- Table -->
    <h2 class="section-title">Caregiver Availability</h2>
    <div class="table-container">
      <table class="caretaker-table" id="caretakerTable">
        <thead>
          <tr>
            <th>Caregiver ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Location</th>
            <th>Check-In</th>
            <th>Check-Out</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['caretakers'])): ?>
        <?php foreach ($data['caretakers'] as $ct): ?>
        <tr>
          <td><?php echo $ct['id']; ?></td>
          <td><?php echo htmlspecialchars($ct['name']); ?></td>

          <td>
            <span class="status 
            <?php
              if ($ct['availability'] == 'Available') echo 'available';
              elseif ($ct['availability'] == 'On Duty') echo 'duty';
              elseif ($ct['availability'] == 'Unavailable') echo 'unavailable';
              else echo 'leave';
            ?>">
            <?php echo $ct['availability']; ?>
            </span>
          </td>

          <td><?php echo $ct['location'] ?? 'N/A'; ?></td>
          <td><?php echo $ct['check_in'] ?? 'N/A'; ?></td>
          <td><?php echo $ct['check_out'] ?? 'N/A'; ?></td>
      </tr>
      <?php endforeach; ?>  
      <?php else: ?>
      <tr>
        <td colspan="6">No caregivers found</td>
      </tr>
      <?php endif; ?>
      </tbody>

      </table>
    </div>
  </div>

    <script src="<?php echo URLROOT; ?>/public/js/hr/hr_managect.js"></script>
</body>
</html>