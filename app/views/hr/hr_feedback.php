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
    <table class="feedback-table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Caretaker</th>
                <th>Service</th>
                <th>Rating</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dummy data -->
            <tr>
                <td>John Doe</td>
                <td>Mary Smith</td>
                <td>Elder Care</td>
                <td>⭐⭐⭐⭐⭐</td>
                <td>Very attentive and professional!</td>
            </tr>
            <tr>
                <td>Jane Williams</td>
                <td>David Lee</td>
                <td>Babysitting</td>
                <td>⭐⭐⭐⭐</td>
                <td>Great with kids, but arrived a bit late.</td>
            </tr>
            <tr>
                <td>Michael Brown</td>
                <td>Lisa Johnson</td>
                <td>Cleaning & Cooking</td>
                <td>⭐⭐⭐⭐⭐</td>
                <td>Excellent service and very friendly.</td>
            </tr>
        </tbody>
    </table>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_feedback.js"></script>
</body>
</html>
