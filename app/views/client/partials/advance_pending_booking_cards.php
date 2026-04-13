<?php
/**
 * Cards inside the "Advance payments required" modal (no visible booking ID).
 *
 * @var array<int, array<string, mixed>> $pendingAdvanceList
 */
if (empty($pendingAdvanceList) || !is_array($pendingAdvanceList)) {
    return;
}
foreach ($pendingAdvanceList as $p) {
    $bid = (int) ($p['booking_id'] ?? 0);
    if ($bid < 1) {
        continue;
    }
    $bkDate = (string) ($p['booking_date'] ?? '');
    $svcStart = trim((string) ($p['service_start_date'] ?? ''));
    $showSvcStart = $svcStart !== '' && $svcStart !== '0000-00-00' && $svcStart !== $bkDate;
    $district = trim((string) ($p['district'] ?? ''));
    $tp = (float) ($p['total_payment'] ?? 0);
    $adv = (float) ($p['advance_amount'] ?? 0);
    ?>
    <div class="advance-modal-item">
        <div class="advance-modal-item__meta">
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Caregiver</span>
                <span class="advance-modal-item__v"><?= htmlspecialchars((string) ($p['caretaker_name'] ?? '')) ?></span>
            </div>
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Service</span>
                <span class="advance-modal-item__v"><?= htmlspecialchars((string) ($p['service_type'] ?? '')) ?></span>
            </div>
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Date</span>
                <span class="advance-modal-item__v"><?= $bkDate !== '' ? htmlspecialchars(date('Y-m-d', strtotime($bkDate))) : '—' ?></span>
            </div>
            <?php if ($showSvcStart): ?>
                <div class="advance-modal-item__line">
                    <span class="advance-modal-item__k">Service start</span>
                    <span class="advance-modal-item__v"><?= htmlspecialchars(date('Y-m-d', strtotime($svcStart))) ?></span>
                </div>
            <?php endif; ?>
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Preferred time</span>
                <span class="advance-modal-item__v"><?= htmlspecialchars((string) ($p['preferred_time'] ?? '')) ?></span>
            </div>
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Duration</span>
                <span class="advance-modal-item__v"><?= htmlspecialchars((string) (($p['duration'] ?? '') . ' ' . ($p['basis'] ?? ''))) ?></span>
            </div>
            <?php if ($district !== ''): ?>
                <div class="advance-modal-item__line">
                    <span class="advance-modal-item__k">Service area</span>
                    <span class="advance-modal-item__v"><?= htmlspecialchars($district) ?></span>
                </div>
            <?php endif; ?>
            <div class="advance-modal-item__line">
                <span class="advance-modal-item__k">Total</span>
                <span class="advance-modal-item__v advance-modal-item__money">LKR <?= number_format($tp, 2) ?></span>
            </div>
            <?php if ($adv > 0): ?>
                <div class="advance-modal-item__line">
                    <span class="advance-modal-item__k">Advance due</span>
                    <span class="advance-modal-item__v advance-modal-item__money">LKR <?= number_format($adv, 2) ?></span>
                </div>
            <?php endif; ?>
        </div>
        <a class="btn advance-modal-item__pay" href="<?= URLROOT ?>/client/c_makePayment?booking_id=<?= $bid ?>">Pay now</a>
    </div>
    <?php
}
