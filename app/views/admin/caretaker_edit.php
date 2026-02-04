<?php  
include_once APPROOT . "/views/templates/admin/ad_header.php"; 
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
    <h1>Edit Caregiver</h1>
    <form action="<?php echo URLROOT; ?>/CaretakerCRUD/edit/<?php echo $caretaker['id']; ?>" 
      method="POST" enctype="multipart/form-data" class="caretaker-form">

  <div class="form-grid">

    <div class="field">
      <label>Name</label>
      <input type="text" name="name"
        value="<?= htmlspecialchars($caretaker['name']); ?>" required>
    </div>

    <div class="field">
      <label>Email</label>
      <input type="email" name="email"
        value="<?= htmlspecialchars($caretaker['email']); ?>" required>
    </div>

    <div class="field">
      <label>Phone</label>
      <input type="text" name="phone"
        value="<?= htmlspecialchars($caretaker['phone']); ?>" required>
    </div>

    <div class="field">
      <label>Experience</label>
      <input type="text" name="experience"
        value="<?= htmlspecialchars($caretaker['experience']); ?>" required>
    </div>

    <div class="field">
      <label>Location</label>
      <input type="text" name="location"
        value="<?= htmlspecialchars($caretaker['location']); ?>" required>
    </div>

    <div class="field full">
      <label>Qualifications</label>
      <input type="text" name="qualifications"
        value="<?= htmlspecialchars($caretaker['qualifications']); ?>" required>
    </div>

    <div class="field">
      <label>Profile Picture</label>
      <input type="file" name="profile_image" accept="image/*">
    </div>

    <div class="field">
      <label>Service Type</label>
      <select name="service_type" required>
        <option value="Elder Care" <?= $caretaker['service_type']=='Elder Care' ? 'selected' : '' ?>>Elder Care</option>
        <option value="Maid" <?= $caretaker['service_type']=='Maid' ? 'selected' : '' ?>>Maid</option>
        <option value="Babysitter" <?= $caretaker['service_type']=='Babysitter' ? 'selected' : '' ?>>Babysitter</option>
      </select>
    </div>

    <div class="field">
      <label>Status</label>
      <select name="status" required>
        <option value="Active" <?= $caretaker['status']=='Active' ? 'selected' : '' ?>>Active</option>
        <option value="Inactive" <?= $caretaker['status']=='Inactive' ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>

  </div>

  <div class="form-actions">
    <button type="submit" class="submit-btn">Update Caregiver</button>
    <a href="<?= URLROOT ?>/CaretakerCRUD/list" class="btn-cancel">Cancel</a>
  </div>

</form>

  </section>
</main>
</body>
</html>