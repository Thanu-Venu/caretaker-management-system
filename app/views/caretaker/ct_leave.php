<?php include_once APPROOT . "/views/templates/caretaker/ct_header.php"; ?>
<?php include_once APPROOT . "/views/templates/caretaker/ct_sidebar.php"; ?>

<?php
$summary = $data['monthlySummary'] ?? ['limit' => 5, 'used' => 0, 'remaining' => 5, 'percentage' => 0, 'label' => '0 / 5 days used'];
$success = $data['success'] ?? '';
$warning = $data['warning'] ?? '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/caretaker/ct_leave.css">



</head>

<body>
    <main class="content">
        <div class="booking">
            <div class="card">
                <h2 >Leave Requests</h2>

                <div class="leave-summary-strip">
                    <div class="summary-item">
                        <span>Monthly Leave Limit</span>
                        <strong><?= (int)$summary['limit'] ?> days</strong>
                    </div>
                    <div class="summary-item">
                        <span>Used</span>
                        <strong><?= (int)$summary['used'] ?> days</strong>
                    </div>
                    <div class="summary-item">
                        <span>Remaining</span>
                        <strong><?= (int)$summary['remaining'] ?> days</strong>
                    </div>
                    <div class="summary-progress">
                        <span><?= htmlspecialchars($summary['label']) ?></span>
                        <div class="track">
                            <div class="fill" style="width: <?= (int)$summary['percentage'] ?>%"></div>
                        </div>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <?php if ($warning): ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($warning) ?></div>
                <?php endif; ?>

                <div class="top">
                    <button class="add-btn" onclick="window.location.href='<?php echo URLROOT; ?>/leaveCRUD/add'">Request Leave</button>
                </div>

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
                                            <?php if ($leave['status'] == 'Pending'): ?>
                                                <a href="<?php echo URLROOT; ?>/LeaveCRUD/edit/<?php echo $leave['id']; ?>">
                                                    <i class="bx bx-edit"></i>
                                                </a> |
                                                <a href="<?php echo URLROOT; ?>/LeaveCRUD/delete/<?php echo $leave['id']; ?>"
                                                    onclick="return confirm('Cancel this leave request?');">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span style="color: gray; font-style: italic;">Locked</span>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">No leave requests found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
            <script>
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
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