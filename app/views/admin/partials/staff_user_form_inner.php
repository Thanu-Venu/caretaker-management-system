<?php
/**
 * Staff user form fields (add / edit modals).
 *
 * @var string $mode 'add'|'edit'
 * @var array $user Row values for edit; empty for add.
 * @var string $fieldPrefix Unique id prefix (e.g. staff-add, staff-edit).
 */
if (!isset($mode)) {
    $mode = 'add';
}
$fp = $fieldPrefix ?? 'staff-field';
$u = $user ?? [];
$val = static function (string $key, string $default = '') use ($u): string {
    return htmlspecialchars((string) ($u[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
?>
<div class="form-grid">
  <div class="field">
    <label for="<?= $fp ?>-username">Username</label>
    <input id="<?= $fp ?>-username" type="text" name="username" required placeholder="Username" value="<?= $val('username'); ?>">
  </div>

  <div class="field">
    <label for="<?= $fp ?>-email">Email</label>
    <input id="<?= $fp ?>-email" type="email" name="email" required placeholder="Email" value="<?= $val('email'); ?>">
  </div>

  <?php if ($mode === 'add'): ?>
    <div class="field">
      <label for="<?= $fp ?>-password">Password</label>
      <input id="<?= $fp ?>-password" type="password" name="password" required placeholder="Password" autocomplete="new-password">
    </div>
  <?php endif; ?>

  <div class="field">
    <label for="<?= $fp ?>-phone">Phone</label>
    <input id="<?= $fp ?>-phone" type="text" name="phone" <?= $mode === 'add' ? 'required' : ''; ?> placeholder="Phone number" value="<?= $val('phone'); ?>">
  </div>

  <div class="field">
    <label for="<?= $fp ?>-role">Role</label>
    <select id="<?= $fp ?>-role" name="role" required>
      <option value="">Select role</option>
      <option value="Admin" <?= (($u['role'] ?? '') === 'Admin' || ($u['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
      <option value="Manager" <?= (($u['role'] ?? '') === 'Manager') ? 'selected' : '' ?>>Manager</option>
    </select>
  </div>

  <div class="field">
    <label for="<?= $fp ?>-status">Status</label>
    <select id="<?= $fp ?>-status" name="status" required>
      <option value="Active" <?= (($u['status'] ?? 'Active') === 'Active') ? 'selected' : '' ?>>Active</option>
      <option value="Inactive" <?= (($u['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
    </select>
  </div>
</div>
