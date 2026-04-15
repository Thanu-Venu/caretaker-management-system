<?php
/**
 * Client shell — opens document and loads the same core styles as Admin/HR (single design system).
 * Before including: set $clientPageTitle (string), optional $clientExtraCss (paths under public/css/),
 * and optional $clientHeadStylesheets (array of full stylesheet URLs).
 */
if (!isset($clientPageTitle)) {
    $clientPageTitle = 'SmartCare — Client';
}
if (!isset($clientExtraCss) || !is_array($clientExtraCss)) {
    $clientExtraCss = [];
}
if (!isset($clientHeadStylesheets) || !is_array($clientHeadStylesheets)) {
    $clientHeadStylesheets = [];
}
$clientBodyClass = isset($clientBodyClass) ? (string) $clientBodyClass : '';
$clientBodyData = (isset($clientBodyData) && is_array($clientBodyData)) ? $clientBodyData : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars((string) $clientPageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <?php include_once APPROOT . '/views/templates/admin/ad_admin_core_styles.php'; ?>
  <?php foreach ($clientHeadStylesheets as $clientHeadSheet): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars((string) $clientHeadSheet, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <?php foreach ($clientExtraCss as $cssRel): ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/<?= htmlspecialchars((string) $cssRel, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/client-post-admin.css">
</head>
<body<?php
if ($clientBodyClass !== '') {
    echo ' class="' . htmlspecialchars($clientBodyClass, ENT_QUOTES, 'UTF-8') . '"';
}
foreach ($clientBodyData as $dataKey => $dataVal) {
    $k = strtolower(preg_replace('/[^a-z0-9_\-]/i', '', (string) $dataKey));
    $k = str_replace('_', '-', $k);
    if ($k === '') {
        continue;
    }
    echo ' data-' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $dataVal, ENT_QUOTES, 'UTF-8') . '"';
}
?>>
