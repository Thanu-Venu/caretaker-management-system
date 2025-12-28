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
              <option value="Elderly Care">Elderly Care</option>
              <option value="Child Care">Baby Sitting</option>
              <option value="Maid">Maid services</option>
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

      <h2 class="two">Available Caretakers</h2>
      <div id="caretakersList" class="caretakers"></div>
    </section>
  </main>

  <script src="<?php echo URLROOT; ?>/public/js/client/c_find.js"></script>
</body>

</html>