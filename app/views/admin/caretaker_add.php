<?php  
include_once APPROOT . "/views/templates/admin/ad_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/caretaker_add.css">
</head>
<body>
<main class="main-content">
    <section class="form-section">
        <h1>Add Caretaker</h1>
        <form method="POST" class="caretaker-form">
            <label>Name</label>
            <input type="text" name="name" required placeholder="Enter full name">

            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter email">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>


            <label>Phone</label>
            <input type="text" name="phone" required placeholder="Enter phone number">

            <label>Service Type</label>
            <select name="service_type" required>
                <option value="">Select service</option>
                <option value="Elder Care">Elder Care</option>
                <option value="Maid">Maid</option>
                <option value="Babysitter">Babysitter</option>
            </select>

            <label>Status</label>
            <select name="status">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="submit" class="submit-btn" window.location.href='/CMA/public/caretakerCRUD/list'>Add Caretaker</button>
        </form>
    </section>
</main>
