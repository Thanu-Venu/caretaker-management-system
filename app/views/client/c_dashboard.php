<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<?php if (!empty($_SESSION['flash_message'])): ?>
  <div class="alert success"><?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?></div>
<?php endif; ?>
<?php
$servicePriceRates = [
  "Elder Care" => [
    "Monthly" => 45000,
    "Yearly"  => 500000
  ],
  "Babysitter" => [
    "Daily"   => 3200,
    "Monthly" => 42000,
    "Yearly"  => 480000
  ],
  "Maid" => [
    "Hourly"  => 500,
    "Daily"   => 3000,
    "Monthly" => 38000,
    "Yearly"  => 450000
  ]
];

$timePriceModifier = [
  "Full Time (8am - 5pm)" => 1.0,
  "Morning (8am - 12pm)"  => 0.6,
  "Evening (1pm - 5pm)"   => 0.6,
  "Night (6pm - 10pm)"    => 1.2
];

function moneyLKR($amount) {
  return "LKR " . number_format((float)$amount, 0);
}
?>

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
<div class="container">
 

<div class="client-dashboard">

  <!-- Welcome -->
  <section class="welcome">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['user']['name']); ?>! </h1>
    <p>Here’s what’s happening with your care services</p>
  </section>

 <!-- Stats Cards -->
<div class="stats-cards">
  <h2>Quick Stats</h2>

  <div class="cards-grid">

    <div class="card stat">
      <div class="icon"><i class='bx bx-book'></i></div>
      <div class="meta">
        <h3><?= $activeBookings ?? 0 ?></h3>
        <p>Active Bookings</p>
      </div>
    </div>

    <div class="card stat">
      <div class="icon"><i class='bx bx-user'></i></div>
      <div class="meta">
        <h3><?= $assignedCaretakers ?? 0 ?></h3>
        <p>Assigned Caretakers</p>
      </div>
    </div>

    <div class="card stat">
      <div class="icon"><i class='bx bx-money'></i></div>
      <div class="meta">
        <h3><?= isset($totalSpent) ? moneyLKR($totalSpent) : moneyLKR(0) ?></h3>
        <p>Total Spent</p>
      </div>
    </div>

    <div class="card stat">
      <div class="icon"><i class='bx bx-star'></i></div>
      <div class="meta">
        <h3><?= $avgRating ?? "0.0" ?></h3>
        <p>Avg Rating Given</p>
      </div>
    </div>

  </div>
</div>

<!-- Price Overview -->
<section class="price-overview">
  <div class="section-head">
    <h2>Service Price Overview</h2>
    <p>Base rates + time modifiers (final price depends on duration and time slot)</p>
  </div>

  <div class="price-grid">

    <!-- Elder Care -->
    <div class="card price-card">
      <div class="price-head">
        <div class="badge"><i class='bx bx-plus-medical'></i></div>
        <div>
          <h3>Elder Care</h3>
          <p>Monthly / Yearly</p>
        </div>
      </div>

      <div class="price-lines">
        <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates["Elder Care"]["Monthly"]) ?></strong></div>
        <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates["Elder Care"]["Yearly"]) ?></strong></div>
      </div>

      <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find">Book Elder Care</a>
    </div>

    <!-- Babysitter -->
    <div class="card price-card">
      <div class="price-head">
        <div class="badge"><i class='bx bx-child'></i></div>
        <div>
          <h3>Babysitter</h3>
          <p>Daily / Monthly / Yearly</p>
        </div>
      </div>

      <div class="price-lines">
        <div class="line"><span>Daily</span><strong><?= moneyLKR($servicePriceRates["Babysitter"]["Daily"]) ?></strong></div>
        <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates["Babysitter"]["Monthly"]) ?></strong></div>
        <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates["Babysitter"]["Yearly"]) ?></strong></div>
      </div>

      <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find">Book Babysitter</a>
    </div>

    <!-- Maid -->
    <div class="card price-card">
      <div class="price-head">
        <div class="badge"><i class='bx bx-home-heart'></i></div>
        <div>
          <h3>Maid</h3>
          <p>Hourly / Daily / Monthly / Yearly</p>
        </div>
      </div>

      <div class="price-lines">
        <div class="line"><span>Hourly</span><strong><?= moneyLKR($servicePriceRates["Maid"]["Hourly"]) ?></strong></div>
        <div class="line"><span>Daily</span><strong><?= moneyLKR($servicePriceRates["Maid"]["Daily"]) ?></strong></div>
        <div class="line"><span>Monthly</span><strong><?= moneyLKR($servicePriceRates["Maid"]["Monthly"]) ?></strong></div>
        <div class="line"><span>Yearly</span><strong><?= moneyLKR($servicePriceRates["Maid"]["Yearly"]) ?></strong></div>
      </div>

      <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find">Book Maid</a>
    </div>

  </div>

  <!-- Time modifier mini card -->
  <div class="card modifier-card">
    <div class="modifier-head">
      <i class='bx bx-time-five'></i>
      <h3>Time Modifiers</h3>
    </div>
    <div class="modifier-grid">
      <div><span>Morning</span><strong><?= $timePriceModifier["Morning (8am - 12pm)"] ?>x</strong></div>
      <div><span>Evening</span><strong><?= $timePriceModifier["Evening (1pm - 5pm)"] ?>x</strong></div>
      <div><span>Full Time</span><strong><?= $timePriceModifier["Full Time (8am - 5pm)"] ?>x</strong></div>
      <div><span>Night</span><strong><?= $timePriceModifier["Night (6pm - 10pm)"] ?>x</strong></div>
    </div>
  </div>
</section>

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
