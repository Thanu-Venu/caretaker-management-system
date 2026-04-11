<?php
$users = $data['users'] ?? [];
$openModal = $data['openModal'] ?? '';
$editUser = $data['editUser'] ?? null;
$eu = is_array($editUser) ? $editUser : [];

/**
 * Safe JSON for data-staff-user (no password).
 */
function admin_staff_user_public_json(array $row): string
{
    $keys = ['id', 'username', 'email', 'phone', 'role', 'status'];
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
} elseif ($openModal === 'edit' && !empty($eu['id'])) {
    $autoOpen = 'edit';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Roles and Access Control</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_users.css">
</head>

<body class="admin-staff-users-page" data-urlroot="<?php echo htmlspecialchars(URLROOT, ENT_QUOTES, 'UTF-8'); ?>"
  data-auto-open="<?php echo htmlspecialchars($autoOpen, ENT_QUOTES, 'UTF-8'); ?>">
  <?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
  <?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title">Staff Roles and Access Control</h1>
      <div class="header-actions">
        <button type="button" class="btn primary" id="staffOpenAddModal">Add staff member</button>
      </div>
    </div>

    <?php if ($flashSuccess !== ''): ?>
      <div class="success-message" role="status"><?php echo htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($flashSuccess === '' && $flashError !== '' && $autoOpen === ''): ?>
      <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="table-container">
      <table class="staff-users-table" data-table-collapse="off">
        <thead>
          <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['phone'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($user['status'] ?? 'N/A'); ?></td>
                <td class="actions">
                  <button type="button" class="btn secondary btn-sm action-view-btn action-view-btn--icon js-staff-user-edit"
                    data-staff-user="<?php echo admin_staff_user_public_json($user); ?>"
                    aria-label="Edit staff member" title="Edit">
                    <i class="bx bx-edit" aria-hidden="true"></i>
                  </button>
                  <a href="<?php echo URLROOT; ?>/userCRUD/delete/<?php echo (int) ($user['id'] ?? 0); ?>"
                    onclick="return confirm('Delete this staff member?');" title="Delete" class="staff-user-action-delete">
                    <i class="bx bx-trash" aria-hidden="true"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="empty" style="text-align:center;">No staff members found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <div id="staffAddModal" class="modal staff-user-form-modal" aria-hidden="true">
    <div class="modal-content staff-user-form-modal__content" role="dialog" aria-modal="true" aria-labelledby="staffAddTitle">
      <button type="button" class="modal-close staff-user-form-modal__close" data-close-staff-modal aria-label="Close">
        <i class="bx bx-x" aria-hidden="true"></i>
      </button>
      <h3 id="staffAddTitle">Add staff member</h3>
      <?php if ($addError && $flashError !== ''): ?>
        <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form id="staffAddForm" method="POST" class="user-form" action="<?php echo URLROOT; ?>/userCRUD/add">
        <?php
        $mode = 'add';
        $user = [];
        $fieldPrefix = 'staff-add';
        include APPROOT . '/views/admin/partials/staff_user_form_inner.php';
        ?>
        <div class="form-actions">
          <button type="submit" class="submit-btn btn primary">Add staff member</button>
          <button type="button" class="btn ghost" data-close-staff-modal>Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div id="staffEditModal" class="modal staff-user-form-modal" aria-hidden="true">
    <div class="modal-content staff-user-form-modal__content" role="dialog" aria-modal="true" aria-labelledby="staffEditTitle">
      <button type="button" class="modal-close staff-user-form-modal__close" data-close-staff-modal aria-label="Close">
        <i class="bx bx-x" aria-hidden="true"></i>
      </button>
      <h3 id="staffEditTitle">Edit staff member</h3>
      <?php if ($editError && $flashError !== ''): ?>
        <div class="error-message" role="alert"><?php echo htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>
      <form id="staffEditForm" method="POST" class="user-form"
        action="<?php echo URLROOT; ?>/userCRUD/edit/<?php echo (int) ($eu['id'] ?? 0); ?>">
        <?php
        $mode = 'edit';
        $user = $eu;
        $fieldPrefix = 'staff-edit';
        include APPROOT . '/views/admin/partials/staff_user_form_inner.php';
        ?>
        <div class="form-actions">
          <button type="submit" class="submit-btn btn primary">Save changes</button>
          <button type="button" class="btn ghost" data-close-staff-modal>Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_users.js"></script>
</body>

</html>
