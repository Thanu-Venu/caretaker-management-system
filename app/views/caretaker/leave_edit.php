<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

$leave = $data['leave'];
?>
<html>
  <title>Edit Leave Request</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/leave_edit.css">
</html> 
<main class="main-content">
  <section class="form-section">
    <h1>Edit Leave Request</h1>
    <form method="POST" action="<?php echo URLROOT; ?>/LeaveCRUD/edit/<?php echo $data['leave']->id; ?>">
      <label>Leave Type</label>
      <select name="leave_type" required>
        <option value="Vacation" <?php echo ($data['leave']->leave_type == 'Vacation') ? 'selected' : ''; ?>>Vacation</option>
        <option value="Sick Leave" <?php echo ($data['leave']->leave_type == 'Sick Leave') ? 'selected' : ''; ?>>Sick Leave</option>
        <option value="Personal Leave" <?php echo ($data['leave']->leave_type == 'Personal Leave') ? 'selected' : ''; ?>>Personal Leave</option>
        <option value="Maternity Leave" <?php echo ($data['leave']->leave_type == 'Maternity Leave') ? 'selected' : ''; ?>>Maternity Leave</option>
      </select>

      <div class="row">
        <label>Start Date <input type="date" name="start_date" value="<?php echo $data['leave']->start_date; ?>" required></label>
        <label>End Date <input type="date" name="end_date" value="<?php echo $data['leave']->end_date; ?>" required></label>
      </div>

      <div class="row">
        <label>Start Time <input type="time" name="start_time" value="<?php echo $data['leave']->start_time; ?>" required></label>
        <label>End Time <input type="time" name="end_time" value="<?php echo $data['leave']->end_time; ?>" required></label>
      </div>

      <label>Reason</label>
      <textarea name="reason" required><?php echo $data['leave']->reason; ?></textarea>

      <button type="submit" class="submit-btn">Update Leave</button>
      <a href="<?php echo URLROOT; ?>/LeaveCRUD/index" class="cancel-btn">Cancel</a>
    </form>
  </section>
</main>
