<?php
/** @var array $complaints Client complaints */
/** @var array $ct_complaints Caregiver complaints */
$complaints    = $complaints ?? [];
$ct_complaints = $ct_complaints ?? [];

// Debug: Check data in view
error_log("View - CT Complaints count: " . count($ct_complaints));
error_log("View - Client Complaints count: " . count($complaints));

if (!empty($ct_complaints)) {
    error_log("View - First CT Complaint: " . print_r($ct_complaints[0], true));
}

$hrPageTitle = 'Complaints management — HR';
$hrExtraCss  = ['hr/hr_complaint.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';
?>

<main class="main-content issuesPage">
    <header class="page-header">
        <h1 class="page-title">Complaints management</h1>
    </header>

    <div class="issueTabs" role="tablist" aria-label="Complaint type">
        <button type="button" class="issueTab active" data-tab="c_complaint" onclick="switchComplaintTab('c_complaint', event)">
            Client complaints
        </button>
        <button type="button" class="issueTab" data-tab="ct_complaint" onclick="switchComplaintTab('ct_complaint', event)">
            Caregiver complaints
        </button>
    </div>

    <section class="tab-content issuePanel active" id="c_complaint" role="tabpanel">
        <div class="table-container issueWrap">
            <table class="table booking-table issueTable" data-table-collapse="off">
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
                                <td class="issueDetails"><?= htmlspecialchars((string) ($c['details'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['complaint_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="issueStatus">
                                    <span class="status-pill issuePill <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <form method="POST" action="<?= htmlspecialchars(URLROOT . '/public/index.php?url=Complaint/updateStatus', ENT_QUOTES, 'UTF-8') ?>" class="issueForm">
                                        <input type="hidden" name="Id" value="<?= (int) ($c['Id'] ?? 0) ?>">
                                        <select name="status" class="form-input issuePick" aria-label="Update status">
                                            <option value="Open" <?= $status === 'Open' ? 'selected' : '' ?>>Open</option>
                                            <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" class="btn secondary btn-sm issueSave">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="issueZero">No client complaints</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="tab-content issuePanel" id="ct_complaint" role="tabpanel">
        <div class="table-container issueWrap">
            <table class="table booking-table issueTable" data-table-collapse="off">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Caregiver</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ct_complaints)): ?>
                        <?php foreach ($ct_complaints as $c): ?>
                            <?php
                            $status = (string) ($c['status'] ?? '');
                            $badgeClass = strtolower(str_replace(' ', '-', $status));
                            ?>
                            <tr>
                                <td><?= (int) ($c['complaint_id'] ?? 0) ?></td>
                                <td><?= htmlspecialchars((string) ($c['caretaker_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['client_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="issueDetails"><?= htmlspecialchars((string) ($c['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($c['service_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="issueStatus">
                                    <span class="status-pill issuePill <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <form method="POST" action="<?= htmlspecialchars(URLROOT . '/public/index.php?url=Complaint/updateCaretakerComplaintStatus', ENT_QUOTES, 'UTF-8') ?>" class="issueForm">
                                        <input type="hidden" name="complaint_id" value="<?= (int) ($c['complaint_id'] ?? 0) ?>">
                                        <select name="status" class="form-input issuePick" aria-label="Update status">
                                            <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="In Progress" <?= $status === 'In Progress' ? 'selected' : '' ?>>In progress</option>
                                            <option value="Resolved" <?= $status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                        </select>
                                        <button type="submit" class="btn secondary btn-sm issueSave">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="issueZero">No caregiver complaints</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="<?= URLROOT ?>/public/js/hr/hr_complaint.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
