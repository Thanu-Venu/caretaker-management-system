<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_feedback_form.css">
</head>
<body>

<div class="main-content">
    <h2>Edit Feedback</h2>

    <form action="index.php?url=feedback/update/<?= $feedback['id'] ?>" method="POST" class="feedback-form">

        <label>Rating</label>
        <select name="rating">
            <?php for($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>" <?= ($feedback['rating']==$i ? 'selected':'') ?>>
                    <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>

        <label>Comment</label>
        <textarea name="comment"><?= $feedback['comment'] ?></textarea>

        <button class="submit-btn">Update</button>
    </form>
</div>

</body>
</html>
