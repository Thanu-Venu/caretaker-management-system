<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare Dashboard</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_dashboard.css">
</head>
<body>

<div id="dashboard">
  
<div class="content">

  <!-- Welcome -->
  <section class="welcome">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h1>
    
  </section>
<br>
  <!-- Dashboard Layout -->
  
  <main class="dashboard">
    <!-- Profile Overview -->
    <section class="card profile">
      <h3>Profile Overview</h3>
      <div class="profile-body">
        <img src="<?= URLROOT ?>/public/uploads/<?= $data['caretaker']['profile_image'] ?>" alt="Profile">
        <div>
          <div class="profile-header">
           <h4>
    <?= htmlspecialchars($data['caretaker']['name']) ?>
    <br>
    <span class="rating">⭐ <?= $data['caretaker']['rating'] ?? '0.0' ?> (<?= $data['caretaker']['views'] ?? 0 ?> views)</span>
    <button class="btn-verify">
        <?= htmlspecialchars($data['caretaker']['service_type']) ?>
    </button>
</h4>

<button class="btn" type="button"
  onclick="window.location.href='<?= URLROOT ?>/index.php?url=Caretaker/ct_settings'">
  Edit profile
</button>
          </div>
          
          <p class="profile-desc">
    <?= nl2br(htmlspecialchars($data['caretaker']['qualifications'])) ?>
</p>
          <div class="tags">
    <span class="tag"><?= htmlspecialchars($data['caretaker']['service_type']) ?></span>
    <span class="tag"><?= htmlspecialchars($data['caretaker']['experience']) ?> Years Experience</span>
    <span class="tag"><?= htmlspecialchars($data['caretaker']['location']) ?></span>
</div>
        </div>
      </div>
    </section>

    <!-- Availability -->
    <section class="card availability">
      <h3>Availability Status</h3>
     <br>
      
      <p>You're visible to clients and can receive new bookings</p><br>
     <label class="switch">
        <input type="checkbox" checked>
        <span class="slider"></span>
      </label>
  
     
    </section>

    <!-- Bookings -->
    <section class="card bookings">
      <h3>Upcoming Bookings</h3>
      <table>
        <thead>
          <tr><th>Client</th><th>Date & Time</th><th>Service</th><th>Location</th><th>Payment</th></tr>
        </thead>
       <tbody>
    <?php if (empty($data['upcoming'])): ?>
        <tr>
            <td colspan="5" style="text-align:center;">No upcoming bookings</td>
        </tr>
    <?php else: ?>
        <?php foreach ($data['upcoming'] as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['client_name']) ?></td>
                <td>
                    <?= htmlspecialchars($b['booking_date']) ?><br>
                    <?= htmlspecialchars($b['preferred_time']) ?>
                </td>
                <td><span ><?= htmlspecialchars($b['service_type']) ?></span></td>
                <td><?= htmlspecialchars($b['service_location']) ?></td>
                <td><?= htmlspecialchars($b['total_payment']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

      </table>
     
       <div class="button-cont">
      <button class="btn-small">See All</button>
      </div>
    </section>

      <!-- Schedule -->
    <section class="card schedule">
      <h3>Schedule</h3>
      <div class="calendar">
        <p>September 2025</p>
        <div class="days">
          <span>Su</span><span>Mo</span><span>Tu</span><span>We</span>
          <span>Th</span><span>Fr</span><span>Sa</span>
        </div>
        <div class="dates" id="calendarDates"></div>
      </div>
    </section>

    <!-- Leave Management -->
    <section class="card leave">
      <h3>Leave Management</h3>
      <div class="button-container">
           <!-- Button to open modal -->
        <button id="openLeaveModal" class="btn-le">Request Leave</button>
      
     </div>
      <table>
        <thead>
          <tr><th>Dates</th><th>Reason</th><th>Status</th></tr>
        </thead>
        <tbody>
<?php foreach($data['leaves'] as $leave): ?>
<tr>
    <td><?= date("M d", strtotime($leave['start_date'])) ?> – <?= date("M d", strtotime($leave['end_date'])) ?></td>
    <td><?= htmlspecialchars($leave['reason']) ?></td>
    <td>
        <span class="status <?= strtolower($leave['status']) ?>">
            <?= $leave['status'] ?>
        </span>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

      </table>
       <div class="button-cont">
      <button class="btn-small">See All</button>
      </div>
      
    </section>

    <!-- This Month -->
    <section class="card">
      <h3>This Month</h3>
      <div class="mon-bod">
      <p>Currently Available: <strong>12</strong></p><br>
      <p>Hours Worked: <strong>48</strong></p><br>
      <p>Earnings: <strong>1200</strong></p><br>
      <p>Ratings: ⭐ 4.9</p>
      </div>
    </section>
  </main>
  <

</div>

<button   onclick="openProfile()" class="btn"  >Edit profile</button>

<!-- Profile Modal -->
<div id="profileModal" class="modal">
  <div class="modal-content"> 
    <h2 class="Edit">Edit Profile</h2>
    <input type="text" id="name" placeholder="Name">
    <input type="text" id="experience" placeholder="Experience">
    <input type="text" id="qualifications" placeholder="Qualifications">
    <div class="button-container">
    <button class="save-btn" onclick="saveProfile()">Save Changes</button>
    <button class="close-btn" onclick="closeProfile()">Close</button>
    </div>
  </div>
</div>


<!-- Modal -->
  <div id="leaveModal" class="le-modal">
    <div class="le-modal-content">
      <span id="closeLeaveModal" class="close">&times;</span>
      <h2>Request Leave</h2>
      <p class="subtext">Submit a new leave request</p>

      <form id="leaveForm">
        <label>Leave Type
          <select required>
            <option value="">Select</option>
            <option value="vacation">Vacation</option>
            <option value="sick">Sick Leave</option>
            <option value="personal">Personal</option>
          </select>
        </label>

        <div class="row">
          <label>Start Date <input type="date" required></label>
          <label>End Date <input type="date" required></label>
        </div>

        <div class="row">
          <label>Start Time <input type="time" value="09:00" required></label>
          <label>End Time <input type="time" value="17:00" required></label>
        </div>

        <label>Reason
          <textarea placeholder="Please provide a reason for your leave request..." required></textarea>
        </label>

        <button type="submit" class="submit-btn">Submit Request</button>
      </form>
    </div>
  </div>





<script src="<?php echo URLROOT; ?>/public/js/caretaker/ct_dashboard.js"></script>
</body>
</html>
