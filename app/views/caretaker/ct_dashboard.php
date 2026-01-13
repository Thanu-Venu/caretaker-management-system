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
    <h1>Welcome back, Sarah!</h1>
    <p>Manage your bookings and availability</p>
  </section>

  <!-- Dashboard Layout -->
  
  <main class="dashboard">
    <!-- Profile Overview -->
    <section class="card profile">
      <h3>Profile Overview</h3>
      <div class="profile-body">
        <img src="<?php echo URLROOT; ?>/public/images/find.png" alt="Profile">
        <div>
          <div class="profile-header">
            <h4>Sarah Johnson <br><span class="rating">⭐ 4.9 (127 views)</span>
              <button class="btn-verify">Elder Care Specialist</button>
            </h4>
            
           <button   onclick="openProfile()" class="btn"  >Edit profile</button>
          </div>
          
          <p class="profile-desc">Experienced elder care specialist with 8 years of compassionate service.<br>
             Specialized in medication management, mobility assistance, and companionship care.</p>
          <div class="tags">
            <span class="tag">Elder Care</span>
            <span class="tag">Medication Management</span>
            <span class="tag">Mobility assistance</span>
            <span class="tag">Companionship</span><br>
          </div>
        </div>
      </div>
    </section>

    <!-- Availability -->
    <section class="card availability">
      <h3>Availability Status</h3>
      <p><strong>Currently Available</strong></p>
      <label class="switch">
        <input type="checkbox" checked>
        <span class="slider"></span>
      </label>
      
      <p>You're visible to clients and can receive new bookings</p>
      <div class="Available">
      <button class="butn">Available now</button>
      </div>
    </section>

    <!-- Bookings -->
    <section class="card bookings">
      <h3>Upcoming Bookings</h3>
      <table>
        <thead>
          <tr><th>Client</th><th>Date & Time</th><th>Service</th><th>Location</th><th>Payment</th></tr>
        </thead>
        <tbody>
          <tr><td>Mrs Johnson</td><td>2024-01-20<br>9:00 AM - 1:00 PM</td><td><span class="badge">Elder Care</span></td><td>Vavuniya</td><td>700</td></tr>
          <tr><td>The Smith Family</td><td>2024-01-20<br>6:00 AM - 1:00 PM</td><td><span class="badge">Elder Care</span></td><td>Jaffna</td><td>2000</td></tr>
          <tr><td>Mr Davis</td><td>2024-01-20<br>8:00 AM - 5:00 PM</td><td><span class="badge">Elder Care</span></td><td>Colombo</td><td>1000</td></tr>
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
      <p>Currently Available: <strong>12</strong></p>
      <p>Hours Worked: <strong>48</strong></p>
      <p>Earnings: <strong>1200</strong></p>
      <p>Ratings: ⭐ 4.9</p>
      </div>
    </section>
  </main>
  </div>

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
