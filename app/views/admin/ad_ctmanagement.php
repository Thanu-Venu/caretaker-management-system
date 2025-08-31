<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caretaker Management</title>
  <link rel="stylesheet" href="style1.css">
  <link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Navbar -->
  <header>
    <nav class="navbar">
      <div class="logo">SmartCare</div>
      <ul class="nav-links">
        <li>
          <div class="profile">
            <i class="fa-solid fa-bell"></i>
            <img src="1.jpeg" alt="User">
          </div>
        </li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <ul class="menu">
        <li><i class="fa-solid fa-house"></i> Dashboard</li>
        <li class="active"><i class="fa-solid fa-user"></i> Caretakers</li>
        <li><i class="fa-solid fa-users"></i> Clients</li>
        <li><i class="fa-solid fa-calendar"></i> Bookings</li>
        <li><i class="fa-solid fa-plane"></i> Leave</li>
        <li><i class="fa-solid fa-dollar-sign"></i> Payments</li>
        <li><i class="fa-solid fa-comment"></i> Feedback</li>
        <li><i class="fa-solid fa-user-gear"></i> Users</li>
        <li><i class="fa-solid fa-bullhorn"></i> Announcements</li>
        <li><i class="fa-solid fa-clock-rotate-left"></i> History</li>
        <li><i class="fa-solid fa-chart-bar"></i> Reports</li>
        <li><i class="fa-solid fa-gear"></i> Settings</li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <section class="caretaker-header">
        <h1>Caretaker Management</h1>
        <button class="add-btn">Add Caretaker</button>
      </section>

      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search caretakers">
      </div>

      <section>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Ethan Bennett</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa-solid fa-eye"></i>
                <i class="fa-solid fa-pen"></i>
                <i class="fa-solid fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="fa-solid fa-eye"></i>
                <i class="fa-solid fa-pen"></i>
                <i class="fa-solid fa-trash"></i>
              </td>
            </tr>
              <tr>
              <td>Liam Foster</td>
              <td>Elder Care</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Ava Morgan</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Noah Parker</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Isabella Reed</td>
              <td>Elder Care</td>
              <td><span class="status inactive">Inactive</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Jackson Cole</td>
              <td>Maid</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
            <tr>
              <td>Mia Fisher</td>
              <td>Babysitter</td>
              <td><span class="status active">Active</span></td>
              <td class="actions">
                <i class="fa fa-eye"></i>
                <i class="fa fa-pen"></i>
                <i class="fa fa-trash"></i>
              </td>
            </tr>
         
            <!-- Add other caretaker rows here -->
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
