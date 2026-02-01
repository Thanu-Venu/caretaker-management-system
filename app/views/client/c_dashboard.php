<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
  <div class="alert success"><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?></div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_dashboard.css">
</head>
<body>
<<div class="container">
 

<div class="client-dashboard">

  <!-- Welcome -->
  <section class="welcome">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['user']['name']); ?>! </h1>
    <p>Here’s what’s happening with your care services</p>
  </section>

  <!-- Stats Cards -->
  <div class="stats-cards">
    <h2>Stats Cards</h2>
    <div class="card">
      <div class="action1">
        <i class='bx bx-book'></i>
      </div>
        <h3><?= $data['activeBookings']; ?></h3>
         <p>Active Bookings</p>

    </div>
    <div class="card">
      <div class="action1">
       <i class='bx bx-user'></i>
       </div>
        <h3><?= $data['caretakers']; ?></h3>
        <p>Assigned Caretakers</p>

    </div>
    <div class="card">
      <div class="action1">
        <i class='bx bx-money'></i>
        </div>
         <h3>LKR <?= number_format($data['totalSpent']); ?></h3>
         <p>Total Spent</p>

    </div>
    <div class="card">
      <div class="action1">
        <i class='bx bx-star' ></i>
        </div>
         <h3><?= $data['avgRating'] ?? '0.0'; ?></h3>
         <p>Avg Rating Given</p>

    </div>
</div>

  <!-- Quick Actions -->
  <section class="quick-actions">
    <h2>Quick Actions</h2>
    <div class="actions">
      <div class="action">
        <i class='bx bx-search'></i>
        <h3>   
          <button id="bookBtn" class="main-btn" onclick="location.href='http://localhost/CMA/public/?url=client/c_find'">
           Book New Service
          </button>
        </h3>

        <p>Find and book a caretaker</p>
      </div>
      <div class="action">
        <i class='bx bx-calendar-edit'></i>
          <h3>   
          <button id="bookBtn" class="main-btn" onclick="location.href='http://localhost/CMA/public/?url=client/c_upcomingBookings'">
           Reschedule Booking
          </button>
        </h3>

        <p>Change your appointmnet time</p>
      </div>
      <div class="action">
        <i class='bx bx-phone'></i>
         <h3>   
          <button id="bookBtn" class="main-btn">Contact Caretaker</button>
        </h3>      
        <p>Manage your assigned caretaker</p>
      </div>
      <div class="action">
        <i class='bx bx-support'></i>
         <h3>   
          <button id="bookBtn" class="main-btn">Emergency Support</button>
        </h3> 
        <p>24/7 Emergency assistance</p>
      </div>
    </div>
  </section>

 <section class="recent-bookings">
  <h2>Recent Bookings</h2>

  <?php foreach ($data['recentBookings'] as $booking): ?>
    <div class="booking">
      <img src="../public/images/find.png" alt="">
      <div>
        <h3><?= $booking['service_type']; ?> with <?= $booking['caretaker_name']; ?></h3>
        <p>
          <?= date('m/d/Y', strtotime($booking['booking_date'])); ?>
          at <?= $booking['preferred_time']; ?>
          • <?= $booking['duration']; ?> hours
        </p>
      </div>
      <span class="status <?= strtolower($booking['status']); ?>">
        <?= $booking['status']; ?>
      </span>
    </div>
  <?php endforeach; ?>
</section>


  <section class="recent-notifications">
  <h2>Recent Notifications</h2>

  <?php foreach ($data['notifications'] as $note): ?>
    <div class="notification">
      <i class='bx bx-bell'></i>
      <div>
        <p><?= $note['message']; ?></p>
      </div>
      <span><?= date("h:i A", strtotime($note['created_at'])); ?></span>
    </div>
  <?php endforeach; ?>
</section>


</div>
 <!-- your existing content -->
</div>

</body>
</html>
