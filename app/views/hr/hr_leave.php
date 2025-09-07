<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/hr/hr_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/hr/hr_leave.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <main class="content">
        <h1>Leave Management</h1>

        <div class="filter-section">
            <div class="filter-group">
                <select class="filter-select">
                    <option disabled selected>Select Caregiver</option>
                    <option>Emily Carter</option>
                    <option>David Lee</option>
                    <option>Sarah Jones</option>
                    <option>Michael Brown</option>
                    <option>Jessica Wilson</option>
                </select>
                <div class="select-arrow"></div>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option disabled selected>Select Status</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                </select>
                <div class="select-arrow"></div>
            </div>
            <button class="apply-filters-btn">Apply Filters</button>
            <button class="cancel-filters-btn">Cancel Filters</button>
        </div>

        <h2>Leave Requests</h2>

        <table class="leave-table">
            <thead>
                <tr>
                    <th>Caregiver Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Emily Carter</td>
                    <td>Vacation</td>
                    <td>2024-07-15</td>
                    <td>2024-07-20</td>
                    <td><span class="status pending">Pending</span></td>
                    <td>
                        <button class="action-btn approve-btn">Approve</button>
                        <button class="action-btn reject-btn">Reject</button>
                    </td>
                </tr>
                <tr>
                    <td>David Lee</td>
                    <td>Sick Leave</td>
                    <td>2024-07-10</td>
                    <td>2024-07-12</td>
                    <td><span class="status approved">Approved</span></td>
                    <td>
                        <button class="action-btn view-btn">View</button>
                    </td>
                </tr>
                <tr>
                    <td>Sarah Jones</td>
                    <td>Personal Leave</td>
                    <td>2024-07-22</td>
                    <td>2024-07-25</td>
                    <td><span class="status pending">Pending</span></td>
                    <td>
                        <button class="action-btn approve-btn">Approve</button>
                        <button class="action-btn reject-btn">Reject</button>
                    </td>
                </tr>
                <tr>
                    <td>Michael Brown</td>
                    <td>Vacation</td>
                    <td>2024-08-05</td>
                    <td>2024-08-10</td>
                    <td><span class="status rejected">Rejected</span></td>
                    <td>
                        <button class="action-btn view-btn">View</button>
                    </td>
                </tr>
                <tr>
                    <td>Jessica Wilson</td>
                    <td>Maternity Leave</td>
                    <td>2024-09-01</td>
                    <td>2024-12-01</td>
                    <td><span class="status pending">Pending</span></td>
                    <td>
                        <button class="action-btn approve-btn">Approve</button>
                        <button class="action-btn reject-btn">Reject</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>

<script src="<?php echo URLROOT; ?>/public/js/hr/hr_leave.js"></script>
</body>
</html>