<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintedit.css">
</head>
<body></body>
<div class="main-content">
<h2>Edit My Complaint</h2>

<form method="POST" action="<?= URLROOT ?>/index.php?url=Complaint/clientUpdate">
    <input type="hidden" name="Id" value="<?= $complaint['Id'] ?>">

    <label>Details:</label><br>
    <textarea name="details" required><?= htmlspecialchars($complaint['details']) ?></textarea><br><br>

    <button type="submit">Update Complaint</button>
</form>
</div>
