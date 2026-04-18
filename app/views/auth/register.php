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
  <?php
  require_once APPROOT . '/core/PasswordPolicy.php';
  $passwordRequirementTitle = PasswordPolicy::formRequirementTitle();
  ?>
  <div class="signup-container">
    <?php if (!empty($error)): ?>
      <div class="alert-danger"
        style="padding:10px; background:#f8d7da; color:#721c24; border-radius:5px; margin-bottom:10px; text-align:center;">
        <?= $error; ?>
      </div>
    <?php endif; ?>

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
            pattern="^[A-Za-z0-9._%+-]+@gmail\.com$"
            title="Please enter a valid Gmail address (example@gmail.com)."
            value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
          <small id="emailError" style="display:none; color:#d32f2f; margin-top:6px;">
            Please enter a valid Gmail address (example@gmail.com).
          </small>
        </div>

        <div class="form-group">
          <label for="phone">Phone number</label>
          <input type="tel" id="phone" name="phone" required
            pattern="^\+?[0-9]{10,15}$"
            title="Please enter a valid phone number with 10 to 15 digits."
            value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>">
          <small id="phoneError" style="display:none; color:#d32f2f; margin-top:6px;">
            Please enter a valid phone number with 10 to 15 digits.
          </small>
        </div>

        <div class="form-group">
          <div class="input-box">
            <label>Password</label>
            <div class="password-wrapper">
              <input
                type="password"
                id="password"
                name="password"
                required
                minlength="8"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                title="<?= htmlspecialchars($passwordRequirementTitle, ENT_QUOTES, 'UTF-8') ?>"
              >
              <i id="togglePassword" class='bx bx-hide' style="cursor:pointer;"></i>
            </div>
            <small id="passwordError" style="display:none; color:#d32f2f; margin-top:6px;">
              <?= htmlspecialchars($passwordRequirementTitle, ENT_QUOTES, 'UTF-8') ?>
            </small>
          </div>
        </div>

        <div class="form-group">
          <div class="input-box">
            <label>Confirm Password</label>
            <div class="password-wrapper">
              <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                required
                minlength="8"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                title="<?= htmlspecialchars($passwordRequirementTitle, ENT_QUOTES, 'UTF-8') ?>"
              >
              <i id="toggleConfirmPassword" class='bx bx-hide' style="cursor:pointer;"></i>
            </div>
            <small id="confirmPasswordError" style="display:none; color:#d32f2f; margin-top:6px;">
              Passwords do not match.
            </small>
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
const signupForm = document.getElementById('signupForm');
const email = document.getElementById('email');
const emailError = document.getElementById('emailError');
const phone = document.getElementById('phone');
const phoneError = document.getElementById('phoneError');
const password = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const passwordError = document.getElementById('passwordError');

togglePassword.addEventListener('click', function() {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});

const confirmPassword = document.getElementById('confirmPassword');
const toggleConfirm = document.getElementById('toggleConfirmPassword');
const confirmPasswordError = document.getElementById('confirmPasswordError');
const gmailRegex = /^[A-Za-z0-9._%+-]+@gmail\.com$/i;
const phoneRegex = /^\+?[0-9]{10,15}$/;
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
const passwordRequirementMsg = <?= json_encode($passwordRequirementTitle, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

function validateGmail() {
    const value = email.value.trim();
    const isValid = gmailRegex.test(value);

    if (!isValid) {
        email.setCustomValidity('Please enter a valid Gmail address (example@gmail.com).');
        emailError.style.display = 'block';
    } else {
        email.setCustomValidity('');
        emailError.style.display = 'none';
    }

    return isValid;
}

function validatePhone() {
    const value = phone.value.trim();
    const isValid = phoneRegex.test(value);

    if (!isValid) {
        phone.setCustomValidity('Please enter a valid phone number with 10 to 15 digits.');
        phoneError.style.display = 'block';
    } else {
        phone.setCustomValidity('');
        phoneError.style.display = 'none';
    }

    return isValid;
}

function validatePasswordRule() {
    const value = password.value.trim();
    const isValid = passwordRegex.test(value);

    if (!isValid) {
        password.setCustomValidity(passwordRequirementMsg);
        passwordError.style.display = 'block';
    } else {
        password.setCustomValidity('');
        passwordError.style.display = 'none';
    }

    return isValid;
}

function validatePasswordMatch() {
    const isMatch = password.value === confirmPassword.value;
    const hasConfirmValue = confirmPassword.value.length > 0;

    if (!isMatch && hasConfirmValue) {
        confirmPassword.setCustomValidity('Passwords do not match.');
        confirmPasswordError.style.display = 'block';
    } else {
        confirmPassword.setCustomValidity('');
        confirmPasswordError.style.display = 'none';
    }

    return isMatch;
}

toggleConfirm.addEventListener('click', function() {
    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPassword.setAttribute('type', type);
    this.classList.toggle('bx-show');
    this.classList.toggle('bx-hide');
});

email.addEventListener('input', validateGmail);
phone.addEventListener('input', validatePhone);
password.addEventListener('input', function() {
    validatePasswordRule();
    validatePasswordMatch();
});

confirmPassword.addEventListener('input', validatePasswordMatch);

signupForm.addEventListener('submit', function(event) {
    const isEmailValid = validateGmail();
    const isPhoneValid = validatePhone();
    const isPasswordValid = validatePasswordRule();
    const isConfirmValid = validatePasswordMatch();

    if (!isEmailValid || !isPhoneValid || !isPasswordValid || !isConfirmValid) {
        event.preventDefault();
        if (!isEmailValid) {
            email.reportValidity();
        } else if (!isPhoneValid) {
            phone.reportValidity();
        } else if (!isPasswordValid) {
            password.reportValidity();
        } else {
            confirmPassword.reportValidity();
        }
    }
});
</script>
</body>

</html>