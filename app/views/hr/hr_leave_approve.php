<?php
$leaveDetails = $data['leaveDetails'] ?? [];
$impact = $data['impact'] ?? [];
$usage = $data['monthlyUsage'] ?? ['used_before' => 0, 'request_days' => 0, 'used_after' => 0, 'limit' => 5];

$hrPageTitle = 'Approve leave — HR';
$hrExtraCss  = ['hr/hr_leave_approve.css'];
include_once APPROOT . '/views/templates/hr/hr_layout_head.php';
include_once APPROOT . '/views/templates/hr/hr_header.php';
include_once APPROOT . '/views/templates/hr/hr_sidebar.php';

$hasAffected = !empty($data['affected']);
?>

<main class="main-content laPage">
    <?php if (!empty($data['error'])): ?>
        <div class="error-message" role="alert"><?= htmlspecialchars((string) $data['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <header class="laBar">
        <a href="<?= htmlspecialchars(URLROOT . '/HrLeave/index', ENT_QUOTES, 'UTF-8') ?>"
            class="laBack"
            title="Back to leave list"
            aria-label="Back to leave list">
            <i class="bx bx-arrow-back" aria-hidden="true"></i>
        </a>
        <div class="laBarMain">
            <h1 class="page-title">Approve leave</h1>
            <?php if (!empty($impact['count'])): ?>
                <span class="laFlag">Reassign required</span>
            <?php endif; ?>
        </div>
        <button type="button" id="leaveApproveOpenContext" class="btn secondary btn-sm laCtxBtn">
            <i class="bx bx-show" aria-hidden="true"></i> Impact &amp; usage
        </button>
    </header>

    <div class="laCard">
        <div class="leave-info leave-info--compact">
            <div class="info-row">
                <span class="label">Leave ID</span>
                <span class="value"><?= (int) $data['leave']->id ?></span>
            </div>
            <div class="info-row">
                <span class="label">Caregiver name</span>
                <span class="value"><?= htmlspecialchars($leaveDetails['caretaker_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Caregiver ID</span>
                <span class="value"><?= (int) $data['leave']->user_id ?></span>
            </div>
            <div class="info-row">
                <span class="label">Leave type</span>
                <span class="value"><?= htmlspecialchars((string) $data['leave']->leave_type, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Date range</span>
                <span class="value">
                    <?= htmlspecialchars((string) $data['leave']->start_date, ENT_QUOTES, 'UTF-8') ?> → <?= htmlspecialchars((string) $data['leave']->end_date, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <div class="info-row">
                <span class="label">Total days</span>
                <span class="value"><?= (int) $usage['request_days'] ?> day(s)</span>
            </div>
            <div class="info-row">
                <span class="label">Time</span>
                <span class="value">
                    <?= htmlspecialchars((string) ($data['leave']->start_time ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    <?= ($data['leave']->start_time && $data['leave']->end_time) ? '→' : '' ?>
                    <?= htmlspecialchars((string) ($data['leave']->end_time ?? ''), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
            <div class="info-row info-row--wide">
                <span class="label">Reason</span>
                <span class="value value--multiline"><?= nl2br(htmlspecialchars((string) $data['leave']->reason, ENT_QUOTES, 'UTF-8')) ?></span>
            </div>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value"><?= htmlspecialchars((string) $data['leave']->status, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <form class="approve-form" method="POST" action="<?= URLROOT ?>/HrLeave/approve_submit">
            <input type="hidden" name="leave_id" value="<?= (int) $data['leave']->id ?>">

            <div class="field full">
                <label for="replacement_caretaker_id">Replacement caregiver</label>
                <select id="replacement_caretaker_id" name="replacement_caretaker_id" class="form-input" <?= $hasAffected ? 'required' : '' ?>>
                    <option value="">— <?= $hasAffected ? 'Required' : 'Optional' ?> —</option>
                    <?php foreach ($data['caretakers'] as $ct): ?>
                        <option value="<?= (int) $ct['id'] ?>">
                            <?= htmlspecialchars((string) $ct['name'], ENT_QUOTES, 'UTF-8') ?> (ID: <?= (int) $ct['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!$hasAffected): ?>
                <p class="laHint">No bookings affected — replacement not required.</p>
            <?php else: ?>
                <p class="laHint">Bookings are affected by this leave. Select a replacement caregiver to reassign all affected bookings. Click <strong>Impact &amp; usage</strong> to review details.</p>
            <?php endif; ?>

            <div class="field full">
                <label for="hr_note">HR note (optional)</label>
                <textarea id="hr_note" name="hr_note" class="form-input" rows="3" placeholder="Add a note (optional)…"></textarea>
            </div>

            <div class="laFoot">
                <button type="submit" class="btn primary" <?= !empty($data['error']) ? 'disabled' : '' ?>
                    data-app-confirm="Approve this leave and reassign all affected bookings to the selected caregiver?">
                    <i class="bx bx-check" aria-hidden="true"></i> Approve leave
                </button>
            </div>
        </form>
    </div>
</main>

<div id="leaveApproveContextModal" class="modal readModal" aria-hidden="true">
    <div class="modal-content readPanel laModal" role="dialog" aria-modal="true"
        aria-labelledby="leaveApproveContextTitle">
        <button type="button" class="modal-close readClose" data-leave-approve-context-close aria-label="Close">
            <i class="bx bx-x" aria-hidden="true"></i>
        </button>
        <header class="readHead">
            <span class="readIcon" aria-hidden="true"><i class="bx bx-show"></i></span>
            <h3 id="leaveApproveContextTitle" class="readTitle">Impact &amp; monthly usage</h3>
        </header>
        <div class="laScroll">
            <?php if (!empty($impact['count'])): ?>
                <div class="impact-banner impact-banner--in-modal">
                    This leave overlaps active bookings. Assign a replacement before approving.
                    <br>
                    Affected bookings: <strong><?= (int) $impact['count'] ?></strong>
                    <?php if (!empty($impact['booking_ids'])): ?>
                        <br>Booking IDs: <?= htmlspecialchars(implode(', ', $impact['booking_ids']), ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <dl class="laUsage">
                <dt>Monthly usage (leave month)</dt>
                <dd>
                    <?= (int) $usage['used_before'] ?> + <?= (int) $usage['request_days'] ?> = <strong><?= (int) $usage['used_after'] ?></strong>
                    / <?= (int) $usage['limit'] ?> days allowed
                </dd>
            </dl>

            <h4 class="laSub">Affected bookings</h4>
            <?php if (!$hasAffected): ?>
                <p class="laEmpty">No active bookings during this leave period.</p>
            <?php else: ?>
                <div class="laTblWrap">
                    <table class="table booking-table laTbl">
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Client ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Service</th>
                                <th>Basis</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['affected'] as $b): ?>
                                <tr>
                                    <td><?= (int) $b['id'] ?></td>
                                    <td><?= (int) $b['client_id'] ?></td>
                                    <td><?= htmlspecialchars((string) $b['booking_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) $b['status'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) $b['service_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) $b['basis'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) $b['duration'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script defer src="<?= URLROOT ?>/public/js/hr/hr_leave_approve.js"></script>
<?php include_once APPROOT . '/views/templates/hr/hr_layout_close.php'; ?>
