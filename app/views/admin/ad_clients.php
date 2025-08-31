<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_clients.css">
</head>
<body>
    <div class="content">
  <h1 class="page-title">Client Management</h1>

  <div class="search-box">
    <i class='bx bx-search'></i>
    <input type="text" placeholder="Search clients...">
  </div>

  <div class="table-container">
    <table class="client-table">
      <thead>
        <tr>
          <th>Client Name</th>
          <th>Associated Services</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Sophia Carter</td>
          <td>Elder Care</td>
          <td>
            <i class='bx bx-show'></i>
            <i class='bx bx-edit'></i>
            <i class='bx bx-trash'></i>
          </td>
        </tr>
        <tr>
          <td>Ethan Bennett</td>
          <td>Cooking and meal preparation</td>
          <td>
            <i class='bx bx-show'></i>
            <i class='bx bx-edit'></i>
            <i class='bx bx-trash'></i>
          </td>
        </tr>
        <tr>
          <td>Isabella Harper</td>
          <td>Personal care (feeding, bathing, grooming)</td>
          <td>
            <i class='bx bx-show'></i>
            <i class='bx bx-edit'></i>
            <i class='bx bx-trash'></i>
          </td>
        </tr>
        <tr>
          <td>Caleb Foster</td>
          <td>Child supervision and safety</td>
          <td>
            <i class='bx bx-show'></i>
            <i class='bx bx-edit'></i>
            <i class='bx bx-trash'></i>
          </td>
        </tr>
        <tr>
          <td>Mia Reynolds</td>
          <td>Cleaning and housekeeping</td>
          <td>
            <i class='bx bx-show'></i>
            <i class='bx bx-edit'></i>
            <i class='bx bx-trash'></i>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>