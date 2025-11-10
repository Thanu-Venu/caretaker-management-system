<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/leave_add.css">
</head>
<body>

<main class="main-content">
  <section class="form-section">
    <h1>Request Leave</h1>
    <form method="POST" action="<?php echo URLROOT; ?>/leaveCRUD/add">
      <label>Leave Type</label>
      <select name="leave_type" required>
        <option value="">Select Type</option>
        <option value="Vacation">Vacation</option>
        <option value="Sick Leave">Sick Leave</option>
        <option value="Personal">Personal</option>
      </select>

      <div class="row">
        <label>Start Date <input type="date" name="start_date" required></label>
        <label>End Date <input type="date" name="end_date" required></label>
      </div>

      <div class="row">
        <label>Start Time <input type="time" name="start_time" value="09:00" required></label>
        <label>End Time <input type="time" name="end_time" value="17:00" required></label>
      </div>

      <label>Reason</label>
      <textarea name="reason" placeholder="Enter reason for leave..." required></textarea>

      <button type="submit" class="submit-btn">Submit Request</button>
      <a href="<?php echo URLROOT; ?>/leaveController/index" class="cancel-btn">Cancel</a>
    </form>
  </section>
</main>
