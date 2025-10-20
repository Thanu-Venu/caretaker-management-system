<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_leave.css">
</head>
<body>
  

  <!-- Main Content -->
  <main class="content">
   

    <section>
      <h1>Leave Management</h1>
         <!-- Filter Section -->
      <div class="filter-container">
          <h3>Filter</h3>
        <div class="filter-row">    
          <select class="caregiver">
            <option>Select Caregiver</option>
            <option>Emily Carter</option>
            <option>David Lee</option>
            <option>Sarah Jones</option>
            <option>Michael Brown</option>
            <option>Jessica Wilson</option>
          </select>

          <select class ="caregiver1">
            <option>Select Status</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
          </select>

          <button class="apply-btn">Apply Filters</button>
        </div>
      </div>

<h3 class="leave">Leave Requests</h3>

      <!-- Leave Requests Table -->
      <table>
        <thead>
          <tr>
            <th>Caregiver Name</th>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="leaveTable">
          <tr>
            <td>Emily Carter</td>
            <td>Vacation</td>
            <td>2024-07-15</td>
            <td>2024-07-20</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>David Lee</td>
            <td>Sick Leave</td>
            <td>2024-07-10</td>
            <td>2024-07-12</td>
            <td><span class="status approved">Approved</span></td>
          </tr>
          <tr>
            <td>Sarah Jones</td>
            <td>Personal Leave</td>
            <td>2024-07-22</td>
            <td>2024-07-25</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>Michael Brown</td>
            <td>Vacation</td>
            <td>2024-08-05</td>
            <td>2024-08-10</td>
            <td><span class="status rejected">Rejected</span></td>
          </tr>
          <tr>
            <td>Jessica Wilson</td>
            <td>Maternity Leave</td>
            <td>2024-09-01</td>
            <td>2024-12-01</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>chael duo</td>
            <td>Vacation</td>
            <td>2024-08-05</td>
            <td>2024-08-10</td>
            <td><span class="status rejected">Rejected</span></td>
          </tr>
           <tr>
            <td>Emily Carter</td>
            <td>Vacation</td>
            <td>2024-07-15</td>
            <td>2024-07-20</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
          <tr>
            <td>David Lee</td>
            <td>Sick Leave</td>
            <td>2024-07-10</td>
            <td>2024-07-12</td>
            <td><span class="status approved">Approved</span></td>
          </tr>
          <tr>
            <td>Sarah Jones</td>
            <td>Personal Leave</td>
            <td>2024-07-22</td>
            <td>2024-07-25</td>
            <td><span class="status pending">Pending</span></td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
