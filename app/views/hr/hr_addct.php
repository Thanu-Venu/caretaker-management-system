
<?php  
include_once APPROOT . "/views/templates/hr/hr_header.php"; 
include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caregiver Management</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_addct.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<main class="main-content">

  <!-- Success Message Alert -->
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success" style="padding: 12px 16px; background: #d4edda; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 20px; border-radius: 4px;">
      <strong>Success:</strong> <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- Header -->
  <section class="caretaker-header">
    <h1>Caregiver Management</h1>
    <button class="add-btn" onclick="window.location.href='/CMA/public/HRCaretakerCRUD/add'">Add Caregiver</button>
  </section>

 <?php
$filters = $data['filters'] ?? [];
?>

<div class="filter-bar">
  <form method="get" action="<?= URLROOT ?>/HRCaretakerCRUD/list">

    <select name="service_type">
      <option value="">All Services</option>
      <option value="Elder Care" <?= (($filters['service_type'] ?? '')==='Elder Care')?'selected':'' ?>>Elder Care</option>
      <option value="Babysitter" <?= (($filters['service_type'] ?? '')==='Babysitter')?'selected':'' ?>>Babysitter</option>
      <option value="Maid" <?= (($filters['service_type'] ?? '')==='Maid')?'selected':'' ?>>Maid</option>
    </select>

    <select name="status">
      <option value="">All Status</option>
      <option value="Active" <?= (($filters['status'] ?? '')==='Active')?'selected':'' ?>>Active</option>
      <option value="Inactive" <?= (($filters['status'] ?? '')==='Inactive')?'selected':'' ?>>Inactive</option>
    </select>

    <input type="text" name="location" placeholder="Location"
           value="<?= htmlspecialchars($filters['location'] ?? '') ?>">

    <input type="text" name="q" placeholder="Search name"
           value="<?= htmlspecialchars($filters['q'] ?? '') ?>">

    <button type="submit">Apply</button>

    <a class="reset-btn" href="<?= URLROOT ?>/HRCaretakerCRUD/list">Reset</a>
  </form>
</div>
<div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Service type</th>
          <th>Experience</th>
          <th>Location</th>
          <th>Phone number</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($data['caretakers'])): ?>
          <?php foreach($data['caretakers'] as $caretaker): ?>
            <tr>
              <td><?= htmlspecialchars($caretaker['name']) ?></td>
              <td><?= htmlspecialchars($caretaker['email'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['service_type'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['experience'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['location'] ?? '') ?></td>
              <td><?= htmlspecialchars($caretaker['phone'] ?? '') ?></td>
              <td>
                <span class="status <?= $caretaker['status']=='Active'?'active':'inactive' ?>">
                  <?= htmlspecialchars($caretaker['status']) ?>
                </span>
              </td>
              <td class="actions">
                      <a href="<?php echo URLROOT; ?>/HRCaretakerCRUD/viewCaretaker/<?php echo $caretaker['id']; ?>" title="View"><i class="bx bx-show"></i></a>
                      <a href="<?php echo URLROOT; ?>/HRCaretakerCRUD/edit/<?php echo $caretaker['id']; ?>" title="Edit"><i class="bx bx-edit"></i></a>              
                      <a href="<?php echo URLROOT; ?>/HRCaretakerCRUD/delete/<?php echo $caretaker['id']; ?>" onclick="return confirm('Are you sure you want to delete this caretaker?');" title="Delete"><i class="bx bx-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align:center;">No caregivers found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
<?php if (($data['totalPages'] ?? 1) > 1): ?>
  <div class="pagination">
    <?php for ($p=1; $p <= $data['totalPages']; $p++): ?>
      <a class="<?= ($p == ($data['page'] ?? 1)) ? 'active' : '' ?>"
         href="<?= URLROOT ?>/HRCaretakerCRUD/list?
            page=<?= $p ?>
            &service_type=<?= urlencode($filters['service_type'] ?? '') ?>
            &status=<?= urlencode($filters['status'] ?? '') ?>
            &location=<?= urlencode($filters['location'] ?? '') ?>
            &q=<?= urlencode($filters['q'] ?? '') ?>">
        <?= $p ?>
      </a>
    <?php endfor; ?>
  </div>
<?php endif; ?>


    </div>
   


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