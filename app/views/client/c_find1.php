<?php
require_once APPROOT . '/views/client/partials/caretaker_skills.php';
$clientPageTitle = 'Browse caregivers — SmartCare';
$clientExtraCss  = ['admin/ad_dashboard.css', 'client/c_find.css'];
require_once APPROOT . '/views/templates/client/client_layout_head.php';
require_once APPROOT . '/views/templates/client/c_header.php';
require_once APPROOT . '/views/templates/client/c_sidebar.php';

$caretakersToShow = $data['allCaretakers'] ?? [];
?>

<div id="popupOverlay"></div>

<div id="searchPopup">
    <form id="popupForm" method="POST" action="<?= URLROOT ?>/client/c_find">
        <h3>Search caregivers</h3>

        <div class="field">
            <label for="popupServiceFilter">Service type</label>
            <select name="service_type" id="popupServiceFilter" required>
               <option value="">Select service</option>
                <option value="Elder Care">Elder Care</option>
                <option value="Babysitter">Babysitter</option>
                <option value="Maid">Maid</option>
            </select>
        </div>
        <script>
            window.serviceLocations = <?= json_encode($data['serviceLocations'] ?? []) ?>;
        </script>
        <div class="field">
            <label for="popupLocationFilter">Location</label>
            <select name="location" id="popupLocationFilter" required>
                <option value="">Select location</option>
                <option value="Colombo">Colombo</option>
                <option value="Kandy">Kandy</option>
                <option value="Matara">Matara</option>
                <option value="Vavuniya">Vavuniya</option>
                <option value="Jaffna">Jaffna</option>
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
            <input type="number" name="duration" id="durationInput" min="1" required>
        </div>
        <div class="field">
            <label for="startDate">Start date</label>
            <input type="date" name="start_date" id="startDate" required>
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
            <button type="submit" class="btn">Search</button>
            <button type="button" id="cancelPopupBtn" class="btn secondary">Cancel</button>
            
        </div>
    </form>
</div>

<main class="main-content client-find-page">
    <div class="dashboard-layout">
        <header class="page-header client-find-page__header">
            <div>
                <h1 class="page-title">Browse caregivers</h1>
                <p class="text-muted client-find-page__lead">Filter by service, location, and rating. Use <strong>Book a service</strong> to check availability for a specific date.</p>
            </div>
            <div class="header-actions">
                <button id="openPopupBtn" class="btn" type="button">Book a service</button>
            </div>
        </header>

        <div id="resultsSection" class="client-find-results">
         <div class="filter-bar client-find-filters">
            <div class="filters-inline client-find-filters__row">
                <div class="filter-group client-find-filters__group">
                <label for="serviceFilter">Service type</label>
                 <select id="serviceFilter" class="client-find-select">
                  <option value="">All services</option>
                <option value="Elder Care">Elder Care</option>
                  <option value="Babysitter">Babysitter</option>
                <option value="Maid">Maid</option>
              </select>
                </div>
                    <div class="filter-group client-find-filters__group">
                        <label for="locationFilter">Location</label>
                        <select id="locationFilter" class="client-find-select">
                            <option value="">All locations</option>
                            <option value="Colombo">Colombo</option>
                            <option value="Kandy">Kandy</option>
                            <option value="Vavuniya">Vavuniya</option>
                            <option value="Jaffna">Jaffna</option>
                            <option value="Matara">Matara</option>
                        </select>
                    </div>


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

            <section class="client-find-list-section" aria-label="Caregiver list">
                <h2 class="dashboard-overview-charts__heading">All caregivers</h2>

                <div id="caretakersList" class="caretakers client-find-grid">
                    <?php if (!empty($caretakersToShow)): ?>
                        <?php foreach ($caretakersToShow as $ct): ?>
                            <?php
                            $ratingRaw = $ct['rating'] ?? null;
                            $ratingNum = ($ratingRaw !== null && $ratingRaw !== '') ? (float) $ratingRaw : null;
                            $ratingDataAttr = $ratingNum !== null ? (string) $ratingNum : '0';
                            $ratingLabel = $ratingNum !== null ? number_format($ratingNum, 1) . ' / 5' : 'Not yet rated';
                            $skillParts = client_caretaker_skill_parts((string) ($ct['qualifications'] ?? ''));
                            ?>
                            <article class="card card-hover caretaker-card"
                                data-service="<?= htmlspecialchars((string) ($ct['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-location="<?= htmlspecialchars((string) ($ct['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                data-experience="<?= (int) ($ct['experience'] ?? 0) ?>"
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
                                        <p class="caretaker-card__service"><?= htmlspecialchars((string) ($ct['service_type'] ?? ''), ENT_QUOTES, 'UTF-8') ?> specialist</p>
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
                          </div>
                         </div>
                     </article>
                <?php endforeach; ?>
                 <?php else: ?>
                  <p class="empty">No caregivers found.</p>
                <?php endif; ?>
             </div>

            <div id="noCaretakerMessage" class="no-results-message hidden">
                  No caregivers match the selected filters.
            </div>

            <div id="caretakerPagination" class="caretaker-pagination hidden" aria-label="Caretaker pagination">
                <button type="button" id="caretakerPrevBtn" class="btn secondary">Previous</button>
                <span id="caretakerPageInfo" class="caretaker-pagination__info">Page 1 of 1</span>
                <button type="button" id="caretakerNextBtn" class="btn secondary">Next</button>
            </div>
        </section>
    </div>
 </div>
</main>

<script src="<?= URLROOT ?>/public/js/client/c_find.js"></script>
<?php require_once APPROOT . '/views/templates/client/client_layout_close.php'; ?>
