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
    <h1>Welcome back, </h1>
    <p>Here’s what’s happening with your care services</p>
  </section>

  <!-- Stats Cards -->
  <div class="stats-cards">
    <h2>Stats Cards</h2>
    <div class="card">
      <div class="action1">
        <i class='bx bx-book'></i>
      </div>
        <h3>5</h3>
        <p>Active Bookings</p>
    </div>
    <div class="card">
      <div class="action1">
       <i class='bx bx-user'></i>
       </div>
        <h3>3</h3>
        <p>Assigned Caretakers</p>
    </div>
    <div class="card">
      <div class="action1">
        <i class='bx bx-money'></i>
        </div>
        <h3>LKR 170,250</h3>
        <p>Total Spent</p>
    </div>
    <div class="card">
      <div class="action1">
        <i class='bx bx-star' ></i>
        </div>
        <h3>4.8</h3>
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

  <!-- Recent Bookings -->
  <section class="recent-bookings">
    <h2>Recent Bookings</h2>
    <div class="booking">
      <img src="../public/images/find.png" alt="">
      <div>
        <h3>Elderly Care with Sarah Johnson</h3>
        <p>12/15/2024 at 12:00 PM • 4 hours</p>
      </div>
      <span class="status confirmed">Confirmed</span>
    </div>
    <div class="booking">
      <img src="../public/images/find.png" alt="">
      <div>
        <h3>Medical Care with Michael Chen</h3>
        <p>12/18/2024 at 07:30 PM • 2 hours</p>
      </div>
      <span class="status confirmed">Confirmed</span>
    </div>
    <div class="booking">
      <img src="../public/images/find.png" alt="">
      <div>
        <h3>Child Care with Emily Rodriguez</h3>
        <p>12/20/2024 at 01:30 PM • 5 hours</p>
      </div>
      <span class="status pending">Pending</span>
    </div>
  </section>

  <!-- Recent Notifications -->
  <section class="recent-notifications">
    <h2>Recent Notifications</h2>
    <div class="notification">
      <i class='bx bx-calendar-check'></i>
      <div>
        <h4>Booking Confirmed</h4>
        <p>Your booking with Sarah Johnson has been confirmed for Dec 15, 2024.</p>
      </div>
      <span>2 hours ago</span>
    </div>
    <div class="notification">
      <i class='bx bx-credit-card'></i>
      <div>
        <h4>Payment Processed</h4>
        <p>Payment of LKR 45,000 has been successfully processed.</p>
      </div>
      <span>1 day ago</span>
    </div>
    <div class="notification">
      <i class='bx bx-alarm'></i>
      <div>
        <h4>Upcoming Appointment</h4>
        <p>You have an appointment with Michael Chen tomorrow at 7:30 PM.</p>
      </div>
      <span>2 days ago</span>
    </div>
  </section>

</div>
 <!-- your existing content -->
</div>

</body>
</html>
