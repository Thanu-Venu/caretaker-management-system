<?php
if (!function_exists('admin_announcement_row_b64')) {
    /**
     * Compact row payload for edit modal (base64 JSON in data attribute).
     */
    function admin_announcement_row_b64(array $ann): string
    {
        $keys = ['id', 'title', 'message', 'target_role'];
        $out = [];
        foreach ($keys as $k) {
            if (array_key_exists($k, $ann)) {
                $out[$k] = is_scalar($ann[$k]) ? (string) $ann[$k] : '';
            }
        }
        $json = json_encode($out, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        return htmlspecialchars(base64_encode((string) $json), ENT_QUOTES, 'UTF-8');
    }
}

$filters = $data['filters'] ?? ['target_role' => '', 'date_from' => '', 'date_to' => '', 'q' => ''];
$currentPage = (int)($data['currentPage'] ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$totalRecords = (int)($data['totalRecords'] ?? 0);
$perPage = (int)($data['perPage'] ?? 10);
$listUrl = $data['listUrl'] ?? (URLROOT . '/public?url=admin/ad_announcement');
$filterFormAction = $data['filterFormAction'] ?? (URLROOT . '/public');
$filterFormHidden = $data['filterFormHidden'] ?? [['name' => 'url', 'value' => 'admin/ad_announcement']];

$openModal = trim((string)($data['openModal'] ?? ''));
$editAnnouncement = is_array($data['editAnnouncement'] ?? null) ? $data['editAnnouncement'] : [];
$hasEditDeepLink = $openModal === 'edit' && !empty($editAnnouncement['id']);

$ft = $filters['target_role'] ?? '';
$df = $filters['date_from'] ?? '';
$dt = $filters['date_to'] ?? '';
$fq = $filters['q'] ?? '';

$flashAnn = '';
if (!empty($_SESSION['flash_message'])) {
    $flashAnn = (string) $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

/** @return string */
$ann_preview = static function (string $text, int $max = 96): string {
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $max) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $max - 1)) . '…';
    }
    if (strlen($text) <= $max) {
        return $text;
    }
    return rtrim(substr($text, 0, $max - 3)) . '…';
};

$buildAnnListQuery = static function (array $hidden, array $filters, int $page) use ($filterFormAction): string {
    $q = [];
    foreach ($hidden as $h) {
        $q[$h['name']] = $h['value'];
    }
    foreach (['target_role', 'date_from', 'date_to', 'q'] as $k) {
        $v = (string)($filters[$k] ?? '');
        if ($v !== '') {
            $q[$k] = $v;
        }
    }
    if ($page > 1) {
        $q['page'] = $page;
    }
    $qs = http_build_query($q);

    return $filterFormAction . ($qs !== '' ? '?' . $qs : '');
};

$editFormAction = URLROOT . '/AnnouncementCRUD/edit/' . ($hasEditDeepLink ? (int) $editAnnouncement['id'] : 0);
$editSubmitDisabled = !$hasEditDeepLink;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_announcement.css">
</head>

<body>
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

  <main class="main-content announcement-admin-page" data-urlroot="<?= htmlspecialchars(URLROOT, ENT_QUOTES, 'UTF-8') ?>">
    <div class="page-header">
      <h1 class="page-title">Announcements</h1>
      <div class="header-actions">
        <button type="button" class="btn primary" id="annOpenAddModal">Add announcement</button>
      </div>
    </div>

    <?php if ($flashAnn !== ''): ?>
      <div class="success-message" role="status"><?= htmlspecialchars($flashAnn, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="get" action="<?= htmlspecialchars($filterFormAction, ENT_QUOTES, 'UTF-8') ?>" class="filter-section announcement-filters" id="announcementFiltersForm">
      <?php foreach ($filterFormHidden as $h): ?>
        <input type="hidden" name="<?= htmlspecialchars($h['name'], ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string)$h['value'], ENT_QUOTES, 'UTF-8') ?>">
      <?php endforeach; ?>
      <input type="hidden" name="page" value="1">

      <div class="filter-group">
        <label for="ann-filter-target">Audience</label>
        <select id="ann-filter-target" name="target_role">
          <option value="" <?= $ft === '' ? 'selected' : '' ?>>All audiences</option>
          <option value="All" <?= $ft === 'All' ? 'selected' : '' ?>>All (broadcast)</option>
          <option value="users" <?= $ft === 'users' ? 'selected' : '' ?>>Admin / HR</option>
          <option value="Caretaker" <?= $ft === 'Caretaker' ? 'selected' : '' ?>>Caretaker</option>
          <option value="Client" <?= $ft === 'Client' ? 'selected' : '' ?>>Client</option>
        </select>
      </div>

      <div class="filter-group">
        <label for="ann-filter-from">From date</label>
        <input type="date" id="ann-filter-from" name="date_from" value="<?= htmlspecialchars($df, ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="filter-group">
        <label for="ann-filter-to">To date</label>
        <input type="date" id="ann-filter-to" name="date_to" value="<?= htmlspecialchars($dt, ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="filter-group filter-group--grow">
        <label for="ann-filter-q">Search</label>
        <input type="search" id="ann-filter-q" name="q" value="<?= htmlspecialchars($fq, ENT_QUOTES, 'UTF-8') ?>" placeholder="Title, message, or author">
      </div>

      <div class="filter-group filter-group--actions">
        <button type="submit" class="btn secondary">Apply filters</button>
        <a class="btn ghost" href="<?= htmlspecialchars($listUrl, ENT_QUOTES, 'UTF-8') ?>">Reset</a>
      </div>
    </form>

    <div class="table-container">
      <table class="announcements-table" data-table-collapse="off">
        <thead>
          <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Target</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($data['announcements'])): ?>
            <?php foreach ($data['announcements'] as $ann): ?>
              <?php
              $title = (string)($ann['title'] ?? '');
              $msg = (string)($ann['message'] ?? '');
              $by = (string)($ann['created_by_name'] ?? '');
              ?>
              <tr>
                <td class="ann-title-cell">
                  <div class="ann-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
                  <?php if ($by !== ''): ?>
                    <div class="ann-by">By <?= htmlspecialchars($by, ENT_QUOTES, 'UTF-8') ?></div>
                  <?php endif; ?>
                </td>
                <td class="ann-msg-cell" title="<?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ann_preview($msg), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($ann['target_role'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="ann-date-cell"><?php
                  $raw = (string)($ann['created_at'] ?? '');
                  if ($raw === '') {
                      echo '';
                  } else {
                      $ts = strtotime($raw);
                      echo htmlspecialchars(
                          $ts !== false ? date('M j, Y · g:i A', $ts) : $raw,
                          ENT_QUOTES,
                          'UTF-8'
                      );
                  }
                ?></td>
                <td class="actions">
                  <button type="button" class="btn secondary btn-sm action-view-btn action-view-btn--icon"
                    title="Edit" aria-label="Edit announcement"
                    data-ann-b64="<?= admin_announcement_row_b64($ann) ?>">
                    <i class="bx bx-edit" aria-hidden="true"></i>
                  </button>
                  <a href="<?= URLROOT ?>/AnnouncementCRUD/delete/<?= (int)($ann['id'] ?? 0) ?>"
                    onclick="return confirm('Delete this announcement?');" title="Delete" aria-label="Delete announcement">
                    <i class="bx bx-trash" aria-hidden="true"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="empty">No announcements match your filters.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <?php if ($totalRecords > 0): ?>
        <div class="pagination">
          <?php if ($currentPage > 1): ?>
            <a href="<?= htmlspecialchars($buildAnnListQuery($filterFormHidden, $filters, $currentPage - 1), ENT_QUOTES, 'UTF-8') ?>">Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= htmlspecialchars($buildAnnListQuery($filterFormHidden, $filters, $i), ENT_QUOTES, 'UTF-8') ?>"
              class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($currentPage < $totalPages): ?>
            <a href="<?= htmlspecialchars($buildAnnListQuery($filterFormHidden, $filters, $currentPage + 1), ENT_QUOTES, 'UTF-8') ?>">Next</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($totalRecords > 0): ?>
      <p class="table-list-meta">Showing <?= (int)(($currentPage - 1) * $perPage + 1) ?>–<?= (int)min($currentPage * $perPage, $totalRecords) ?> of <?= $totalRecords ?></p>
    <?php endif; ?>
  </main>

  <?php if ($openModal === 'add' || ($openModal === 'edit' && $hasEditDeepLink)): ?>
    <script type="application/json" id="annDeepLinkPayload"><?php
      $payload = ['open' => $openModal === 'add' ? 'add' : 'edit'];
      if ($openModal === 'edit' && $hasEditDeepLink) {
          $payload['row'] = [
              'id' => (int) $editAnnouncement['id'],
              'title' => (string)($editAnnouncement['title'] ?? ''),
              'message' => (string)($editAnnouncement['message'] ?? ''),
              'target_role' => (string)($editAnnouncement['target_role'] ?? 'All'),
          ];
      }
      echo json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    ?></script>
  <?php endif; ?>

  <!-- Add announcement modal -->
  <div id="annAddModal" class="modal announcement-form-modal" aria-hidden="true">
    <div class="modal-content announcement-form-modal__content" role="dialog" aria-modal="true" aria-labelledby="annAddTitle">
      <button type="button" class="modal-close announcement-form-modal__close" data-ann-modal-close aria-label="Close">
        <i class="bx bx-x" aria-hidden="true"></i>
      </button>
      <h3 id="annAddTitle">Add announcement</h3>
      <?php
      $formId = 'annAddForm';
      $formAction = URLROOT . '/AnnouncementCRUD/add';
      $fieldPrefix = 'ann-add';
      $row = [];
      $submitLabel = 'Add announcement';
      include APPROOT . '/views/admin/partials/announcement_modal_form.php';
      ?>
    </div>
  </div>

  <!-- Edit announcement modal -->
  <div id="annEditModal" class="modal announcement-form-modal" aria-hidden="true">
    <div class="modal-content announcement-form-modal__content" role="dialog" aria-modal="true" aria-labelledby="annEditTitle">
      <button type="button" class="modal-close announcement-form-modal__close" data-ann-modal-close aria-label="Close">
        <i class="bx bx-x" aria-hidden="true"></i>
      </button>
      <h3 id="annEditTitle">Edit announcement</h3>
      <?php
      $formId = 'annEditForm';
      $formAction = $editFormAction;
      $fieldPrefix = 'ann-edit';
      $row = $hasEditDeepLink ? $editAnnouncement : [];
      $submitLabel = 'Update announcement';
      $submitButtonId = 'annEditSubmit';
      $submitDisabled = $editSubmitDisabled;
      include APPROOT . '/views/admin/partials/announcement_modal_form.php';
      ?>
    </div>
  </div>

  <script src="<?= URLROOT ?>/public/js/admin/ad_announcements.js"></script>
</body>

</html>
