<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leave Management</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_leave.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  

</head>
<body>
    <main class="content">
        <section>
        

    <button class="add-btn" onclick="window.location.href='<?php echo URLROOT; ?>/leaveCRUD/add'">Request Leave</button>


    <div class="card">
        <h2>Leave Requests</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Dates</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['leaves'])): ?>
                        <?php foreach ($data['leaves'] as $leave): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave['start_date'] . " – " . $leave['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                <td><span class="status <?php echo strtolower($leave['status']); ?>"><?php echo $leave['status']; ?></span></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/leaveController/edit/<?php echo $leave['id']; ?>"><i class="bx bx-edit"></i></a> |
                                    <a href="<?php echo URLROOT; ?>/leaveController/delete/<?php echo $leave['id']; ?>" onclick="return confirm('Delete this leave request?');"><i class="bx bx-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No leave requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Optional: Search Filter -->
<script>
const searchInput = document.getElementById('searchInput');
if(searchInput){
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
}
</script>

</body>
</html>
