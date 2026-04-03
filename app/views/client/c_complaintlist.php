<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php
if (isset($_SESSION['flash_message'])) {
    echo "<script>alert('" . addslashes($_SESSION['flash_message']) . "');</script>";
    unset($_SESSION['flash_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Complaints</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintlist.css">
</head>
<body>

<div class="main-content">

    <!-- title + button row -->
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
        <h1 style="margin:0;">Registered Complaints</h1>

        <button type="button" class="register-btn" onclick="openComplaintModal()">
            + Register Complaint
        </button>
    </div>

    <?php if (!empty($complaints)): ?>
        <div class="table-wrapper" style="margin-top:16px;">
            <table class="complaints-table">
                <thead>
                    <tr>
                        <th>Caregiver</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td><?= htmlspecialchars($complaint['caretaker_name']) ?></td>
                        <td><?= htmlspecialchars($complaint['category']) ?></td>
                        <td><?= htmlspecialchars($complaint['details']) ?></td>
                        <td><?= htmlspecialchars($complaint['status']) ?></td>
                        <td><?= htmlspecialchars($complaint['complaint_date']) ?></td>
                        <td>
                            <?php if ($complaint['status'] != 'Resolved' && $complaint['status'] != 'Closed'): ?>
                                <a href="<?= URLROOT ?>/index.php?url=Complaint/clientEdit/<?= $complaint['Id'] ?>">Edit</a>
                                |
                                <a href="<?= URLROOT ?>/index.php?url=Complaint/clientDelete/<?= $complaint['Id'] ?>"
                                   onclick="return confirm('Delete this complaint?')">Delete</a>
                            <?php else: ?>
                                <em>Locked</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="margin-top:12px;">No complaints found.</p>
    <?php endif; ?>

</div>



<div id="complaintModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width:650px;">
        <span class="close" onclick="closeComplaintModal()">&times;</span>
        <h2>Register a Complaint</h2>

        <form action="<?= URLROOT ?>/index.php?url=Complaint/store" method="POST">

            <label>Client Name</label>
            <input type="text" name="client_name"
                   value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" readonly>

            <label>Caretaker</label>
<select name="caretaker_id" required>
    <option value="">Select Caregiver</option>

    <?php foreach ($caretakers as $caretaker): ?>
        <option value="<?= $caretaker['name'] ?>">
            <?= htmlspecialchars($caretaker['name']) ?>
        </option>
    <?php endforeach; ?>

</select>

            <label>Complaint Category</label>
            <select name="category" required>
                <option value="">Choose a category</option>
                <option value="Caretaker Behavior">Caretaker Behavior</option>
                <option value="Service Quality">Service Quality</option>
                <option value="Late Arrival">Late Arrival</option>
                <option value="Unprofessional">Unprofessional</option>
                <option value="Other">Other</option>
            </select>

            <label>Complaint Description</label>
            <textarea name="details" rows="5" required></textarea>

            <button type="submit">Submit Complaint</button>
        </form>
    </div>
</div>




</body>
</html>