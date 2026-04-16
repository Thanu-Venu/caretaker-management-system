
<?php
$caretakerPageTitle = 'Caretaker Feedback - SmartCare';
$caretakerExtraCss = ['caretaker/ct_reviews.css'];
require_once APPROOT . '/views/templates/caretaker/caretaker_layout_head.php';
include_once APPROOT . '/views/templates/caretaker/ct_header.php';
include_once APPROOT . '/views/templates/caretaker/ct_sidebar.php';
$feedbackFilters = (isset($data['filters']) && is_array($data['filters'])) ? $data['filters'] : [];
$feedbackServiceOptions = (isset($data['serviceOptions']) && is_array($data['serviceOptions'])) ? $data['serviceOptions'] : [];
$selectedFeedbackService = trim((string) ($feedbackFilters['service'] ?? ''));
$selectedFeedbackRating = trim((string) ($feedbackFilters['rating'] ?? ''));
?>
<main class="content reviews-container">
  <header class="page-header">
    <h1 class="page-title">
        Client Feedback &amp; Ratings
        <?php if (isset($data['avgRating']) && $data['avgRating'] > 0): ?>
            <span style="font-size: 20px; color: #f39c12; margin-left: 15px;">
                Average: ⭐ <?= number_format($data['avgRating'], 1) ?>
            </span>
        <?php endif; ?>
    </h1>
  </header>  
  <div class="card">
    <form class="filter-section filters-inline ct-page-filters" method="get" action="<?= htmlspecialchars(URLROOT . '/public', ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="url" value="caretaker/ct_reviews">
      <div class="filter-group">
        <label for="feedbackServiceFilter">Service</label>
        <select id="feedbackServiceFilter" name="feedback_service">
          <option value="">All services</option>
          <?php foreach ($feedbackServiceOptions as $service): ?>
            <option value="<?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($selectedFeedbackService, (string) $service) === 0 ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $service, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-group">
        <label for="feedbackRatingFilter">Rating</label>
        <select id="feedbackRatingFilter" name="feedback_rating">
          <option value="">All ratings</option>
          <?php foreach (['5', '4', '3', '2', '1'] as $rating): ?>
            <option value="<?= $rating ?>" <?= $selectedFeedbackRating === $rating ? 'selected' : '' ?>><?= $rating ?> star</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-group filter-group--actions">
        <button type="submit" class="btn primary">Apply</button>
        <a class="btn ghost" href="<?= htmlspecialchars(URLROOT . '/public?url=caretaker/ct_reviews', ENT_QUOTES, 'UTF-8') ?>">Reset</a>
      </div>
    </form>

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
</main>

<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_review.js"></script>

<?php require_once APPROOT . '/views/templates/caretaker/caretaker_layout_close.php'; ?>
