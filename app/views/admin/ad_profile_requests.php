<?php include_once APPROOT . "/views/templates/admin/ad_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<?php
$requests = $data['requests'] ?? [];
$selectedStatus = $data['selectedStatus'] ?? 'All';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update Requests</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_profile_requests.css">
    <!-- Design System Override (ensures consistency) -->
</head>

<body>
    <main class="content">
        <section>
            <h1>Caretaker Profile Update Requests</h1>

            <?php if (!empty($_SESSION['success'])): ?>
                <p class="flash success"><?= htmlspecialchars($_SESSION['success']) ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (!empty($_SESSION['error'])): ?>
                <p class="flash error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="filter-row">
                <label for="status">Status</label>
                <select id="status" onchange="applyFilter()">
                    <option value="All" <?= $selectedStatus === 'All' ? 'selected' : '' ?>>All</option>
                    <option value="Pending" <?= $selectedStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $selectedStatus === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $selectedStatus === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Caretaker ID</th>
                            <th>Current Profile</th>
                            <th>Requested Profile</th>
                            <th>Status</th>
                            <th>Requested At</th>
                            <th>Admin Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="7">No profile update requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td><?= (int)$r['caretaker_id'] ?></td>
                                    <td>
                                        <strong>Name:</strong> <?= htmlspecialchars($r['current_name'] ?? '-') ?><br>
                                        <strong>Email:</strong> <?= htmlspecialchars($r['current_email'] ?? '-') ?><br>
                                        <strong>Phone:</strong> <?= htmlspecialchars($r['current_phone'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <strong>Name:</strong> <?= htmlspecialchars($r['requested_name'] ?? '-') ?><br>
                                        <strong>Email:</strong> <?= htmlspecialchars($r['requested_email'] ?? '-') ?><br>
                                        <strong>Phone:</strong> <?= htmlspecialchars($r['requested_phone'] ?? '-') ?><br>
                                        <strong>Experience:</strong> <?= htmlspecialchars($r['requested_experience'] ?? '-') ?><br>
                                        <strong>Location:</strong> <?= htmlspecialchars($r['requested_location'] ?? '-') ?><br>
                                        <strong>Qualifications:</strong> <?= htmlspecialchars($r['requested_qualifications'] ?? '-') ?>
                                    </td>
                                    <td><span class="status <?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                                    <td><?= htmlspecialchars($r['created_at'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($r['admin_note'] ?? '-') ?></td>
                                    <td>
                                        <?php if (($r['status'] ?? '') === 'Pending'): ?>
                                            <form method="POST" action="<?= URLROOT ?>/admin/approve_profile_request" class="action-form">
                                                <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                                                <textarea name="admin_note" placeholder="Optional note"></textarea>
                                                <button type="submit" class="btn approve">Approve</button>
                                            </form>
                                            <form method="POST" action="<?= URLROOT ?>/admin/reject_profile_request" class="action-form">
                                                <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                                                <textarea name="admin_note" placeholder="Reason for rejection"></textarea>
                                                <button type="submit" class="btn reject">Reject</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="done">Reviewed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        function applyFilter() {
            const status = document.getElementById('status').value;
            const params = new URLSearchParams(window.location.search);
            params.set('status', status);
            window.location = window.location.pathname + '?' + params.toString();
        }
    </script>
</body>

</html>
