<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Logs - SmartCare</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_history.css">
</head>
<body>
  

  <main class="content">
      <h1>Logs</h1>
      <div class="search">
        <input type="text" id="searchInput" placeholder="Search...">
        <button id="searchButton">Search</button>
      </div>

      <div class="filters">
        <select id="roleFilter">
          <option value="">User Role</option>
          <option value="Admin">Admin</option>
          <option value="Manager">HR Manager</option>
          <option value="Caregiver">Caregiver</option>
          <option value="Client">Client</option>
        </select>

        <select id="userFilter">
          <option value="">Username</option>
        </select>

        <select id="dateFilter">
          <option value="">Date Range</option> 
         <option value="asc">Ascending</option>
          <option value="desc">Descending</option>
        </select>
        

       <select id="actionFilter">
          <option value="">Action Type</option>
        </select>
      </div>  

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
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/hr/hr_history.js"></script>
</body>
</html>