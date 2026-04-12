<?php
/** @var array $complaints Client complaints */
/** @var array $ct_complaints Caregiver complaints */
$complaints    = $complaints ?? [];
$ct_complaints = $ct_complaints ?? [];

$hrPageTitle = 'Complaints management — HR';
$hrExtraCss  = ['hr/hr_complaint.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content hr-complaints-page">
    <header class="page-header">
        <h1 class="page-title">Complaints management</h1>
    </header>

    <div class="complaint-tabs" role="tablist" aria-label="Complaint type">
        <button type="button" class="complaint-tab-btn active" data-tab="c_complaint" onclick="switchComplaintTab('c_complaint', event)">
            Client complaints
        </button>
        <button type="button" class="complaint-tab-btn" data-tab="ct_complaint" onclick="switchComplaintTab('ct_complaint', event)">
            Caregiver complaints
        </button>
    </div>

    <section class="tab-content complaint-tab-panel active" id="c_complaint" role="tabpanel">
        <div class="table-container complaint-table-wrap">
            <table class="table booking-table complaint-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Caregiver</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($complaints)): ?>
                        <?php foreach ($complaints as $c): ?>
                            <?php
                            $status = (string) ($c['status'] ?? '');
                            $badgeClass = strtolower(str_replace(' ', '-', $status));
                            ?>
                            <tr>
                                <td><?= (int) ($c['Id'] ?? 0) ?></td>
                                <td><?= htmlspecialchars((string) ($c['client_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['caretaker_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="complaint-details-cell"><?= htmlspecialchars((string) ($c['details'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['complaint_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="complaint-status-cell">
                                    <span class="status-pill complaint-status-pill complaint-status-pill--<?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <form method="POST" action="<?= htmlspecialchars(URLROOT . '/public/index.php?url=Complaint/updateStatus', ENT_QUOTES, 'UTF-8') ?>" class="complaint-status-form">
                                        <input type="hidden" name="Id" value="<?= (int) ($c['Id'] ?? 0) ?>">
                                        <select name="status" class="form-input complaint-status-select" aria-label="Update status">
                                            <option value="Open" <?= $status === 'Open' ? 'selected' : '' ?>>Open</option>
                                            <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" class="btn secondary btn-sm complaint-update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="complaint-empty">No client complaints</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="tab-content complaint-tab-panel" id="ct_complaint" role="tabpanel">
        <div class="table-container complaint-table-wrap">
            <table class="table booking-table complaint-table" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Caregiver</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ct_complaints)): ?>
                        <?php foreach ($ct_complaints as $c): ?>
                            <?php $st = (string) ($c['status'] ?? ''); ?>
                            <tr>
                                <td><?= (int) ($c['complaint_id'] ?? 0) ?></td>
                                <td><?= htmlspecialchars((string) ($c['caretaker_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['client_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="complaint-details-cell"><?= htmlspecialchars((string) ($c['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['service_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="status-pill complaint-status-pill complaint-status-pill--<?= htmlspecialchars(strtolower(str_replace(' ', '-', $st)), ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($st, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="actions complaint-actions-cell">
                                    <form method="POST" action="<?= htmlspecialchars(URLROOT . '/public/index.php?url=Complaint/updateCaretakerComplaintStatus', ENT_QUOTES, 'UTF-8') ?>" class="complaint-ct-form">
                                        <input type="hidden" name="complaint_id" value="<?= (int) ($c['complaint_id'] ?? 0) ?>">
                                        <select name="action" class="form-input complaint-status-select" aria-label="Update status">
                                            <option value="Pending" <?= $st === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="In Progress" <?= $st === 'In Progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="Resolved" <?= $st === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" class="btn secondary btn-sm complaint-update-btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="complaint-empty">No caregiver complaints</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="<?= URLROOT ?>/public/js/hr/hr_complaint.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
