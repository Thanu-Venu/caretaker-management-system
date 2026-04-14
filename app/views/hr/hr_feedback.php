<?php
$feedbacks = $feedbacks ?? [];
$hrPageTitle = 'Client Feedback — HR';
$hrExtraCss  = ['hr/hr_feedback.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content">
  <header class="feedback-header page-header">
    <h1 class="page-title">Client feedback</h1>
  </header>

  <div class="table-container">
    <table class="table">
      <thead>
        <tr>
          <th>Client</th>
          <th>Caregiver</th>
          <th>Service</th>
          <th>Rating</th>
          <th>Comments</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($feedbacks)): ?>
          <?php foreach ($feedbacks as $fb): ?>
            <tr>
              <td><?= htmlspecialchars((string) ($fb['client_name'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($fb['caretaker_name'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($fb['service'] ?? 'N/A')) ?></td>
              <td>
                <span class="feedback-stars" aria-label="Rating <?= (int) ($fb['rating'] ?? 0) ?> of 5">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= (int) ($fb['rating'] ?? 0)): ?>
                      <i class="fa-solid fa-star" aria-hidden="true"></i>
                    <?php else: ?>
                      <i class="fa-regular fa-star feedback-star--off" aria-hidden="true"></i>
                    <?php endif; ?>
                  <?php endfor; ?>
                </span>
              </td>
              <td><?= htmlspecialchars((string) ($fb['comment'] ?? $fb['feedback'] ?? '')) ?></td>
              <td><?= htmlspecialchars((string) ($fb['created_at'] ?? '')) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="empty">No feedback available</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
