<?php
require_once APPROOT . '/views/client/partials/caretaker_skills.php';
$ct = $data['caretaker'];
$clientPageTitle = 'Caregiver profile — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_ctprofileview.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$ratingRaw = $ct['rating'] ?? null;
$ratingNum = ($ratingRaw !== null && $ratingRaw !== '') ? (float) $ratingRaw : null;
$ratingLabel = $ratingNum !== null ? number_format($ratingNum, 1) . ' / 5' : 'Not yet rated';
$skillParts = client_caretaker_skill_parts((string) ($ct['qualifications'] ?? ''));
$ctId = (int) ($ct['id'] ?? 0);
$bookingContext = isset($data['bookingContext']) && is_array($data['bookingContext']) ? $data['bookingContext'] : [];
$bookQuery = array_merge(
    ['url' => 'client/c_book', 'id' => (string) $ctId],
    $bookingContext
);
$bookUrl = URLROOT . '/public?' . http_build_query($bookQuery);
?>

<main class="main-content client-ct-profile-page">
    <div class="dashboard-layout">
        <header class="page-header client-ct-profile-page__header">
            <div>
                <h1 class="page-title">Caregiver profile</h1>
                <p class="text-muted client-ct-profile-page__lead">Review experience and care details before requesting a booking.</p>
            </div>
            <div class="header-actions">
                <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Back to browse</a>
                <button type="button" class="btn secondary" onclick="window.history.back()">Previous page</button>
            </div>
        </header>

        <article class="card client-ct-profile-card">
            <div class="client-ct-profile-card__hero">
                <div class="client-ct-profile-card__avatar-wrap">
                    <img
                        src="<?= URLROOT ?>/public/uploads/<?= htmlspecialchars((string) ($ct['profile_image'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars((string) ($ct['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        class="client-ct-profile-card__avatar"
                        onerror="this.src='<?= URLROOT ?>/public/uploads/default.jpg';">
                </div>
                <div class="client-ct-profile-card__intro">
                    <h2 class="client-ct-profile-card__name"><?= htmlspecialchars((string) ($ct['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="client-ct-profile-card__service"><?= htmlspecialchars((string) ($ct['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?> specialist</p>
                    <div class="client-ct-profile-card__meta">
                        <span><i class="bx bx-map" aria-hidden="true"></i> <?= htmlspecialchars((string) ($ct['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                        <span><i class="bx bx-time-five" aria-hidden="true"></i> <?= htmlspecialchars((string) ($ct['experience'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                        <span><i class="bx bx-check-shield" aria-hidden="true"></i> <?= htmlspecialchars((string) ($ct['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="client-ct-profile-card__rating" data-has-rating="<?= $ratingNum !== null ? '1' : '0' ?>">
                        <i class="bx bxs-star" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($ratingLabel, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
            </div>

            <div class="card-body client-ct-profile-card__body">
                <section class="client-ct-profile-section" aria-labelledby="ct-about-heading">
                    <h3 id="ct-about-heading" class="client-ct-profile-section__title">About &amp; care details</h3>
                    <?php if ($skillParts !== []): ?>
                        <ul class="client-ct-profile-skills">
                            <?php foreach ($skillParts as $skill): ?>
                                <li><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="client-ct-profile-prose text-muted">
                            <?= nl2br(htmlspecialchars((string) ($ct['qualifications'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="client-ct-profile-section" aria-labelledby="ct-exp-heading">
                    <h3 id="ct-exp-heading" class="client-ct-profile-section__title">Experience summary</h3>
                    <p class="client-ct-profile-prose"><?= htmlspecialchars((string) ($ct['experience'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </section>
            </div>

            <div class="card-footer client-ct-profile-card__footer">
                <a class="btn" href="<?= htmlspecialchars($bookUrl, ENT_QUOTES, 'UTF-8') ?>">Request booking</a>
            </div>
        </article>
    </div>
</main>

<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
