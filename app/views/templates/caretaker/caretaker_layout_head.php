<?php
/**
 * Caretaker shell - shared document head for consistent caretaker pages.
 * Before including:
 * - $caretakerPageTitle (string)
 * - $caretakerExtraCss (array paths under public/css/)
 * - $caretakerHeadStylesheets (array full stylesheet URLs, optional)
 * - $caretakerHeadScripts (array full script URLs for <head>, optional)
 */
if (!isset($caretakerPageTitle)) {
    $caretakerPageTitle = 'SmartCare - Caretaker';
}
if (!isset($caretakerExtraCss) || !is_array($caretakerExtraCss)) {
    $caretakerExtraCss = [];
}
if (!isset($caretakerHeadStylesheets) || !is_array($caretakerHeadStylesheets)) {
    $caretakerHeadStylesheets = [];
}
if (!isset($caretakerHeadScripts) || !is_array($caretakerHeadScripts)) {
    $caretakerHeadScripts = [];
}
$caretakerBodyClass = isset($caretakerBodyClass) ? (string) $caretakerBodyClass : '';
$caretakerBodyData = (isset($caretakerBodyData) && is_array($caretakerBodyData)) ? $caretakerBodyData : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars((string) $caretakerPageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_header.css">
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/ct_sidebar.css">
  <?php foreach ($caretakerHeadStylesheets as $caretakerHeadSheet): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars((string) $caretakerHeadSheet, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <?php foreach ($caretakerExtraCss as $cssRel): ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/<?= htmlspecialchars((string) $cssRel, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/caretaker/caretaker-post-admin.css">
  <?php foreach ($caretakerHeadScripts as $headScript): ?>
  <script src="<?= htmlspecialchars((string) $headScript, ENT_QUOTES, 'UTF-8') ?>"></script>
  <?php endforeach; ?>
</head>
<body<?php 
if ($caretakerBodyClass !== '') echo ' class="' . htmlspecialchars($caretakerBodyClass, ENT_QUOTES, 'UTF-8') . '"';
foreach ($caretakerBodyData as $dataKey => $dataVal) {
    echo ' data-' . htmlspecialchars($dataKey, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $dataVal, ENT_QUOTES, 'UTF-8') . '"';
}
?>>
