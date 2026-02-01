<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php
if (isset($_SESSION['flash_message'])) {
    echo "<script>alert('" . $_SESSION['flash_message'] . "');</script>";
    unset($_SESSION['flash_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Complaint</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_complaintReg.css">
</head>
<body>

<div class="page-wrapper">
    <div class="complaint-container">

        <!-- ================= REGISTER FORM ================= -->
        <div class="complaint-section">
            <h2>Register a Complaint</h2>

            <form action="<?= URLROOT ?>/index.php?url=Complaint/store" method="POST">

                <label>Client Name</label>
                <input type="text" name="client_name" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" readonly>

                <label>Caretaker Name</label>
                <input type="text" name="caretaker_name" required>

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

        <!-- ================= COMPLAINT LIST ================= -->
        <div class="complaint-section">
            <h2>My Complaints</h2>

            <?php if (!empty($complaints)): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>ID</th>
                        <th>Caretaker</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>

                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?= htmlspecialchars($complaint['Id']) ?></td>
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
                </table>
            <?php else: ?>
                <p>No complaints found.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>
