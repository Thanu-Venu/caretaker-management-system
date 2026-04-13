<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Feedback</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_feedback.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="main-content">
    <h1>Client Feedback</h1>

    <div class="table-container">
        <table class="feedback-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Caregiver</th>
                    <th>Service</th>
                    <th>Rating</th>
                    <th>Comments</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($feedbacks)): ?>
                    <?php foreach ($feedbacks as $fb): ?>
                        <tr>
                            <td><?= $fb['client_name'] ?></td>
                            <td><?= $fb['caretaker_name'] ?></td>
                            <td><?= $fb['service'] ?? 'N/A' ?></td>

                            <!-- Star Rendering -->
                            <td>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $fb['rating']): ?>
                                        <i class="fa-solid fa-star" style="color:#f4c542;"></i>
                                    <?php else: ?>
                                        <i class="fa-regular fa-star" style="color:#ccc;"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </td>

                            <td><?= $fb['comment'] ?></td>
                            <td><?= $fb['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#999;">No feedback available</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_feedback.js"></script>

</body>
</html>
