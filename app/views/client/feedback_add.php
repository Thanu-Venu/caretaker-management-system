<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_feedback_form.css">
</head>
<body>

<div class="main-content">
    <h2>Give Feedback</h2>

    <form action="index.php?url=feedback/store" method="POST" class="feedback-form">

        <label>Caretaker ID</label>
        <input type="number" name="caretaker_id" required>

        <label>Rating</label>
        <select name="rating" required>
            <option value="">Select</option>
            <?php for($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <label>Comment</label>
        <textarea name="comment" required></textarea>

        <button class="submit-btn">Submit</button>
    </form>
</div>

</body>
</html>

