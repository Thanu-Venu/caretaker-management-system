<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SmartCare Login</title>
    <link rel="stylesheet" href="/CMA/public/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>SmartCare</h1>
            <h2>Welcome Back!</h2>
            <p class="subtitle">Sign in to your account</p>

            <form action="#" method="POST">
                <div class="input-box">
                    <label>Email Address</label>
                    <input type="text" name="email" required>
                </div>
                <div class="input-box">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="options">
                    <label class="remember">
                        <input type="checkbox"> Remember me?
                    </label>
                    <a href="#" class="forgot">Forgot Password?</a>
                </div>

                <button type="submit" class="btn">Sign in</button>
            </form>

            <p class="signup">Don't have an account? <a href="register.php">Sign up here</a></p>
            <a href="/CMA/public/" class="back-home">← Back to Home</a>
        </div>
    </div>
</body>
</html>
