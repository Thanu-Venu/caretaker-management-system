<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Logs - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_history.css">
</head>
<body>
  

  <main class="content">
      <h1>Logs</h1>
      <div class="filters">
        <label for="rolFilter">User Role</label>
        <select id="roleFilter" onchange="filterLogs()">
          <option value="">All</option>
          <option value="Admin">Admin</option>
          <option value="Manager">HR Manager</option>
          <option value="Caregiver">Caregiver</option>
          <option value="Client">Client</option>
        </select>

        <label for="userFilter">Username</label>
        <select id="userFilter" onchange="filterLogs()">
          <option value="">All</option>
        </select>

        <label for="dateFilter">Date Range</label>
        <select id="dateFilter" onchange="filterLogs()">
          <option value="">All</option> 
         <option value="asc">Ascending</option>
          <option value="desc">Descending</option>
        </select>  

        <label for="actionFilter">Action Type</label>
       <select id="actionFilter" onchange="filterLogs()">
          <option value="">All</option>
        </select>
      </div>  
  <div class="container">
    <section>
      <table id="logTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>User Name</th>
            <th>Role</th>
            <th>Action Description</th>
            <th>Affected Section</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data filled by JS -->
        </tbody>
      </table>
    </section>
  </div>
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/admin/ad_history.js"></script>
</body>
</html>