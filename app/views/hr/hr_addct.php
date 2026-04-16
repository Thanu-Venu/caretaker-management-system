
<?php
$caretakers = $data['caretakers'] ?? [];
$filters = $data['filters'] ?? [];
$page = (int) ($data['page'] ?? 1);
$totalPages = (int) ($data['totalPages'] ?? 1);
$openModal = $data['openModal'] ?? '';
$editCaretaker = $data['editCaretaker'] ?? null;

$ec = is_array($editCaretaker) ? $editCaretaker : [];

/**
 * @return string Safe HTML attribute value for JSON payload (no password).
 */
function hr_caretaker_public_json(array $row): string
{
    $keys = ['id', 'name', 'email', 'phone', 'service_type', 'experience', 'location', 'qualifications', 'status', 'profile_image', 'created_at'];
    $out = [];
    foreach ($keys as $k) {
        if (array_key_exists($k, $row)) {
            $out[$k] = $row[$k];
        }
    }

    return htmlspecialchars(json_encode($out, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
}

$addError = ($openModal === 'add' && !empty($_SESSION['error']));
$editError = ($openModal === 'edit' && !empty($_SESSION['error']));
$flashError = $addError || $editError ? (string) $_SESSION['error'] : '';
if ($addError || $editError) {
    unset($_SESSION['error']);
}

$flashSuccess = '';
if (!empty($_SESSION['success'])) {
    $flashSuccess = (string) $_SESSION['success'];
    unset($_SESSION['success']);
}

$autoOpen = '';
if ($openModal === 'add') {
    $autoOpen = 'add';
} elseif ($openModal === 'edit' && !empty($ec['id'])) {
    $autoOpen = 'edit';
}

$paginationQuery = static function (int $p) use ($filters): string {
    $q = array_merge(['page' => $p], $filters);

    return http_build_query($q);
};

$hrPageTitle = 'Caregiver Management';
$hrExtraCss = ['hr/hr_caretakers.css'];
$hrBodyClass = 'carePage';
$hrBodyData = [
    'urlroot' => URLROOT,
    'auto_open' => $autoOpen,
    'caretaker_crud_base' => URLROOT . '/HRCaretakerCRUD',
];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content">

  <section class="caretaker-header page-header">
    <h1 class="page-title">Caregiver Management</h1>
    <div class="header-actions">
      <button type="button" class="btn primary" id="caretakerOpenAddModal">Add Caregiver</button>
    </div>
  </section>

  <?php if ($flashSuccess !== ''): ?>
    <div class="success-message" role="status"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php endif; ?>

  <?php if ($flashSuccess === '' && $flashError !== '' && $autoOpen === ''): ?>
    <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
  <?php endif; ?>

  <div class="filter-bar">
    <form method="get" action="<?= URLROOT ?>/HRCaretakerCRUD/list">

      <select name="service_type">
        <option value="">All Services</option>
        <option value="Elder Care" <?= (($filters['service_type'] ?? '') === 'Elder Care') ? 'selected' : '' ?>>Elder Care</option>
        <option value="Babysitter" <?= (($filters['service_type'] ?? '') === 'Babysitter') ? 'selected' : '' ?>>Babysitter</option>
        <option value="Maid" <?= (($filters['service_type'] ?? '') === 'Maid') ? 'selected' : '' ?>>Maid</option>
      </select>

      <select name="status">
        <option value="">All Status</option>
        <option value="Active" <?= (($filters['status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= (($filters['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>

      <input type="text" name="location" placeholder="Location"
        value="<?= htmlspecialchars($filters['location'] ?? '') ?>">

      <input type="text" name="q" placeholder="Search name"
        value="<?= htmlspecialchars($filters['q'] ?? '') ?>">

      <button type="submit">Apply</button>

      <a class="reset-btn" href="<?= URLROOT ?>/HRCaretakerCRUD/list">Reset</a>
    </form>
  </div>

  <div class="table-container">
    <table class="caretakers-table" data-table-collapse="off">
      <thead>
        <tr>
          <th>Name</th>
          <th>Service type</th>
          <th>Experience</th>
          <th>Location</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($caretakers)): ?>
          <?php foreach ($caretakers as $caretaker): ?>
            <tr>
              <td><?= htmlspecialchars($caretaker['name']) ?></td>
              <td><?= htmlspecialchars($caretaker['service_type'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['experience'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['location'] ?? '') ?></td>
              <td>
                <span class="status <?= ($caretaker['status'] ?? '') === 'Active' ? 'active' : 'inactive' ?>">
                  <?= htmlspecialchars($caretaker['status'] ?? '') ?>
                </span>
              </td>
              <td class="actions">
                <button type="button" class="btn secondary btn-sm iconBtn cgView"
                  data-caretaker="<?= hr_caretaker_public_json($caretaker) ?>"
                  aria-label="View caregiver details including qualifications" title="View details">
                  <i class="bx bx-show" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn secondary btn-sm iconBtn cgEdit"
                  data-caretaker="<?= hr_caretaker_public_json($caretaker) ?>"
                  aria-label="Edit caregiver" title="Edit">
                  <i class="bx bx-edit" aria-hidden="true"></i>
                </button>
                <a href="<?php echo URLROOT; ?>/HRCaretakerCRUD/delete/<?php echo (int) $caretaker['id']; ?>"
                  onclick="return confirm('Are you sure you want to delete this caregiver?');" title="Delete"
                  class="trashLink"><i class="bx bx-trash" aria-hidden="true"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="empty" style="text-align:center;">No caregivers found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a class="<?= ($p === $page) ? 'active' : '' ?>"
            href="<?= URLROOT ?>/HRCaretakerCRUD/list?<?= htmlspecialchars($paginationQuery($p), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>

</main>

<div id="caretakerDetailModal" class="modal readModal" aria-hidden="true">
  <div class="modal-content readPanel narrowRead" role="dialog" aria-modal="true"
    aria-labelledby="caretakerDetailTitle">
    <button type="button" class="modal-close readClose" data-close-caretaker-modal aria-label="Close">
      <i class="bx bx-x" aria-hidden="true"></i>
    </button>
    <header class="readHead">
      <span class="readIcon" aria-hidden="true"><i class="bx bx-show"></i></span>
      <h3 id="caretakerDetailTitle" class="readTitle">Caregiver details</h3>
    </header>
    <dl class="pairList" id="caretakerDetailDl"></dl>
  </div>
</div>

<div id="caretakerAddModal" class="modal wideFormModal" aria-hidden="true">
  <div class="modal-content wideFormPanel" role="dialog" aria-modal="true" aria-labelledby="caretakerAddTitle">
    <button type="button" class="modal-close wideFormClose" data-close-caretaker-modal aria-label="Close">
      <i class="bx bx-x" aria-hidden="true"></i>
    </button>
    <h3 id="caretakerAddTitle">Add caregiver</h3>
    <?php if ($addError && $flashError !== ''): ?>
      <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form id="caretakerAddForm" method="POST" class="caretaker-form" enctype="multipart/form-data"
      action="<?php echo URLROOT; ?>/HRCaretakerCRUD/add" data-admin-validate>
      <?php
      $mode = 'add';
      $caretaker = [];
      $fieldPrefix = 'caretaker-add';
      include APPROOT . '/views/admin/partials/caretaker_form_inner.php';
      ?>
      <div class="form-actions">
        <button type="submit" class="submit-btn btn primary">Add caregiver</button>
        <button type="button" class="btn ghost" data-close-caretaker-modal>Cancel</button>
      </div>
    </form>
  </div>
</div>

<div id="caretakerEditModal" class="modal wideFormModal" aria-hidden="true">
  <div class="modal-content wideFormPanel" role="dialog" aria-modal="true" aria-labelledby="caretakerEditTitle">
    <button type="button" class="modal-close wideFormClose" data-close-caretaker-modal aria-label="Close">
      <i class="bx bx-x" aria-hidden="true"></i>
    </button>
    <h3 id="caretakerEditTitle">Edit caregiver</h3>
    <?php if ($editError && $flashError !== ''): ?>
      <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form id="caretakerEditForm" method="POST" class="caretaker-form" enctype="multipart/form-data"
      action="<?php echo URLROOT; ?>/HRCaretakerCRUD/edit/<?php echo (int) ($ec['id'] ?? 0); ?>" data-admin-validate>
      <?php
      $mode = 'edit';
      $caretaker = $ec;
      $fieldPrefix = 'caretaker-edit';
      include APPROOT . '/views/admin/partials/caretaker_form_inner.php';
      ?>
      <div class="form-actions">
        <button type="submit" class="submit-btn btn primary">Save changes</button>
        <button type="button" class="btn ghost" data-close-caretaker-modal>Cancel</button>
      </div>
    </form>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/admin/ad_caretakers.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
