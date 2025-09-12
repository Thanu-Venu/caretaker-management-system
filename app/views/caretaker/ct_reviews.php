<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
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
          <!-- Dummy data -->
          <tr>
            <td>Mrs. Johnson</td>
            <td>Elder Care</td>
            <td>2025-08-20</td>
            <td>⭐ 5</td>
            <td>Excellent care, very patient and attentive!</td>
          </tr>
          <tr>
            <td>The Smith Family</td>
            <td>Babysitting</td>
            <td>2025-08-15</td>
            <td>⭐ 4</td>
            <td>Good service, arrived on time and friendly.</td>
          </tr>
          <tr>
            <td>Mr. Davis</td>
            <td>Cleaning & Maid</td>
            <td>2025-08-10</td>
            <td>⭐ 5</td>
            <td>Very professional and thorough with cleaning.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_reviews.js"></script>
</body>
</html>
