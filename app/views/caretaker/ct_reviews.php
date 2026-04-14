<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_reviews.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>
<div class="main-content reviews-container">
  <header class="page-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="color: #1e88e5; font-size: 30px; font-weight: 700; margin: 0; letter-spacing: -0.02em;">
        Client Feedback & Ratings
        <?php if (isset($data['avgRating']) && $data['avgRating'] > 0): ?>
            <span style="font-size: 20px; color: #f39c12; margin-left: 15px;">
                Average: ⭐ <?= number_format($data['avgRating'], 1) ?>
            </span>
        <?php endif; ?>
    </h1>
  </header>  
  <div class="card">

    <div class="table-container">
      <table id="feedbackTable">
        <thead>
          <tr>
            <th>Client</th>
            <th>Service</th>
            <th>Date</th>
            <th>Rating</th>
            <th>Feedback</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['feedbacks'])) : ?>
            <?php foreach ($data['feedbacks'] as $fb) : ?>
                <tr>
                    <td><?= htmlspecialchars($fb['client_name']) ?></td>
                    <td><?= htmlspecialchars($fb['service']) ?></td>
                    <td><?= date('Y-m-d', strtotime($fb['created_at'])) ?></td>
                    <td>⭐ <?= htmlspecialchars($fb['rating']) ?></td>
                    <td><?= htmlspecialchars($fb['comment']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5">No feedback yet</td>
            </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_review.js"></script>

</body>
</html>