<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_feedback_table.css">
</head>
<body>

<div class="main-content">

    <h2>My Feedback</h2>

    <a href="index.php?url=feedback/create" class="btn-add">+ Add Feedback</a>

    <table class="table">
        <thead>
            <tr>
                <th>Caretaker</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($feedbacks as $fb): ?>
            <tr>
                <td><?= $fb['caretaker_name'] ?></td>
                <td><?= $fb['rating'] ?>/5</td>
                <td><?= $fb['comment'] ?></td>
                <td><?= $fb['created_at'] ?></td>

                <td>
                    <a href="index.php?url=feedback/edit/<?= $fb['id'] ?>" class="btn-edit">Edit</a>
                    <a href="index.php?url=feedback/delete/<?= $fb['id'] ?>" class="btn-delete"
                        onclick="return confirm('Delete this feedback?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>

</body>
</html>
