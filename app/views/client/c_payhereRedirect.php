<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayHere</title>
</head>

<body>
    <div style="max-width: 680px; margin: 40px auto; padding: 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; font-family: Arial, Helvetica, sans-serif;">
        <h2>Redirecting to PayHere Sandbox...</h2>
        <p>Please wait while we connect to the secure payment page.</p>

        <form id="payhereForm" method="post" action="<?= htmlspecialchars((string)($gateway_url ?? '')) ?>">
            <?php $fields = $payhere ?? []; ?>
            <?php foreach ($fields as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars((string)$key) ?>" value="<?= htmlspecialchars((string)$value) ?>">
            <?php endforeach; ?>
            <noscript><button type="submit">Continue to PayHere</button></noscript>
        </form>
    </div>

    <script>
        document.getElementById('payhereForm').submit();
    </script>
</body>

</html>
