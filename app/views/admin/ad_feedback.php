<?php include_once APPROOT . "/views/templates/client/c_header.php"; ?>
<?php include_once APPROOT . "/views/templates/admin/ad_sidebar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin dashboard</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/admin/ad_feedback.css">
</head>
<body>
   <div class="content">
  <div class="card">
    <h2>Feedback & Complaints</h2>

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-btn active" data-tab="feedback">Client Feedback</button>
      <button class="tab-btn" data-tab="complaints">Complaints</button>
    </div>

    <!-- Client Feedback Section -->
    <div id="feedbackSection" class="tab-section">
      <!-- Filters -->
      <div class="filters">
        <label for="ratingFilter">Rating:</label>
        <select id="ratingFilter">
          <option value="">All</option>
          <option value="5">★★★★★</option>
          <option value="4">★★★★☆</option>
          <option value="3">★★★☆☆</option>
          <option value="2">★★☆☆☆</option>
          <option value="1">★☆☆☆☆</option>
        </select>

        <label for="dateFilter">Date Received:</label>
        <input type="date" id="dateFilter">
      </div>

      <div class="table-container">
        <table id="feedbackTable">
          <thead>
            <tr>
              <th>Client</th>
              <th>Caregiver</th>
              <th>Rating</th>
              <th>Comments</th>
              <th>Date Received</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Sophia Bennett</td>
              <td>Ethan Carter</td>
              <td data-rating="5" class="stars">★★★★★</td>
              <td>Excellent service, very attentive.</td>
              <td data-date="2025-07-20">2025-07-20</td>
            </tr>
            <tr>
              <td>Liam Harper</td>
              <td>Olivia Davis</td>
              <td data-rating="4" class="stars">★★★★☆</td>
              <td>Good, but could improve on punctuality.</td>
              <td data-date="2025-07-18">2025-07-18</td>
            </tr>
            <tr>
              <td>Ava Morgan</td>
              <td>Noah Wilson</td>
              <td data-rating="3" class="stars">★★★☆☆</td>
              <td>Highly recommend, very professional.</td>
              <td data-date="2025-07-15">2025-07-15</td>
            </tr>
            <tr>
              <td>Jackson Reed</td>
              <td>Isabella Clark</td>
              <td data-rating="3" class="stars">★★★☆☆</td>
              <td>Satisfactory, but communication could be better.</td>
              <td data-date="2025-07-12">2025-07-12</td>
            </tr>
            <tr>
              <td>Chloe Foster</td>
              <td>Lucas Turner</td>
              <td data-rating="5" class="stars">★★★★★</td>
              <td>Outstanding care, very compassionate.</td>
              <td data-date="2025-07-10">2025-07-10</td>
            </tr>
          </tbody>
        </table>
        <p id="noResults" style="display:none; text-align:center; margin-top:15px; font-weight:500; color:#ef4444;">
          No matching results found.
        </p>
      </div>
    </div>

    <!-- Complaints Section -->
    <div id="complaintsSection" class="tab-section" style="display:none;">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Client</th>
              <th>Caregiver</th>
              <th>Complaint</th>
              <th>Date Filed</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Michael Green</td>
              <td>Sophia Lewis</td>
              <td>Caregiver was late multiple times.</td>
              <td>2025-07-22</td>
              <td>Pending</td>
            </tr>
            <tr>
              <td>Emily White</td>
              <td>Daniel Scott</td>
              <td>Unprofessional behavior during visit.</td>
              <td>2025-07-19</td>
              <td>Resolved</td>
            </tr>
            <tr>
              <td>James Hall</td>
              <td>Emma Brown</td>
              <td>Lack of communication with family.</td>
              <td>2025-07-16</td>
              <td>In Review</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo URLROOT; ?>/public/js/admin/ad_feedback.js"></script>
</body>
</html>