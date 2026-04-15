<?php
/**
 * HR shell — opens document and loads HR-owned core styles (see hr_core_styles.php).
 * Before including: set $hrPageTitle (string), optional $hrExtraCss (paths under public/css/),
 * and optional $hrHeadStylesheets (array of full stylesheet URLs, e.g. FullCalendar CDN).
 */
if (!isset($hrPageTitle)) {
    $hrPageTitle = 'SmartCare — HR';
}
if (!isset($hrExtraCss) || !is_array($hrExtraCss)) {
    $hrExtraCss = [];
}
/** Optional extra stylesheets (full URLs), e.g. FullCalendar CDN — emitted in head before page CSS. */
if (!isset($hrHeadStylesheets) || !is_array($hrHeadStylesheets)) {
    $hrHeadStylesheets = [];
}
$hrBodyClass = isset($hrBodyClass) ? (string) $hrBodyClass : '';
$hrBodyData = (isset($hrBodyData) && is_array($hrBodyData)) ? $hrBodyData : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars((string) $hrPageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <?php include_once APPROOT . '/views/templates/hr/hr_core_styles.php'; ?>
  <?php foreach ($hrHeadStylesheets as $hrHeadSheet): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars((string) $hrHeadSheet, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
  <?php foreach ($hrExtraCss as $cssRel): ?>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/<?= htmlspecialchars((string) $cssRel, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>
</head>
<body<?php
if ($hrBodyClass !== '') {
    echo ' class="' . htmlspecialchars($hrBodyClass, ENT_QUOTES, 'UTF-8') . '"';
}
foreach ($hrBodyData as $dataKey => $dataVal) {
    $k = strtolower(preg_replace('/[^a-z0-9_\-]/i', '', (string) $dataKey));
    $k = str_replace('_', '-', $k);
    if ($k === '') {
        continue;
    }
    echo ' data-' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $dataVal, ENT_QUOTES, 'UTF-8') . '"';
}
?>>
