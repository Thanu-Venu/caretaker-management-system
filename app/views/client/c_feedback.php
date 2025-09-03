<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_feedback.css">
</head>
<body>
  <div class="app">
    <!-- Main Content -->
    <main class="content">
      <button id="openModalBtn">Give Feedback</button>
    </main>
  </div>

  <!-- Feedback Modal -->
  <div id="feedbackModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h3>Caretaker feedback</h3>
      <p>Please rate your experience below</p>

      <!-- Star Rating -->
      <div class="stars" id="stars">
        <span data-value="1">&#9733;</span>
        <span data-value="2">&#9733;</span>
        <span data-value="3">&#9733;</span>
        <span data-value="4">&#9733;</span>
        <span data-value="5">&#9733;</span>
      </div>
      <p id="ratingText">0/5 stars</p>

      <!-- Feedback Input -->
      <label for="feedback">Additional feedback</label>
      <textarea id="feedback" rows="3" placeholder="Write your feedback..."></textarea>

      <button id="submitBtn">Submit feedback</button>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_feedback.js"></script>
</body>
</html>