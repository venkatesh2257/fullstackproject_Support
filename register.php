<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-container">
    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="logo-icon">✨</div>
            <h2>Create Account</h2>
            <p>Join us today</p>
        </div>

        <!-- Registration Form -->
        <form id="registerForm">

            <!-- Full Name -->
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" id="fullname" required>
                    <label>Full Name</label>
                    <div class="input-line"></div>
                </div>
            </div>

            <!-- Email -->
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" id="email" required>
                    <label>Email Address</label>
                    <div class="input-line"></div>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group password-wrapper">
                <div class="input-wrapper">
                    <input type="password" id="password" required>
                    <label>Password</label>
                    <div class="input-line"></div>

                    <button type="button" class="password-toggle" id="togglePass">
                        <span class="toggle-icon"></span>
                    </button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group password-wrapper">
                <div class="input-wrapper">
                    <input type="password" id="confirmPassword" required>
                    <label>Confirm Password</label>
                    <div class="input-line"></div>

                    <button type="button" class="password-toggle" id="toggleConfirm">
                        <span class="toggle-icon"></span>
                    </button>
                </div>
            </div>

            <!-- Terms -->
            <div class="form-options">
                <label class="remember-wrapper">
                    <input type="checkbox" required>
                    <span class="checkbox-label">
                        <span class="custom-checkbox"></span>
                        I agree to the Terms & Conditions
                    </span>
                </label>
            </div>

            <!-- Button -->
            <button type="submit" class="login-btn">
                <span class="btn-glow"></span>
                <span class="btn-text">Create Account</span>
                <span class="btn-loader"></span>
            </button>

            <!-- Divider -->
            <div class="divider"><span>OR</span></div>

            <!-- Social -->
            <div class="social-login">
                <button class="social-btn">
                    <span class="social-icon google-icon"></span>
                    Sign up with Google
                </button>

                <button class="social-btn">
                    <span class="social-icon apple-icon"></span>
                    Sign up with Apple
                </button>
            </div>

            <!-- Login Link -->
            <div class="signup-link">
                <p>Already have an account? <a href="#">Sign In</a></p>
            </div>

        </form>

        <!-- Success -->
        <div class="success-message" id="successMsg" style="display:none;">
            <div class="success-icon">✔</div>
            <h3>Account Created</h3>
            <p>Redirecting...</p>
        </div>

    </div>
</div>

<!-- JS for password show/hide -->
<script>
document.getElementById("togglePass").onclick = function () {
    let pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
};

document.getElementById("toggleConfirm").onclick = function () {
    let pass = document.getElementById("confirmPassword");
    pass.type = pass.type === "password" ? "text" : "password";
};
</script>

</body>
</html>
