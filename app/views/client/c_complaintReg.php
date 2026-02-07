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


    </div>
</div>

</body>
</html>
