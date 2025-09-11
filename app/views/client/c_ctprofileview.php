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
    <!-- Caretaker Image -->
    <div class="profile-header">
      <img src="<?php echo URLROOT; ?>/public/images/find.png" alt="Caretaker Photo" class="profile-img">
      <div>
        <h1>Jane Doe</h1>
        <p class="service-type">Babysitter</p>
        <p class="location"><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</p>
      </div>
    </div>

    <!-- Profile Details -->
    <div class="profile-details">
      <h2>About Me</h2>
      <p>
        I have over 5 years of experience in babysitting. I am responsible, caring, 
        and enjoy working with children. My goal is to provide a safe and fun environment.
      </p>

      <h2>Experience</h2>
      <ul>
        <li>5+ years in babysitting</li>
        <li>Certified in First Aid & CPR</li>
        <li>Worked with kids aged 1–12</li>
      </ul>

      <h2>Availability</h2>
      <p>Morning, Afternoon, and Evening shifts available.</p>

      <h2>Ratings & Reviews</h2>
      <div class="reviews">
        <p>⭐ 4.7/5 (23 reviews)</p>
        <div class="review">
          <strong>Client A:</strong> "Very caring and punctual."
        </div>
        <div class="review">
          <strong>Client B:</strong> "Kids loved her, will book again!"
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="profile-actions">
      <button class="book-btn" onclick="window.location.href='?url=client/c_book'">Request Booking</button>
      <button class="back-btn" onclick="window.history.back()">Back</button>
    </div>
  </div>
</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_ctprofileview.js"></script>
</body>
</html>
