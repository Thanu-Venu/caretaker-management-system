<?php
$searched = isset($_POST['service_type']);
?>


<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/client/c_sidebar.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Finder</title>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/client/c_find.css">
</head>

<body>
  <main class="content">

    <h1>Find the Perfect Caretaker</h1>
    <p>Browse our qualified professionals and book the care you need</p>

    <!-- Search Filters -->
    <section class="filters">
      <div class="filter-box">
        <div class="filter-row">

          <div class="filter-group">
            <label for="serviceFilter">Service Type</label>
<select id="serviceFilter">
  <option value="">All Services</option>
  <option value="Elder Care">Elderly Care</option>   <!-- matches data-service -->
  <option value="Babysitter">Baby Sitting</option>     <!-- text = display, value = real service -->
  <option value="Maid">Maid services</option>         <!-- text = display, value = real service -->
</select>

          </div>


          <div class="filter-group">

            <label for="locationFilter">Location</label>
            <select id="locationFilter">
              <option value="">All Locations</option>
              <option value="Jaffna">Jaffna</option>
              <option value="Colombo">Colombo</option>
              <option value="Kandy">Kandy</option>
              <option value="Matara">Matara</option>
            </select>
          </div>
        </div>




        <div class="filter-group">
          <label for="ratingFilter">Minimum Rating</label>
          <select id="ratingFilter">
            <option value="0">Any Rating</option>
            <option value="3.5">3.5+</option>
            <option value="4">4+</option>
            <option value="4.5">4.5+</option>
          </select>
        </div>


        <div class="filter-group">
          <label>&nbsp;</label>
          <button onclick="clearFilters()" class="clearFilters">Clear Filters</button>
        </div>
      </div>

      </div>
    </section>

    <!-- Search Results -->
    <section>



      <section>
  <h2 class="two">Available Caretakers</h2>

  <div id="caretakersList" class="caretakers">

    <?php if (!empty($data['caretakers'])): ?>
      <?php foreach ($data['caretakers'] as $ct): ?>

        <div class="card"
             data-service="<?= htmlspecialchars($ct['service_type']) ?>"
             data-location="<?= htmlspecialchars($ct['location']) ?>"
             data-rating="<?= htmlspecialchars($ct['rating'] ?? 0) ?>">

          <h3><?= htmlspecialchars($ct['name']) ?></h3>
          <p><?= htmlspecialchars($ct['service_type']) ?> Specialist</p>

          <img 
  src="<?= URLROOT ?>/uploads/<?= htmlspecialchars($ct['profile_image']) ?>"
  alt="<?= htmlspecialchars($ct['name']) ?>"
  onerror="this.src='<?= URLROOT ?>/uploads/default.png';">


          <div class="exp-loc">
            <p>Exp: <?= htmlspecialchars($ct['experience']) ?></p>
            <p>Location: <?= htmlspecialchars($ct['location']) ?></p>
          </div>

          <p class="rating">⭐ <?= htmlspecialchars($ct['rating'] ?? 'N/A') ?></p>
          <p><?= htmlspecialchars($ct['qualifications']) ?></p>

          <div class="card-buttons">
            <a href="<?= URLROOT ?>/public/?url=client/c_ctprofileview&id=<?= $ct['id'] ?>" class="view-btn">
              View Profile
            </a>

            <a href="<?= URLROOT ?>/public/?url=client/c_book&id=<?= $ct['id'] ?>" class="book-btn">
              Book Now
            </a>
          </div>

        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <p>No caretakers found.</p>
    <?php endif; ?>

  </div>
</section>



    </section>
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_find.js"></script>
</body>

</html>