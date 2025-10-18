<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 


// unpack the data array into $caretaker so you can use it easily
$caretaker = $data['caretaker'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/caretaker_edit.css">
</head>
<body>
<main class="main-content">
  <section class="form-section">
    <h1>Edit Caretaker</h1>
    <form action="<?php echo URLROOT; ?>/CaretakerCRUD/edit/<?php echo $caretaker['id']; ?>" method="POST">
      
  <label for="name">Name</label>
  <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($caretaker['name']); ?>" required>

  <label for="email">Email</label>
  <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($caretaker['email']); ?>" required>

  <label for="phone">Phone</label>
  <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($caretaker['phone']); ?>" required>

  <label for="service_type">Service Type</label>
  <select name="service_type" id="service_type" required>
    <option value="Elder Care" <?php if($caretaker['service_type']=='Elder Care') echo 'selected'; ?>>Elder Care</option>
    <option value="Maid" <?php if($caretaker['service_type']=='Maid') echo 'selected'; ?>>Maid</option>
    <option value="Babysitter" <?php if($caretaker['service_type']=='Babysitter') echo 'selected'; ?>>Babysitter</option>
  </select>

  <label for="status">Status</label>
  <select name="status" id="status" required>
    <option value="Active" <?php if($caretaker['status']=='Active') echo 'selected'; ?>>Active</option>
    <option value="Inactive" <?php if($caretaker['status']=='Inactive') echo 'selected'; ?>>Inactive</option>
  </select>

  <button type="submit">Update Caretaker</button>
  <a href="<?php echo URLROOT; ?>/admin/ad_caretakers">Cancel</a>
</form>

  </section>
</main>
</body>
</html>