<?php
$ct = $data['caretaker'];
?>
<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Profile</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_ctprofileview.css">
</head>

<body>
<main class="content">
  <div class="profile-card">

    <!-- Profile Header -->
    <div class="profile-header">
      <img 
        src="<?= URLROOT ?>/uploads/<?= htmlspecialchars($ct['profile_image']) ?>"
        alt="<?= htmlspecialchars($ct['name']) ?>"
        class="profile-img1"
        onerror="this.src='<?= URLROOT ?>/uploads/default.png';"
      >

      <div>
        <h1><?= htmlspecialchars($ct['name']) ?></h1>
        <p class="service-type"><?= htmlspecialchars($ct['service_type']) ?></p>
        <p class="location"><?= htmlspecialchars($ct['location']) ?></p>
      </div>
    </div>

    <!-- Profile Details -->
    <div class="profile-details">

      <h2>About Me</h2>
      <p><?= nl2br(htmlspecialchars($ct['qualifications'])) ?></p>

      <h2>Experience</h2>
      <p><?= htmlspecialchars($ct['experience']) ?></p>

      <h2>Status</h2>
      <p><?= htmlspecialchars($ct['status']) ?></p>

    </div>

    <!-- Actions -->
    <div class="profile-actions">
      <a href="<?= URLROOT ?>/public/?url=client/c_book&id=<?= $ct['id'] ?>&return=client/c_find" 
         class="book-btn">
         Request Booking
      </a>

      <button class="back-btn" onclick="window.history.back()">Back</button>
    </div>

  </div>
</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_ctprofileview.js"></script>
</body>
</html>
