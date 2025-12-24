<?php include_once APPROOT . "/views/templates/hr/hr_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Management</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_leave.css">
</head>

<body>
<main class="content">

<h1>Leave Management</h1>

<!-- FILTER -->
<div class="filter-section">
    <form method="GET" action="<?php echo URLROOT; ?>/LeaveApproval/index">
        <select name="status" class="filter-select">
            <option value="">All</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
        <button class="apply-filters-btn">Apply</button>
        <a href="<?php echo URLROOT; ?>/LeaveApproval/index" class="cancel-filters-btn">Reset</a>
    </form>
</div>

<h2>Leave Requests</h2>

<div class="table-container">
<table class="leave-table">
<thead>
<tr>
    <th>Caretaker ID</th>
    <th>Name</th>
    <th>Leave Type</th>
    <th>Start</th>
    <th>End</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php if(!empty($data['leaves'])): ?>
<?php foreach($data['leaves'] as $leave): ?>
<tr>
    <td><?php echo $leave['caretaker_id']; ?></td>
    <td><?php echo htmlspecialchars($leave['caretaker_name']); ?></td>
    <td><?php echo $leave['leave_type']; ?></td>
    <td><?php echo $leave['start_date']; ?></td>
    <td><?php echo $leave['end_date']; ?></td>

    <span class="status <?php echo strtolower($leave['status']); ?>">
            <?php echo $leave['status']; ?>
    </span>

    <td>
    <?php if($leave->status == 'Pending'): ?>
        <a href="<?php echo URLROOT; ?>/LeaveApproval/approve/<?php echo $leave->id; ?>" 
           class="action-btn approve-btn"
           onclick="return confirm('Approve this leave request?')">Approve</a>

        <a href="<?php echo URLROOT; ?>/LeaveApproval/reject/<?php echo $leave->id; ?>" 
           class="action-btn reject-btn"
           onclick="return confirm('Reject this leave request?')">Reject</a>
    <?php else: ?>
        <span class="view-only">Completed</span>
    <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="7">No leave requests found.</td>
</tr>
<?php endif; ?>
</tbody>

</table>
</div>

</main>
</body>
</html>
