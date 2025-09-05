<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare - Edit Profile</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_profile.css">

<body>
  <div class="container">

    <!-- Main Content -->
    <main class="main-content">
      

      <section class="edit-profile">
        <div class="edit-header">
          <h1>Edit profile</h1>
          <img src="https://i.pravatar.cc/80" alt="Profile Picture" class="profile-pic">
        </div>
        <form>
          <div class="profile-header">
            <div class="form-group half">
              <label>First Name</label>
              <input type="text" value="Mehrab">
            </div>
            <div class="form-group half">
              <label>Last Name</label>
              <input type="text" value="Bozorgi">
            </div>
          </div>

          <div class="form-group">
            <label>Email</label>
            <div class="input-with-icon">
              <input type="email" value="Mehrabbozorgi.business@gmail.com">
              <i class="fa fa-check"></i>
            </div>
          </div>

          <div class="form-group">
            <label>Address</label>
            <input type="text" value="33062 Zboncak isle">
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" value="58077.79">
          </div>

          <div class="profile-header">
            <div class="form-group half">
              <label>City</label>
              <select>
                <option>Mehrab</option>
              </select>
            </div>
            <div class="form-group half">
              <label>State</label>
              <select>
                <option>Bozorgi</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Password</label>
            <div class="input-with-icon">
              <input type="password" value="sbdfbnd65sfdvb s">
              <i class="fa fa-check"></i>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="cancel-btn">Cancel</button>
            <button type="submit" class="save-btn">Save</button>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>