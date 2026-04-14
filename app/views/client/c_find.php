<?php
require_once APPROOT . '/views/client/partials/caretaker_skills.php';
$clientPageTitle = 'Search results — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_find.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$showResults = ($_SERVER['REQUEST_METHOD'] === 'POST');
$caretakersToShow = $showResults ? ($data['caretakers'] ?? []) : [];
$locations = $data['locations'] ?? [];
$post = $showResults ? $_POST : [];
?>

<div id="popupOverlay"></div>

<div id="searchPopup">
    <form id="popupForm" method="POST" action="<?= URLROOT ?>/client/c_find">
        <h3>Search caregivers</h3>

        <div class="field">
            <label for="popupServiceFilter">Service type</label>
            <select name="service_type" id="popupServiceFilter" required>
                <option value="">Select service</option>
                <?php foreach (['Elder Care', 'Babysitter', 'Maid'] as $svc): ?>
                    <option value="<?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?>" <?= (($post['service_type'] ?? '') === $svc) ? 'selected' : '' ?>><?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <script>
            window.serviceLocations = <?= json_encode($data['serviceLocations'] ?? []) ?>;
        </script>
        <div class="field">
            <label for="popupLocationFilter">Location</label>
            <select name="location" id="popupLocationFilter" required>
                <option value="">Select location</option>
                <?php foreach ($locations as $loc): ?>
                    <option value="<?= htmlspecialchars((string) $loc, ENT_QUOTES, 'UTF-8') ?>" <?= (($post['location'] ?? '') === $loc) ? 'selected' : '' ?>><?= htmlspecialchars((string) $loc, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="basisFilter">Duration basis</label>
            <select name="basis" id="basisFilter" required>
                <option value="">Select basis</option>
            </select>
        </div>
        <div class="field">
            <label for="durationInput">Duration</label>
            <input type="number" name="duration" id="durationInput" min="1" required value="<?= htmlspecialchars((string) ($post['duration'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
            <label for="startDate">Start date</label>
            <input type="date" name="start_date" id="startDate" required value="<?= htmlspecialchars((string) ($post['start_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
            <label id="preferredTimeLabel" for="preferredTimeSelect">Preferred time</label>
            <div id="timeContainer">
                <select name="preferred_time" id="preferredTimeSelect" required>
                    <option value="">Select time</option>
                </select>
            </div>
        </div>

        <div class="popup-actions">
            <button type="button" id="cancelPopupBtn" class="btn secondary">Cancel</button>
            <button type="submit" class="btn">Search</button>
        </div>
    </form>
</div>

<main class="main-content client-find-page client-find-page--results">
    <div class="dashboard-layout">
        <header class="page-header client-find-page__header">
            <div>
                <h1 class="page-title">Find the Perfect Caregiver</h1>
                <p class="text-muted client-find-page__lead">
                    <?php if ($showResults): ?>
                        Filtered caregivers based on your booking details. Adjust filters below or <strong>modify search</strong> to change dates and service.
                    <?php else: ?>
                        Use <strong>Modify search</strong> to choose service, location, and schedule. Results appear here after you search.
                    <?php endif; ?>
                </p>
            </div>
            <div class="header-actions">
                <button id="openPopupBtn" class="btn" type="button">Modify search</button>
                <a class="btn secondary" href="<?= URLROOT ?>/public?url=client/c_find1">Browse all caregivers</a>
            </div>
        </header>

        <div id="resultsSection" class="client-find-results">
            <?php if ($showResults): ?>
                <div class="filter-bar client-find-filters">
                    <div class="filters-inline client-find-filters__row">
                        <div class="filter-group client-find-filters__group">
                            <label for="ratingFilter">Minimum rating</label>
                            <select id="ratingFilter" class="client-find-select">
                                <option value="0">Any rating</option>
                                <option value="3.5">3.5+</option>
                                <option value="4">4+</option>
                                <option value="4.5">4.5+</option>
                            </select>
                        </div>
                        <div class="filter-group client-find-filters__group client-find-filters__group--action">
                            <label class="client-find-filters__action-label" aria-hidden="true">&#160;</label>
                            <button type="button" class="btn secondary" onclick="clearFilters()">Clear filters</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <section class="client-find-list-section" aria-label="Available caregivers">
                <h2 class="dashboard-overview-charts__heading">Available caregivers</h2>
                <?php if (!$showResults): ?>
                    <p class="text-muted client-find-list-section__hint">Run a search from the popup to see caregivers who match your dates and availability.</p>
                <?php else: ?>
                    <p class="text-muted client-find-list-section__hint">Highest-rated caregivers are listed first. Use the rating filter to narrow the list.</p>
                <?php endif; ?>

                <div id="caretakersList" class="caretakers client-find-grid">
                    <?php if ($showResults && !empty($caretakersToShow)): ?>
                        <?php
                        $searchBookingParams = [
                            'service_type' => (string) ($post['service_type'] ?? ''),
                            'basis' => (string) ($post['basis'] ?? ''),
                            'duration' => (string) ($post['duration'] ?? ''),
                            'date' => (string) ($post['start_date'] ?? ''),
                            'time' => (string) ($post['preferred_time'] ?? ''),
                        ];
                        $searchBookingParams = array_filter($searchBookingParams, static fn ($v) => $v !== '');
                        ?>
                        <?php foreach ($caretakersToShow as $ct): ?>
                            <?php
                            $ratingRaw = $ct['rating'] ?? null;
                            $ratingNum = ($ratingRaw !== null && $ratingRaw !== '') ? (float) $ratingRaw : null;
                            $ratingDataAttr = $ratingNum !== null ? (string) $ratingNum : '0';
                            $ratingLabel = $ratingNum !== null ? number_format($ratingNum, 1) . ' / 5' : 'Not yet rated';
                            $skillParts = client_caretaker_skill_parts((string) ($ct['qualifications'] ?? ''));
                            $ctId = (int) ($ct['id'] ?? 0);
                            $svc = (string) ($ct['service_type'] ?? '');
                            $bookQuery = array_merge(
                                ['url' => 'client/c_book', 'id' => (string) $ctId, 'service_type' => $svc],
                                $searchBookingParams
                            );
                            $bookUrl = URLROOT . '/public?' . http_build_query($bookQuery);
                            $profileQuery = array_merge(
                                ['url' => 'client/c_ctprofileview', 'id' => (string) $ctId],
                                $searchBookingParams
                            );
                            $profileUrl = URLROOT . '/public?' . http_build_query($profileQuery);
                            ?>
                            <article class="card card-hover caretaker-card"
                                data-service="<?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?>"
                                data-location="<?= htmlspecialchars((string) ($ct['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-rating="<?= htmlspecialchars($ratingDataAttr, ENT_QUOTES, 'UTF-8') ?>">
                                <div class="card-body caretaker-card__body">
                                    <div class="caretaker-card__media">
                                        <img src="<?= URLROOT ?>/public/uploads/<?= htmlspecialchars(trim((string) ($ct['profile_image'] ?? '')), ENT_QUOTES, 'UTF-8') ?>"
                                            alt=""
                                            loading="lazy"
                                            onerror="this.src='<?= URLROOT ?>/public/uploads/default.jpg';">
                                    </div>
                                    <div class="caretaker-card__content">
                                        <h3 class="caretaker-card__name"><?= htmlspecialchars((string) ($ct['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p class="caretaker-card__service"><?= htmlspecialchars($svc, ENT_QUOTES, 'UTF-8') ?> specialist</p>
                                        <div class="caretaker-card__meta">
                                            <span><i class="bx bx-time-five" aria-hidden="true"></i> <?= htmlspecialchars((string) ($ct['experience'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span><i class="bx bx-map" aria-hidden="true"></i> <?= htmlspecialchars((string) ($ct['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="caretaker-card__rating" data-has-rating="<?= $ratingNum !== null ? '1' : '0' ?>">
                                            <i class="bx bxs-star" aria-hidden="true"></i>
                                            <span class="caretaker-card__rating-label"><?= htmlspecialchars($ratingLabel, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="caretaker-card__details">
                                            <span class="caretaker-card__details-title">Care details</span>
                                            <?php if ($skillParts !== []): ?>
                                                <ul class="caretaker-card__skills">
                                                    <?php foreach ($skillParts as $skill): ?>
                                                        <li><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="caretaker-card__details-empty text-muted">No care details listed yet.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="caretaker-card__actions">
                                            <a class="btn secondary" href="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>">View profile</a>
                                            <a class="btn" href="<?= htmlspecialchars($bookUrl, ENT_QUOTES, 'UTF-8') ?>">Book now</a>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php elseif ($showResults): ?>
                        <p class="empty client-find-empty">No caregivers matched your search. Try different dates or location.</p>
                    <?php else: ?>
                        <p class="empty client-find-empty">No search yet. Open <strong>Modify search</strong> to get started.</p>
                    <?php endif; ?>
                </div>

                <?php if ($showResults): ?>
                    <div id="noCaretakerMessage" class="no-results-message hidden">No caregivers match the selected filters.</div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</main>

<script src="<?= URLROOT ?>/public/js/client/c_find.js"></script>
<?php if ($showResults): ?>
<script>
(function () {
  var svc = <?= json_encode((string) ($post['service_type'] ?? '')) ?>;
  var basis = <?= json_encode((string) ($post['basis'] ?? '')) ?>;
  var pref = <?= json_encode((string) ($post['preferred_time'] ?? '')) ?>;
  function restorePopupSelections() {
    var ps = document.getElementById('popupServiceFilter');
    if (ps && svc) { ps.value = svc; }
    if (typeof updatePopupOptions === 'function' && ps) {
      updatePopupOptions(ps.value || svc);
    }
    var bs = document.getElementById('basisFilter');
    if (bs && basis) {
      bs.value = basis;
      if (typeof updateDurationLimits === 'function') { updateDurationLimits(basis); }
    }
    var ts = document.getElementById('preferredTimeSelect');
    if (ts && pref) {
      if (ts.tagName === 'SELECT') {
        var o = Array.prototype.slice.call(ts.options).some(function (opt) { return opt.value === pref; });
        if (o) { ts.value = pref; }
      } else {
        ts.value = pref;
      }
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restorePopupSelections);
  } else {
    restorePopupSelections();
  }
})();
</script>
<?php endif; ?>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
