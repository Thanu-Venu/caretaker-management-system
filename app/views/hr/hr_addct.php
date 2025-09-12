<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_addct.css">
  <link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
        <main class="main-content">
      <section class="caretaker-header">
        <h1>Caretakers</h1>
        <button class="add-btn">Add Caretaker</button>
      </section>

      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search caretakers">
      </div>

      <section>
        <table>
          <thead>
            <tr>
              <th>Caretaker ID</th>
              <th>Name</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>101</td>
              <td>Ethan Bennett</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>102</td>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
              <tr>
              <td>103</td>
              <td>Liam Foster</td>
              <td>Elder Care</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>104</td>
              <td>Ava Morgan</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>105</td>
              <td>Noah Parker</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>106</td>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>107</td>
              <td>Jackson Cole</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
            <tr>
              <td>108</td>
              <td>Mia Fisher</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="bx bx-show"></i>
                <i class="bx bx-edit"></i>
                <i class="bx bx-trash"></i>
              </td>
            </tr>
         
            <!-- Add other caretaker rows here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>

