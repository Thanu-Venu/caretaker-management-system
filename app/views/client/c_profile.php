<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<?php
if (isset($data['user'])) {
    $user = $data['user'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_profile.css">
</head>
<body>
  <!-- Main content area -->
  <div class="main-content">
    <div class="card">
      <h2>Edit Profile</h2>
      <form id="profileForm">
        <div class="row">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" value="<?= htmlspecialchars($user['name']); ?>" readonly>
          </div>
          
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" readonly>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" id="address">
        </div>

        <div class="form-group">
          <label for="contact">Contact Number</label>
          <input type="text" id="contact">
        </div>

        <div class="row">
          <div class="form-group">
            <label for="city">City</label>
            <select id="city">
              <option selected>colombo</option>
              <option>jaffna</option>
              <option>matara</option>
              <option>vavuniya</option>
            </select>
          </div>
          
        </div>


        <div class="form-actions">
          <button type="button" class="btn btn-cancel">Cancel</button>
          <button type="submit" class="btn btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>