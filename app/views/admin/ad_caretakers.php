<?php  
include_once APPROOT . "/views/templates/admin/ad_header.php"; 
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
<main class="main-content">

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
