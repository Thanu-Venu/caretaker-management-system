<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Complaint #<?php echo htmlspecialchars($complaint['Id']); ?></title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/complaint_edit.css" />


</head>

<body>
  <main class="main-content">
    <h1>Edit Complaint</h1>

    <div class="form-card">
      <form action="/CMA/public/index.php?url=Complaint/update" method="POST">
        <!-- use real primary column name Id -->
        <input type="hidden" name="Id" value="<?php echo htmlspecialchars($complaint['Id']); ?>">

        <label>Client Name</label><br>
        <input type="text" disabled value="<?php echo htmlspecialchars($complaint['client_name']); ?>"><br>
        <input type="hidden" name="client_name" value="<?php echo htmlspecialchars($complaint['client_name']); ?>">

        <label>Caretaker Name</label><br>
        <input type="text" disabled name="caretaker_name" value="<?php echo htmlspecialchars($complaint['caretaker_name']); ?>" required><br>
        <input type="hidden" name="caretaker_name" value="<?php echo htmlspecialchars($complaint['caretaker_name']); ?>">

      <label>Category</label><br>
<select disabled>
  <?php
        $categories = ['Caretaker Behavior', 'Service Quality', 'Late Arrival', 'Unprofessional', 'Other'];
        foreach ($categories as $cat) {
          $sel = ($complaint['category'] === $cat) ? 'selected' : '';
          echo "<option value=\"" . htmlspecialchars($cat) . "\" $sel>" . htmlspecialchars($cat) . "</option>";
        }
        ?>
      </select>
      <input type="hidden" name="category" value="<?php echo htmlspecialchars($complaint['category']); ?>">
      <br>


        <label>Details</label><br>
          <textarea disabled rows="6"><?php echo htmlspecialchars($complaint['details']); ?></textarea><br>
          <input type="hidden" name="details" value="<?php echo htmlspecialchars($complaint['details']); ?>">
          
        <label>Status</label><br>
        <select name="status" required>
          <?php
          $statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];
          foreach ($statuses as $st) {
            $sel = ($complaint['status'] === $st) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($st) . "\" $sel>" . htmlspecialchars($st) . "</option>";
          }
          ?>
        </select><br><br>

        <div class="form-actions">
          <a href="/CMA/public/index.php?url=Complaint/index" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">Save Changes</button>
        </div>

      </form>
    </div>
  </main>
</body>

</html>