<?php
$clientPageTitle = 'Complaints — SmartCare';
$clientExtraCss  = ['client/c_complaintlist.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$complaintsList = $data['complaints'] ?? ($complaints ?? []);
?>

<main class="main-content">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="flash success"><?= htmlspecialchars((string) $_SESSION['flash_message'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
    <header class="page-header">
        <div>
            <h1 class="page-title">Registered complaints</h1>
            <p class="text-muted">Your submitted complaints and categories.</p>
        </div>
        <div class="header-actions">
            <a class="btn" href="<?= URLROOT ?>/public/index.php?url=Complaint/complaintReg">Register complaint</a>
        </div>
    </header>

    <div class="table-container">
        <table class="client-table">
            <thead>
                <tr>
                    <th>Client name</th>
                    <th>Caretaker</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Registered</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($complaintsList)): ?>
                    <?php foreach ($complaintsList as $complaint): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) ($complaint['client_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($complaint['caretaker_name'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($complaint['category'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($complaint['details'] ?? '')) ?></td>
                            <td>
                                <?php $registeredAt = strtotime((string) ($complaint['complaint_date'] ?? '')); ?>
                                <?php if ($registeredAt !== false): ?>
                                    <span class="complaint-date"><?= date('Y-m-d', $registeredAt) ?></span>
                                    <span class="complaint-time text-muted"><?= date('h:i A', $registeredAt) ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars((string) ($complaint['complaint_date'] ?? '')) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $status = strtolower((string)($complaint['status'] ?? 'pending'));
                                    $statusClass = 'complaint-status';
                                    if ($status === 'resolved') $statusClass .= ' resolved';
                                    elseif ($status === 'rejected') $statusClass .= ' rejected';
                                    elseif ($status === 'in progress' || $status === 'inprogress') $statusClass .= ' inprogress';
                                ?>
                                <span class="<?= $statusClass ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">No complaints registered.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
