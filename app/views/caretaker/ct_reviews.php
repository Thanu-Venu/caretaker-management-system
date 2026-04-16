<?php
$caretakerPageTitle = 'Caretaker Feedback - SmartCare';
$caretakerExtraCss = ['caretaker/ct_reviews.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
?>
<main class="content reviews-container">
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

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
