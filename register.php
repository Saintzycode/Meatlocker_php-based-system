<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MeatLocker</title>
    <link rel="stylesheet" href="frontend/css/common.css">
    <link rel="stylesheet" href="frontend/css/login.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <a href="index.php" class="back-button-icon" title="Back to Home">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="login-title">MeatLocker</h1>
            <p class="login-subtitle">Create Your Account</p>

            <?php
            require_once 'backend/handlers/AuthHandler.php';
            
            $auth = new AuthHandler();
            $auth->checkAlreadyLoggedIn('dashboard.php');
            $auth->handleRegistration('login.php');
            $error = $auth->getError();
            $success = $auth->getSuccess();
            ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <span class="success-icon">✓</span>
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <?php if ($error): ?>
                    <div class="error-message">
                        <span class="error-icon">✕</span>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Choose a username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter a password (min 6 characters)"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Confirm your password"
                        required
                    >
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" name="agree_terms" required>
                        I agree to the <a href="about.php" target="_blank">Terms of Service</a> and <a href="about.php" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="login-button">Create Account</button>
            </form>

            <div class="login-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
