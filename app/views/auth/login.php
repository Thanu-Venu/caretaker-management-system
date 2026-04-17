<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SmartCare Login</title>
    <link rel="stylesheet" href="/CMA/public/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


</head>

<body>
    <div class="login-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"
                style="padding:10px; background:#1e88e5; color:white; border-radius:5px; margin-bottom:10px;">
                <?= $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <div class="login-box">
            <h1>SmartCare</h1>
            <h2>Welcome Back!</h2>
            <p class="subtitle">Sign in to your account</p>
            <?php if (!empty($data['error'])): ?>
                <div class="error-message" style="color:red; text-align:center; margin-bottom:10px;">
                    <?php echo htmlspecialchars($data['error']); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo URLROOT; ?>/public/?url=auth/login" method="POST">
                <div class="input-box">
                    <label>Email Address</label>
                    <input type="text" name="email" required>
                </div>
                <div class="input-box">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="loginPassword" name="password" required>
                        <i id="toggleLoginPassword" class='bx bx-hide' style="cursor:pointer;"></i>
                    </div>
                </div>

                <div class="options">
                    <label class="remember">
                        <input type="checkbox"> Remember me?
                    </label>
                    <a href="#" class="forgot">Forgot Password?</a>
                </div>

                <button type="submit" class="btn">Sign in</button>
            </form>

            <p class="signup">Don't have an account? <a href="<?php echo URLROOT; ?>/public/?url=auth/register">Sign up
                    here</a></p>
            <a href="<?php echo URLROOT; ?>/public/" class="back-home">← Back to Home</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginPassword = document.getElementById('loginPassword');
            const toggleLogin = document.getElementById('toggleLoginPassword');

            toggleLogin.addEventListener('click', function () {
                // Toggle input type
                const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                loginPassword.setAttribute('type', type);

                // Toggle icon
                if (this.classList.contains('bx-hide')) {
                    this.classList.remove('bx-hide');
                    this.classList.add('bx-show'); // eye open
                } else {
                    this.classList.remove('bx-show');
                    this.classList.add('bx-hide'); // eye closed
                }
            });
        });


    </script>
</body>

</html>
