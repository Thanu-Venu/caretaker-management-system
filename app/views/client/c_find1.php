<?php
include_once APPROOT . "/views/templates/client/c_header.php";
include_once APPROOT . "/views/templates/client/c_sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caregiver Finder</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_find.css">
</head>

<body>

  <div id="popupOverlay" class="overlay"></div>

  <!-- popupform -->
  <div id="searchPopup" class="popup">
    <form id="popupForm" method="POST" action="<?= URLROOT ?>/client/c_find">

      <h3>Search Caregivers</h3>

      <label>Service Type</label>
      <select name="service_type" id="popupServiceFilter" required>
        <option value="">Select Service</option>
        <option value="Elder Care">Elder Care</option>
        <option value="Babysitter">Babysitter</option>
        <option value="Maid">Maid</option>
      </select>
      <script>
        window.serviceLocations = <?= json_encode($data['serviceLocations'] ?? []) ?>;
      </script>
      <label>Location</label>
      <select name="location" id="popupLocationFilter" required>
        <option value="">Select Location</option>
        <option value="Colombo">Colombo</option>
        <option value="Kandy">Kandy</option>
        <option value="Matara">Matara</option>
        <option value="Vavuniya">Vavuniya</option>
        <option value="Jaffna">Jaffna</option>
      </select>
      <label>Duration Basis</label>
      <select name="basis" id="basisFilter" required>
        <option value="">Select Basis</option>
      </select>

      <label>Duration</label>
      <input type="number" name="duration" id="durationInput" min="1" required>

      <label>Start Date</label>
      <input type="date" name="start_date" id="startDate" required>

      <label id="preferredTimeLabel">Preferred Time</label>
      <div id="timeContainer">
        <select name="preferred_time" id="preferredTimeSelect" required>
          <option value="">Select Time</option>
        </select>
      </div>

      <div class="popup-actions">
      <button type="button" id="cancelPopupBtn" class="cancel-btn">Cancel</button>
      <button type="submit" class="book-btn">Search</button>
    </div>

    </form>
  </div>

  <?php
  $caretakersToShow = $data['allCaretakers'] ?? [];
  ?>

  <main class="content">

    <div class="page-header header-left-cta">
      <div>
        <h1>Find the Perfect Caregiver</h1>
        <p>Browse all active caregivers and filter by service, location, and rating</p>
      </div>
      <button id="openPopupBtn" class="book-btn" type="button">Book Caregiver</button>
    </div>

    <div id="resultsSection">

      <section class="filters">
        <div class="filter-box">

          <div class="filter-row">
            <div class="filter-group">
              <label>Service Type</label>
              <select id="serviceFilter">
                <option value="">All Services</option>
                <option value="Elder Care">Elder Care</option>
                <option value="Babysitter">Babysitter</option>
                <option value="Maid">Maid</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Location</label>
              <select id="locationFilter">
                <option value="">All Locations</option>
                <option value="Colombo">Colombo</option>
                <option value="Kandy">Kandy</option>
                <option value="Vavuniya">Vavuniya</option>
                <option value="Jaffna">Jaffna</option>
                <option value="Matara">Matara</option>
              </select>
            </div>

            <div class="filter-group">
              <label>Minimum Rating</label>
              <select id="ratingFilter">
                <option value="0">Any Rating</option>
                <option value="3.5">3.5+</option>
                <option value="4">4+</option>
                <option value="4.5">4.5+</option>
              </select>
            </div>

            <div class="filter-group">
              <label>&nbsp;</label>
              <button type="button" onclick="clearFilters()">Clear Filters</button>
            </div>

          </div>
        </div>
      </section>

      <section>
        <h2 class="two">All Caregivers</h2>

        <div id="caretakersList" class="caretakers">

          <?php if (!empty($caretakersToShow)): ?>
            <?php foreach ($caretakersToShow as $ct): ?>

              <div class="card" data-service="<?= htmlspecialchars($ct['service_type']) ?>"
                data-location="<?= htmlspecialchars($ct['location']) ?>"
                data-rating="<?= htmlspecialchars($ct['rating'] ?? 0) ?>">

                <h3><?= htmlspecialchars($ct['name']) ?></h3>
                <p><?= htmlspecialchars($ct['service_type']) ?> Specialist</p>

                <img src="<?= URLROOT ?>/uploads/<?= htmlspecialchars($ct['profile_image']) ?>"
                  onerror="this.src='<?= URLROOT ?>/uploads/default.png';">

                <div class="exp-loc">
                  <p>Exp: <?= htmlspecialchars($ct['experience']) ?></p>
                  <p><?= htmlspecialchars($ct['location']) ?></p>
                </div>

                <p class="rating">⭐ <?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></p>
                <p><?= htmlspecialchars($ct['qualifications']) ?></p>


              </div>

            <?php endforeach; ?>
          <?php else: ?>
            <p>No caregivers found.</p>
          <?php endif; ?>

        </div>

        <div id="noCaretakerMessage" class="no-results-message hidden">
          No caregivers match the selected filters.
        </div>
      </section>

    </div>
  </main>

  <!-- ================= JS ================= -->
  <script src="<?php echo URLROOT; ?>/public/js/client/c_find.js"></script>

</body>

</html>
