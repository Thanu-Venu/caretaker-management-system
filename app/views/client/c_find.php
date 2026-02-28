<?php
include_once APPROOT . "/views/templates/client/c_header.php";
include_once APPROOT . "/views/templates/client/c_sidebar.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Finder</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_find.css">
</head>

<body>

  <!-- ================= POPUP OVERLAY ================= -->
  <div id="popupOverlay" class="overlay"></div>

  <!-- ================= POPUP FORM ================= -->
  <div id="searchPopup" class="popup">
    <form id="popupForm" method="POST" action="<?= URLROOT ?>/client/c_find">

      <h3>Search Caretakers</h3>

      <label>Service Type</label>
      <select name="service_type" id="popupServiceFilter" required>
        <option value="">Select Service</option>
        <option value="Elder Care">Elder Care</option>
        <option value="Babysitter">Babysitter</option>
        <option value="Maid">Maid</option>
        <option value="Disability Support">Disability Support</option>
      </select>
      <script>
        window.serviceLocations = <?= json_encode($data['serviceLocations'] ?? []) ?>;
      </script>

      <label>Location</label>
      <select name="location" id="popupLocationFilter" required>
        <option value="">Select Location</option>
        <?php
        $locations = $data['locations'] ?? [];
        foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>" <?= ($_POST['location'] ?? '') === $loc ? 'selected' : '' ?>><?= htmlspecialchars($loc) ?></option>
        <?php endforeach; ?>
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
  $showResults = ($_SERVER['REQUEST_METHOD'] === 'POST');
  $caretakersToShow = $showResults ? ($data['caretakers'] ?? []) : [];
  $selectedService = $showResults ? ($_POST['service_type'] ?? '') : '';
  ?>

  <main class="content">

    <div class="page-header">
      <div>
        <h1>Find the Perfect Caregiver</h1>
        <?php if ($showResults): ?>
          <p>Filtered caregivers based on your booking details</p>
        <?php else: ?>
          <p>Search for available caregivers based on your booking details</p>
        <?php endif; ?>
      </div>
    </div>

    <div id="resultsSection">

      <section class="filters">
        <div class="filter-box">

          <div class="filter-row">

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
        <h2 class="two">Available Caregivers</h2>

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

                <div class="card-buttons">
                  <a href="<?= URLROOT ?>/public/?url=client/c_ctprofileview&id=<?= $ct['id'] ?>" class="view-btn">
                    View Profile
                  </a>
                  <a href="<?= URLROOT ?>/public/?url=client/c_book
&id=<?= $ct['id'] ?>
&service_type=<?= urlencode($ct['service_type']) ?>
&basis=<?= urlencode($_POST['basis'] ?? '') ?>
&duration=<?= urlencode($_POST['duration'] ?? '') ?>
&date=<?= urlencode($_POST['start_date'] ?? '') ?>
&time=<?= urlencode($_POST['preferred_time'] ?? '') ?>"
class="book-btn">Book Now</a>
                </div>

              </div>

            <?php endforeach; ?>
          <?php else: ?>
            <p>Use the Book Caregiver button to search for availability.</p>
          <?php endif; ?>

        </div>
      </section>

    </div>
  </main>

  <!-- ================= JS ================= -->
  <script src="<?php echo URLROOT; ?>/public/js/client/c_find.js"></script>

</body>

</html>