<?php
$clientPageTitle = 'Redirecting to PayHere — SmartCare';
$clientExtraCss  = ['client/c_payhereRedirect.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>
<main class="main-content">
    <div class="payhere-redirect-card">
        <h1 class="page-title">Redirecting to PayHere</h1>
        <p class="text-muted">Please wait while we connect you to the secure payment page.</p>
        <form id="payhereForm" method="post" action="<?= htmlspecialchars((string) ($gateway_url ?? '')) ?>">
            <?php $fields = $payhere ?? []; ?>
            <?php foreach ($fields as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars((string) $key) ?>" value="<?= htmlspecialchars((string) $value) ?>">
            <?php endforeach; ?>
            <noscript><button type="submit" class="btn">Continue to PayHere</button></noscript>
        </form>
    </div>
</main>
<script>
    document.getElementById('payhereForm').submit();
</script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
