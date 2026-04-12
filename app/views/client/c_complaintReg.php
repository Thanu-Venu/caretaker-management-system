<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Complaint</title>
    <link rel="stylesheet" href="<?= URLROOT ?>/public/css/client/c_complaintReg.css">
</head>

<body>
    <div class="page-wrapper">
        <div class="complaint-container">
            <div class="complaint-section">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                    <h2 style="margin:0;">Register a Complaint</h2>
                    <a href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintlist" style="font-weight:600;">View Complaint List</a>
                </div>

                <form action="<?= URLROOT ?>/public/index.php?url=Complaint/store" method="POST" style="margin-top: 36px;">
                    <label>Client Name</label>
                    <input type="text" name="client_name" value="<?= htmlspecialchars($_SESSION['user']['name']) ?>" readonly>

                    <label>Caretaker</label>
                    <select name="caretaker_name" required>
                        <option value="">Select Caregiver</option>
                        <?php foreach (($caretakers ?? []) as $caretaker): ?>
                            <option value="<?= htmlspecialchars($caretaker['name']) ?>">
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
    </div>
</body>

</html>
