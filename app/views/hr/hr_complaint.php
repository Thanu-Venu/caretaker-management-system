<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Management - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_complaint.css">
</head>
<body>


    <!-- Main Content -->
    <main class="main">
      <h1>Complaints Management</h1>
      <p class="subtitle">Manage and resolve client complaints efficiently.</p>

      <!-- Tabs -->
      <div class="tabs">
        <button class="tab active">All Complaints</button>
        <button class="tab">My Complaints</button>
      </div>

      <!-- Complaints Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Complaint ID</th>
              <th>Client Name</th>
              <th>Caretaker Name</th>
              <th>Category</th>
              <th>Details</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>Emily Carter</td>
              <td>David Lee</td>
              <td><span class="tag blue">Caretaker Behavior</span></td>
              <td>Inappropriate conduct during visit.</td>
              <td><span class="status open">Open</span></td>
              <td><button class="btn">Assign to Investigator</button></td>
            </tr>
            <tr>
              <td>#12346</td>
              <td>Robert Johnson</td>
              <td>Sarah Chen</td>
              <td><span class="tag green">Service Quality</span></td>
              <td>Missed scheduled visit.</td>
              <td><span class="status progress">In Progress</span></td>
              <td><button class="btn">Mark as Resolved</button></td>
            </tr>
            <tr>
              <td>#12347</td>
              <td>Olivia Davis</td>
              <td>Michael Brown</td>
              <td><span class="tag blue">Caretaker Behavior</span></td>
              <td>Lack of communication.</td>
              <td><span class="status open">Open</span></td>
              <td><button class="btn">Assign to Investigator</button></td>
            </tr>
            <tr>
              <td>#12348</td>
              <td>William Clark</td>
              <td>Jessica Green</td>
              <td><span class="tag green">Service Quality</span></td>
              <td>Unsatisfactory care provided.</td>
              <td><span class="status resolved">Resolved</span></td>
              <td><button class="btn">View Details</button></td>
            </tr>
            <tr>
              <td>#12349</td>
              <td>Sophia White</td>
              <td>Daniel Taylor</td>
              <td><span class="tag blue">Caretaker Behavior</span></td>
              <td>Disrespectful attitude.</td>
              <td><span class="status progress">In Progress</span></td>
              <td><button class="btn">Mark as Resolved</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <script src="script.js"></script>
</body>
</html>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Complaints Management - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_complaint.css " />
</head>
<body>
  <!-- Assuming your sidebar is already coded and present in the layout -->

  <!-- Main Content -->
  
  <main class="main-content">
    <br>
    <h1>Complaints Management</h1>
    <p class="subtitle">Manage and resolve client complaints efficiently.</p>

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab active"><b>All Complaints</b></button>
      <button class="tab"><b>My Complaints</b></button>
    </div>

    <!-- Complaints Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Client Name</th>
            <th>Caretaker Name</th>
            <th>Category</th>
            <th>Details</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#12345</td>
            <td>Emily Carter</td>
            <td>David Lee</td>
            <td><span class="tag blue">Caretaker Behavior</span></td>
            <td>Inappropriate conduct during visit.</td>
            <td><span class="status open">Open</span></td>
            <td><b>Assign to Investigator</b></td>
          </tr>
          <tr>
            <td>#12346</td>
            <td>Robert Johnson</td>
            <td>Sarah Chen</td>
            <td><span class="tag green">Service Quality</span></td>
            <td>Missed scheduled visit.</td>
            <td><span class="status progress">In Progress</span></td>
            <td><b>Mark as Resolved</b></td>
          </tr>
          <tr>
            <td>#12347</td>
            <td>Olivia Davis</td>
            <td>Michael Brown</td>
            <td><span class="tag blue">Caretaker Behavior</span></td>
            <td>Lack of communication.</td>
            <td><span class="status open">Open</span></td>
            <td><b>Assign to Investigator</b></td>
          </tr>
          <tr>
            <td>#12348</td>
            <td>William Clark</td>
            <td>Jessica Green</td>
            <td><span class="tag green">Service Quality</span></td>
            <td>Unsatisfactory care provided.</td>
            <td><span class="status resolved">Resolved</span></td>
            <td><b>View Details</b></td>
          </tr>
          <tr>
            <td>#12349</td>
            <td>Sophia White</td>
            <td>Daniel Taylor</td>
            <td><span class="tag blue">Caretaker Behavior</span></td>
            <td>Disrespectful attitude.</td>
            <td><span class="status progress">In Progress</span></td>
            <td><b>Mark as Resolved</b></td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>

