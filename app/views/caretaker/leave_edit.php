<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<?php
$leave = $data['leave'];
$errors = $data['errors'] ?? [];
$warnings = $data['warnings'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Leave Request</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/leave_edit.css">
</head>

<body>
    <main class="main-content">
        <section class="form-section">
            <h1>Edit Leave Request</h1>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($warnings)): ?>
                <div class="warning-box">
                    <?php foreach ($warnings as $warning): ?>
                        <p><?php echo htmlspecialchars($warning); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo URLROOT; ?>/LeaveCRUD/edit/<?php echo (int)$leave->id; ?>">
                <label>Leave Type</label>
                <select name="leave_type" required>
                    <option value="Vacation" <?php echo ($leave->leave_type === 'Vacation') ? 'selected' : ''; ?>>Vacation</option>
                    <option value="Sick Leave" <?php echo ($leave->leave_type === 'Sick Leave') ? 'selected' : ''; ?>>Sick Leave</option>
                    <option value="Personal Leave" <?php echo ($leave->leave_type === 'Personal Leave') ? 'selected' : ''; ?>>Personal Leave</option>
                    <option value="Maternity Leave" <?php echo ($leave->leave_type === 'Maternity Leave') ? 'selected' : ''; ?>>Maternity Leave</option>
                </select>

                <div class="row">
                    <label>Start Date <input type="date" name="start_date" value="<?php echo htmlspecialchars($leave->start_date); ?>" required></label>
                    <label>End Date <input type="date" name="end_date" value="<?php echo htmlspecialchars($leave->end_date); ?>" required></label>
                </div>

                <div class="row">
                    <label>Start Time <input type="time" name="start_time" value="<?php echo htmlspecialchars($leave->start_time); ?>" required></label>
                    <label>End Time <input type="time" name="end_time" value="<?php echo htmlspecialchars($leave->end_time); ?>" required></label>
                </div>

                <label>Reason</label>
                <textarea name="reason" required><?php echo htmlspecialchars($leave->reason); ?></textarea>

                <button type="submit" class="submit-btn">Update Leave</button>
                <a href="<?php echo URLROOT; ?>/LeaveCRUD/index" class="cancel-btn">Cancel</a>
            </form>
        </section>
    </main>
</body>

</html>