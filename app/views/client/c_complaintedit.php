<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Complaint</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_complaintedit.css">
</head>

<body>
<div class="main-content">
    <h2>Edit My Complaint</h2>

    <?php if (!empty($success)): ?>
        <p class="success-msg"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" action="<?= URLROOT ?>/index.php?url=Complaint/clientUpdate/<?= $complaint['Id'] ?>">
        <input type="hidden" name="Id" value="<?= $complaint['Id'] ?>">

        <label>Details</label>
        <textarea name="details" required><?= htmlspecialchars($complaint['details']) ?></textarea>

        <button type="submit">Update Complaint</button>
    </form>
</div>
</body>
</html>
