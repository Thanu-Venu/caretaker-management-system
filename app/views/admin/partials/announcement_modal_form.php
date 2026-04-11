<?php
/**
 * Announcement add/edit form fields for modals.
 *
 * @var string $formId
 * @var string $formAction POST action URL
 * @var string $fieldPrefix Unique id prefix (ann-add / ann-edit)
 * @var array $row Current row for edit; empty for add
 * @var string $submitLabel
 * @var string $cancelLabel
 */
$row = is_array($row ?? null) ? $row : [];
$fp = $fieldPrefix ?? 'ann-field';
$submitLabel = $submitLabel ?? 'Save';
$cancelLabel = $cancelLabel ?? 'Cancel';
$submitButtonId = isset($submitButtonId) ? (string) $submitButtonId : '';
$submitDisabled = !empty($submitDisabled);
$val = static function (string $key, string $default = '') use ($row): string {
    return htmlspecialchars((string) ($row[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
?>
<form id="<?= htmlspecialchars($formId, ENT_QUOTES, 'UTF-8') ?>" method="POST"
    action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>"
    class="announcement-form announcement-form--modal" data-admin-validate>

  <div class="form-grid announcement-form-grid">
    <div class="field full">
      <label for="<?= $fp ?>-title">Title<span class="required-mark" aria-hidden="true">*</span></label>
      <input id="<?= $fp ?>-title" type="text" name="title" required maxlength="200" placeholder="Announcement title" value="<?= $val('title'); ?>">
    </div>

    <div class="field full">
      <label for="<?= $fp ?>-message">Message<span class="required-mark" aria-hidden="true">*</span></label>
      <textarea id="<?= $fp ?>-message" name="message" rows="4" required maxlength="8000" placeholder="Announcement body"><?= $val('message'); ?></textarea>
    </div>

    <div class="field full">
      <label for="<?= $fp ?>-target">Target audience<span class="required-mark" aria-hidden="true">*</span></label>
      <select id="<?= $fp ?>-target" name="target_role" required>
        <?php
        $tr = (string) ($row['target_role'] ?? '');
        ?>
        <option value="All" <?= ($tr === '' || $tr === 'All') ? 'selected' : '' ?>>All</option>
        <option value="users" <?= $tr === 'users' ? 'selected' : '' ?>>Admin / HR</option>
        <option value="Caretaker" <?= $tr === 'Caretaker' ? 'selected' : '' ?>>Caretaker</option>
        <option value="Client" <?= $tr === 'Client' ? 'selected' : '' ?>>Client</option>
      </select>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="submit-btn btn primary"<?= $submitButtonId !== '' ? ' id="' . htmlspecialchars($submitButtonId, ENT_QUOTES, 'UTF-8') . '"' : '' ?><?= $submitDisabled ? ' disabled' : '' ?>><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
    <button type="button" class="btn ghost ann-modal-close" data-ann-modal-close><?= htmlspecialchars($cancelLabel, ENT_QUOTES, 'UTF-8') ?></button>
  </div>
</form>
