<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

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
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" value="Mehrab">
          </div>
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" value="Bozorgi">
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" value="mehrabbozorgi.business@gmail.com">
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" id="address" value="33062 Zboncak Isle">
        </div>

        <div class="form-group">
          <label for="contact">Contact Number</label>
          <input type="text" id="contact" value="58077.79">
        </div>

        <div class="row">
          <div class="form-group">
            <label for="city">City</label>
            <select id="city">
              <option selected>Mehrab</option>
              <option>Tehran</option>
              <option>London</option>
              <option>New York</option>
            </select>
          </div>
          <div class="form-group">
            <label for="state">State</label>
            <select id="state">
              <option selected>Bozorgi</option>
              <option>California</option>
              <option>Ontario</option>
              <option>Berlin</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" value="sbdlfbnd65sfdvb s">
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