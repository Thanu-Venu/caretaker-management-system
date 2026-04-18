<?php
/** Tab / PWA icon — same asset as header logo site-wide. */
$iconHref = rtrim(URLROOT, '/') . '/public/images/logo.webp';
$iconHref = htmlspecialchars($iconHref, ENT_QUOTES, 'UTF-8');
?>
<link rel="icon" href="<?= $iconHref ?>" type="image/webp" sizes="any">
<link rel="apple-touch-icon" href="<?= $iconHref ?>">
