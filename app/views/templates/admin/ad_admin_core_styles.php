<?php
/**
 * Core admin layout CSS (margins, header offset, tables, tokens).
 * Include once inside <head> on admin views. Fonts/icons were previously loaded
 * from a duplicate document in ad_header.php — they live here now.
 */
?>
<?php include_once APPROOT . '/views/partials/smartcare_favicon.php'; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= URLROOT ?>/public/vendor/font-awesome/css/all.min.css">
<link href="<?= URLROOT ?>/public/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/common/sidebar-badges.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_header.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/ad_sidebar.css">
<link rel="stylesheet" href="<?= URLROOT ?>/public/css/admin/admin-ui.css">
<script defer src="<?= URLROOT ?>/public/js/admin/admin-form-validation.js"></script>
