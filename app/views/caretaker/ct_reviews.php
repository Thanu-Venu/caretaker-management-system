<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_reviews.css">
</head>

<body>
<div class="main-content">
  <h2>Client Feedback & Ratings</h2>  
  <div class="card">

    <div class="feedback-table-container">
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