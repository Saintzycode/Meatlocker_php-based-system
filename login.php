<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - MeatLocker</title>
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
            <p class="login-subtitle">Customer Portal</p>

            <?php
            require_once 'backend/handlers/AuthHandler.php';
            
            $auth = new AuthHandler();
            $auth->checkAlreadyLoggedIn('index.php');
            $auth->handleLogin('customer', 'index.php');
            $error = $auth->getError();
            ?>

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
                        placeholder="Enter your username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">Sign In</button>
            </form>

            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Create one</a></p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: #999;">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>customer</code><br>
                    Password: <code>password</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
