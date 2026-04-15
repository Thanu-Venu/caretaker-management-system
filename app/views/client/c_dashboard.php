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

$pendingAdvanceList = $data['pendingAdvance'] ?? [];
$hasPendingAdvance  = !empty($pendingAdvanceList);
?>

<main class="main-content admin-dashboard-page client-dashboard-page">

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <?php $flashType = $_SESSION['flash_type'] ?? 'success'; ?>
      <div class="alert <?= htmlspecialchars((string) $flashType, ENT_QUOTES, 'UTF-8') ?>"><?php echo $_SESSION['flash_message'];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
      </div>
    <?php endif; ?>

    <div id="emergencyModal" class="modal">
      <div class="modal-content">

        <span class="close" onclick="closeEmergencyModal()">&times;</span>

        <h2>🚨 Emergency Support</h2>
        <p>Submit your emergency request</p>

        <form method="POST" action="">
          <input type="hidden" name="emergency_submit" value="1">
          
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



        <div class="emergency-hotlines" role="note" aria-label="Emergency hotline information">
          <p><strong>Police Emergency (Hotline):</strong> 119 / 118</p>
          <p><strong>Ambulance / Medical Emergency (Suwa Seriya):</strong> 1990</p>
          <p><strong>Ambulance / Fire &amp; Rescue (General):</strong> 110</p>
        </div>

      </div>
    </div>


    <?php if ($hasPendingAdvance): ?>
      <div id="advanceModal" class="modal show" role="dialog" aria-modal="true" aria-labelledby="advanceModalTitle">
        <div class="modal-content" style="max-width:640px;">
          <button type="button" class="close" onclick="document.getElementById('advanceModal').classList.remove('show')" aria-label="Close">&times;</button>
          <h3 id="advanceModalTitle">Advance payments required</h3>
          <p class="text-muted">You have pending advance payments for the following bookings.</p>
          <div class="advance-modal-list">
            <?php require APPROOT . '/views/client/partials/advance_pending_booking_cards.php'; ?>
          </div>
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

          <div class="card">
            <div class="action1">
              <i class='bx bx-wallet-alt'></i>
            </div>
            <h3>
              <?= count($data['pendingAdvance'] ?? []); ?>
            </h3>
            <p>Pending Advances</p>
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
              <div class="line"><span>Daily</span><strong>15-30 days: 10 days advance</strong></div>
              <div class="line"><span>Daily balance</span><strong>Remaining due before booking end</strong></div>
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

        <section class="recent-bookings client-dashboard-recent">
          <h2>Recent Bookings</h2>

          <?php if (!empty($data['recentBookings'])): ?>
            <?php foreach ($data['recentBookings'] as $booking): ?>
              <?php
                $rawStatus = (string)($booking['status'] ?? '');
                $statusClass = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $rawStatus), '-'));
              ?>
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
                <span class="status <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars($rawStatus, ENT_QUOTES, 'UTF-8'); ?>
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