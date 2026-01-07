<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_feedback.css">
</head>
<body>

<div class="content"> 
  <h2>Feedback & Complaints</h2>
</div>

<div class="card">

  <!-- Tabs -->
  <div class="tabs">
    <button class="tab-btn active" data-tab="feedback">Client Feedback</button>
    <button class="tab-btn" data-tab="complaints">Complaints</button>
  </div>

  <!-- FEEDBACK TAB -->
  <div id="feedbackSection" class="tab-section">

    <!-- Filters -->
    <div class="filters">
      <label for="ratingFilter">Rating:</label>
      <select id="ratingFilter">
          <option value="">All</option>
          <option value="5">★★★★★</option>
          <option value="4">★★★★☆</option>
          <option value="3">★★★☆☆</option>
          <option value="2">★★☆☆☆</option>
          <option value="1">★☆☆☆☆</option>
      </select>

      <label for="dateFilter">Date Received:</label>
      <input type="date" id="dateFilter">
    </div>

    <div class="table-container">
      <table id="feedbackTable">
        <thead>
          <tr>
            <th>Client</th>
            <th>Caregiver</th>
            <th>Rating</th>
            <th>Comments</th>
            <th>Date Received</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($feedbacks)): ?>
            <?php foreach($feedbacks as $fb): ?>
              <tr>
                <td><?= $fb['client_name'] ?></td>
                <td><?= $fb['caretaker_name'] ?></td>

                <!-- Star rating -->
                <td class="stars" data-rating="<?= $fb['rating'] ?>">
                  <?= str_repeat("★", $fb['rating']) . str_repeat("☆", 5 - $fb['rating']) ?>
                </td>

                <td><?= $fb['comment'] ?></td>

                <td data-date="<?= $fb['created_at'] ?>">
                  <?= $fb['created_at'] ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No feedback found</td></tr>
          <?php endif; ?>
        </tbody>

      </table>

      <p id="noResults" style="display:none; text-align:center; margin-top:15px; font-weight:500; color:#ef4444;">
        No matching results found.
      </p>

    </div>
  </div>

  <!-- COMPLAINTS TAB (leave for your complaints module) -->
  <div id="complaintsSection" class="tab-section" style="display:none;">
      <div class="table-container">
          <table>
              <thead>
                  <tr>
                      <th>Client</th>
                      <th>Caregiver</th>
                      <th>Complaint</th>
                      <th>Date Filed</th>
                      <th>Status</th>
                  </tr>
              </thead>
              <tbody>
                  <?php include_once APPROOT."/views/admin/complaints_dummy_or_dynamic.php"; ?>
              </tbody>
          </table>
      </div>
  </div>

</div>

<script src="<?php echo URLROOT; ?>/public/js/admin/ad_feedback.js"></script>
</body>
</html>
