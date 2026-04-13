<?php
$clientPageTitle = 'Dashboard - SmartCare';
$clientExtraCss = ['client/c_dashboard.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';
?>
<?php
$servicePriceRates = [
  "Elder Care" => [
    "Monthly" => 50000,
    "Yearly"  => 550000
  ],
  "Babysitter" => [
    "Daily"   => 2200,
    "Monthly" => 45000,
    "Yearly"  => 500000
  ],
  "Maid" => [
    "Hourly"  => 500,
    "Daily"   => 2000,
    "Monthly" => 38000,
    "Yearly"  => 420000
  ]
];

$timePriceModifier = [
  "Full Time (8am - 5pm)" => 1.0,
  "Morning (8am - 12pm)"  => 0.6,
  "Evening (1pm - 5pm)"   => 0.6,
  "Night (6pm - 10pm)"    => 1.2
];

function moneyLKR($amount)
{
  return "LKR " . number_format((float)$amount, 0);
}
?>

<main class="main-content admin-dashboard-page client-dashboard-page">

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="alert success"><?php echo $_SESSION['flash_message'];
        unset($_SESSION['flash_message']); ?>
      </div>
    <?php endif; ?>

    <div id="emergencyModal" class="modal">
      <div class="modal-content">

        <span class="close" onclick="closeEmergencyModal()">&times;</span>

        <h2>🚨 Emergency Support</h2>
        <p>Submit your emergency request</p>

        <form method="POST" action="">
          
          <div class="form-group">
            <label>Emergency Type</label>
            <select name="type" required>
              <option value="">Select type</option>
              <option>Medical Emergency</option>
              <option>Caretaker Not Responding</option>
              <option>Accident / Injury</option>
              <option>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Describe the emergency..." required></textarea>
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="phone" required>
          </div>

          <button type="submit" class="submit-btn">Send Alert 🚨</button>
        </form>

        <div class="quick-call">
          <a href="tel:1990">🚑 Ambulance</a>
          <a href="tel:119">🚓 Police</a>
        </div>

      </div>
    </div>


    <?php if (!empty($data['pendingAdvance'])): ?>
      <div id="advanceModal" class="modal" style="display:flex;">
        <div class="modal-content" style="max-width:640px;">
          <span class="close" onclick="document.getElementById('advanceModal').style.display='none'">&times;</span>
          <h2 style="margin-bottom:12px; color:#1e88e5; font-family: 'Poppins', sans-serif; font-weight:700; font-size:24px;">Advance Payments Required</h2>
          <p>You have pending advance payments for the following bookings:</p>

          <?php foreach ($data['pendingAdvance'] as $p): ?>
            <div class="advance-details" style="margin-bottom:15px;">
              <div><b>Booking #:</b> <?= $p['booking_id'] ?></div>
              <div><b>Service:</b> <?= htmlspecialchars($p['service_type']) ?></div>
              <div><b>Date:</b> <?= htmlspecialchars($p['booking_date']) ?></div>
              <div><b>Time:</b> <?= htmlspecialchars($p['preferred_time']) ?></div>
              <div><b>Duration:</b> <?= htmlspecialchars($p['duration'] . ' ' . $p['basis']) ?></div>

              <a class="modal-submit-btn"
                style="margin-top:10px;"
                href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= $p['booking_id'] ?>">
                Pay Now
              </a>
            </div>
          <?php endforeach; ?>

        </div>
      </div>
    <?php endif; ?>
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
              <i class='bx bx-star'></i>
            </div>
            <h3><?= $data['avgRating'] ?? '0.0'; ?></h3>
            <p>Avg Rating Given</p>
          </div>

          <div class="card" style="<?= !empty($data['pendingAdvance']) ? 'border-color: #e53935; box-shadow: 0 4px 12px rgba(229,57,53,0.1);' : '' ?>">
            <div class="action1">
              <i class='bx bx-wallet-alt' style="<?= !empty($data['pendingAdvance']) ? 'color: #e53935; background: rgba(229,57,53,0.1);' : '' ?>"></i>
            </div>
            <h3 style="color: <?= !empty($data['pendingAdvance']) ? '#e53935' : '#111' ?>;">
              <?= count($data['pendingAdvance'] ?? []); ?>
            </h3>
            <p style="<?= !empty($data['pendingAdvance']) ? 'color: #e53935; font-weight: 500;' : '' ?>">Pending Advances</p>
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

              <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find1">Book Elder Care</a>
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

              <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find1">Book Babysitter</a>
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

              <a class="ghost-btn" href="<?= URLROOT; ?>/client/c_find1">Book Maid</a>
            </div>

          </div>

         

           <!-- Time modifier mini card -->
          <div class="card modifier-card">
            <div class="modifier-head">
              <i class='bx bx-time-five'></i>
              <h3>Time Modifiers</h3>
            </div>
            <div class="modifier-grid">
              <div><span>Morning</span><strong><?= $timePriceModifier["Morning (8am - 12pm)"] ?> %</strong></div>
              <div><span>Evening</span><strong><?= $timePriceModifier["Evening (1pm - 5pm)"] ?> %</strong></div>
              <div><span>Full Time</span><strong><?= $timePriceModifier["Full Time (8am - 5pm)"] ?> %</strong></div>
              <div><span>Night</span><strong><?= $timePriceModifier["Night (6pm - 10pm)"] ?> %</strong></div>
            </div>
          </div>

          <div class="card advance-card">
            <div class="modifier-head">
              <i class='bx bx-wallet'></i>
              <h3>Advance Payment Rules</h3>
            </div>
            <div class="price-lines">
              <div class="line"><span>Hourly</span><strong>Full payment required</strong></div>
              <div class="line"><span>Daily (lead time)</span><strong>Within 15 days: full payment</strong></div>
              <div class="line"><span>Daily</span><strong>15+ days: 50% advance</strong></div>
              <div class="line"><span>Monthly</span><strong>1 month advance for &lt; 6 months; otherwise 3 months</strong></div>
              <div class="line"><span>Yearly</span><strong>&lt; 1 year: 3 months; 1+ year: 6 months</strong></div>
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
                <button id="bookBtn" class="main-btn" onclick="location.href='http://localhost/CMA/public/?url=client/c_find1'">
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
                <button id="bookBtn" class="main-btn" onclick="location.href='http://localhost/CMA/public/?url=client/c_contactCT'">Contact Caretaker</button>
              </h3>
              <p>Manage your assigned caretaker</p>
            </div>
            <div class="action">
              <i class='bx bx-support'></i>
              <h3>
                <button id="bookBtn" type="button" onclick="openEmergencyModal()" class="main-btn">Emergency Support</button>
              </h3>
              <p>24/7 Emergency assistance</p>
            </div>
          </div>
        </section>

        <section class="recent-bookings">
          <h2>Recent Bookings</h2>

          <?php if (!empty($data['recentBookings'])): ?>
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
          <?php else: ?>
            <div class="booking">
              <div>
                <h3>No recent bookings</h3>
                <p>You do not have any recent bookings yet.</p>
              </div>
            </div>
          <?php endif; ?>
        </section>


      </div>

</main>

<script src="<?php echo URLROOT; ?>/public/js/client/c_dashboard.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>