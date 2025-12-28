<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Requests</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_pending_request.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="main-content">
    <h1>Pending Service Requests</h1>
    <div class="table-container">
    <table class="requests-table">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Client</th>
                <th>Service</th>
                <th>Preferred Caretaker</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dummy data -->
            <tr>
                <td>101</td>
                <td>John Doe</td>
                <td>Elder Care</td>
                <td>Mary Smith</td>
                <td>2025-09-12 09:00-13:00</td>
                <td>Pending</td>
                <td>
                    <button class="approve">Approve</button>
                    <button class="reject">Reject</button>
                </td>
            </tr>
            <tr>
                <td>102</td>
                <td>Jane Williams</td>
                <td>Babysitting</td>
                <td>David Lee</td>
                <td>2025-09-13 14:00-18:00</td>
                <td>Pending</td>
                <td>
                    <button class="approve">Approve</button>
                    <button class="reject">Reject</button>
                </td>
            </tr>
            <tr>
                <td>103</td>
                <td>Michael Brown</td>
                <td>Cleaning & Cooking</td>
                <td>Lisa Johnson</td>
                <td>2025-09-14 10:00-15:00</td>
                <td>Pending</td>
                <td>
                    <button class="approve">Approve</button>
                    <button class="reject">Reject</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</div>

<!-- Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalText">Are you sure?</p>
        <div class="modal-actions">
            <button id="confirmYes" class="approve">Yes</button>
            <button id="confirmNo" class="reject">No</button>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_pending_request.js"></script>
</body>
</html>
