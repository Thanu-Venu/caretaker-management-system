<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register a Complaint</title>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_complaintReg.css">
</head>
<body>
<div class="main-wrapper">
<div class="container">
<h2>Register a Complaint</h2>

<form action="/CMA/public/index.php?url=Complaint/store" method="POST">
    <label>Client Name:</label><br>
    <input type="text" name="client_name" required><br>

    <label>Caretaker Name:</label><br>
    <input type="text" name="caretaker_name" required><br>

    <label>Complaint Category:</label><br>
    <select name="category" required>
        <option value="">Choose a category</option>
        <option value="Caretaker Behavior">Caretaker Behavior</option>
        <option value="Service Quality">Service Quality</option>
        <option value="Late Arrival">Late Arrival</option>
        <option value="Unprofessional">Unprofessional</option>
        <option value="Other">Other</option>
    </select><br>

    <label>Complaint Description:</label><br>
    <textarea name="details" rows="5" required></textarea><br>

    <button type="submit">Submit Complaint</button>
</form>

</div>
</div>
</body>
</html>
