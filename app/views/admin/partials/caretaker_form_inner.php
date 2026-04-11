<?php
/**
 * Shared caregiver form fields (add / edit modals).
 *
 * @var string $mode 'add'|'edit'
 * @var array $caretaker Current values (edit); empty for add.
 * @var string $fieldPrefix Unique prefix for input ids (e.g. caretaker-add, caretaker-edit).
 */
if (!isset($mode)) {
    $mode = 'add';
}
$fp = $fieldPrefix ?? 'caretaker-field';
$c = $caretaker ?? [];
$val = static function (string $key, string $default = '') use ($c): string {
    return htmlspecialchars((string) ($c[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
?>
<div class="form-grid">
  <div class="field">
    <label for="<?= $fp ?>-name">Name</label>
    <input id="<?= $fp ?>-name" type="text" name="name" required placeholder="Full name" value="<?= $val('name'); ?>">
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
    <input id="<?= $fp ?>-phone" type="text" name="phone" required placeholder="10-digit phone" value="<?= $val('phone'); ?>">
  </div>

  <div class="field">
    <label for="<?= $fp ?>-experience">Experience</label>
    <input id="<?= $fp ?>-experience" type="text" name="experience" required placeholder="Experience" value="<?= $val('experience'); ?>">
  </div>

  <div class="field">
    <label for="<?= $fp ?>-location">Location</label>
    <input id="<?= $fp ?>-location" type="text" name="location" required placeholder="Location" value="<?= $val('location'); ?>">
  </div>

  <div class="field full">
    <label for="<?= $fp ?>-qualifications">Qualifications</label>
    <textarea id="<?= $fp ?>-qualifications" name="qualifications" required rows="3" placeholder="Qualifications, certifications, training"><?= $val('qualifications'); ?></textarea>
  </div>

  <div class="field">
    <label for="<?= $fp ?>-profile">Profile picture</label>
    <input id="<?= $fp ?>-profile" type="file" name="profile_image" accept="image/*">
    <?php if ($mode === 'edit'): ?>
      <p class="field-hint">Leave empty to keep the current photo.</p>
    <?php endif; ?>
  </div>

  <div class="field">
    <label for="<?= $fp ?>-service">Service type</label>
    <select id="<?= $fp ?>-service" name="service_type" required>
      <option value="">Select service</option>
      <option value="Elder Care" <?= (($c['service_type'] ?? '') === 'Elder Care') ? 'selected' : '' ?>>Elder Care</option>
      <option value="Maid" <?= (($c['service_type'] ?? '') === 'Maid') ? 'selected' : '' ?>>Maid</option>
      <option value="Babysitter" <?= (($c['service_type'] ?? '') === 'Babysitter') ? 'selected' : '' ?>>Babysitter</option>
    </select>
  </div>

  <div class="field">
    <label for="<?= $fp ?>-status">Status</label>
    <select id="<?= $fp ?>-status" name="status" required>
      <option value="Active" <?= (($c['status'] ?? 'Active') === 'Active') ? 'selected' : '' ?>>Active</option>
      <option value="Inactive" <?= (($c['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
    </select>
  </div>
</div>
