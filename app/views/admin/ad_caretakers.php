<?php  
include_once APPROOT . "/views/templates/client/c_header.php"; 
include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_caretakers.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<<<<<<< HEAD
    <main class="main-content">
      <section class="caretaker-header">
        <h1>Caretaker Management</h1>
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
              <th>Name</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ethan Bennett</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa-solid fa-eye"></i>
                <i class="fa-solid fa-pen"></i>
                <i class="fa-solid fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="fa-solid fa-eye"></i>
                <i class="fa-solid fa-pen"></i>
                <i class="fa-solid fa-trash"></i>
              </td>
            </tr>
              <tr>
              <td>Liam Foster</td>
              <td>Elder Care</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Ava Morgan</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Noah Parker</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Jackson Cole</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Mia Fisher</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
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



<?php  include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php  include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_caretakers.css">
  <link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
        <main class="main-content">
      <section class="caretaker-header">
        <h1>Caretaker Management</h1>
        <button class="add-btn">Add Caretaker</button>
      </section>
=======
<main class="main-content">
>>>>>>> 0c7983bba4a11fb59e245f151d7232f48bed7f8e

  <!-- Header -->
  <section class="caretaker-header">
    <h1>Caretaker Management</h1>
    <button class="add-btn" onclick="window.location.href='/CMA/public/caretakerCRUD/add'">Add Caretaker</button>
  </section>

  <!-- Search -->
  <div class="search-wrapper">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Search caretakers" id="searchInput">
  </div>

  <!-- Caretaker Table -->
  <section>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Service</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($data['caretakers'])): ?>
          <?php foreach($data['caretakers'] as $caretaker): ?>
            <tr>
              <td><?= htmlspecialchars($caretaker['name']) ?></td>
              <td><?= htmlspecialchars($caretaker['service_type']) ?></td>
              <td>
                <span class="status <?= $caretaker['status']=='Active'?'active':'inactive' ?>">
                  <?= htmlspecialchars($caretaker['status']) ?>
                </span>
              </td>
              <td class="actions">
                      <a href="<?php echo URLROOT; ?>/CaretakerCRUD/edit/<?php echo $caretaker['id']; ?>"><i class="bx bx-edit"></i></a>              
                      <a href="<?php echo URLROOT; ?>/CaretakerCRUD/delete/<?php echo $caretaker['id']; ?>" onclick="return confirm('Are you sure you want to delete this caretaker?');"><i class="bx bx-trash"></i></a>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center;">No caretakers found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>

</main>

<!-- Optional JS for search filter -->
<script>
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function() {
  const filter = this.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
  });
});
</script>

</body>
</html>
