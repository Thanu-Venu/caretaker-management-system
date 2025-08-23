<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare - Signup</title>
  <link rel="stylesheet" href="/CMA/public/css/register.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body>
  <div class="signup-container">
    <div class="signup-box">
      <h1 class="logo">SmartCare</h1>
      <h2>Create Account</h2>
      <p class="subtitle">Join SmartCare to find trusted caretakers</p>

      <form id="signupForm">
        <div class="form-group-inline">
          <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" required>
          </div>
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" required>
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone number</label>
          <input type="tel" id="phone" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" required>
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" required>
        </div>

        <div class="form-group checkbox">
          <input type="checkbox" id="terms" required>
          <label for="terms">I agree to the <a href="#">Terms of service</a> and <a href="#">Privacy Policy</a></label>
        </div>

        <button type="submit" class="btn">Create Account</button>

        <p class="footer-text">Already have an account? <a href="login.php">Sign In here</a></p>
        <p class="back-link"><a href="/CMA/public">← Back to Home</a></p>
      </form>
    </div>
  </div>

  <script src="signup.js"></script>
</body>
</html>
