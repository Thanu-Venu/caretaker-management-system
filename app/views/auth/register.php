<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare - Signup</title>
  <link rel="stylesheet" href="/CMA/public/css/register.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


</head>

<body>
  <div class="signup-container">
    <div class="signup-box">
      <h1 class="logo">SmartCare</h1>
      <h2>Create Account</h2>
      <p class="subtitle">Join SmartCare to find trusted caretakers</p>

      <?php if (!empty($data['error'])): ?>
        <div class="error-message" style="color:red; margin-bottom:10px;">
          <?php echo htmlspecialchars($data['error']); ?>
        </div>
      <?php endif; ?>

      <form id="signupForm" action="http://localhost/CMA/public/?url=auth/register" method="POST">
        <div class="form-group-inline">
          <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" required
              value="<?php echo htmlspecialchars($data['firstName'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" required
              value="<?php echo htmlspecialchars($data['lastName'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required
            value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label for="phone">Phone number</label>
          <input type="tel" id="phone" name="phone" required
            value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <div class="input-box">
            <label>Password</label>
            <div class="password-wrapper">
              <input type="password" id="password" name="password" required>
              <i id="togglePassword" class='bx bx-hide' style="cursor:pointer;"></i>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="input-box">
            <label>Confirm Password</label>
            <div class="password-wrapper">
              <input type="password" id="confirmPassword" name="confirmPassword" required>
              <i id="toggleConfirmPassword" class='bx bx-hide' style="cursor:pointer;"></i>
            </div>
          </div>
        </div>

        <div class="form-group checkbox">
          <input type="checkbox" id="terms" required>
          <label for="terms">I agree to the <a href="#">Terms of service</a> and <a href="#">Privacy Policy</a></label>
        </div>

        <button type="submit" class="btn">Create Account</button>

        <p class="footer-text">Already have an account? <a href="http://localhost/CMA/public/?url=auth/login">Sign In
            here</a></p>
        <p class="back-link"><a href="/CMA/public">← Back to Home</a></p>
      </form>
    </div>
  </div>

<script>
const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');

togglePassword.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});

const confirmPassword = document.getElementById('confirmPassword');
const toggleConfirm = document.getElementById('toggleConfirmPassword');

toggleConfirm.addEventListener('click', function() {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});
</script>
</body>

</html>