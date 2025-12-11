<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MeatLocker</title>
    <link rel="stylesheet" href="frontend/css/common.css">
    <link rel="stylesheet" href="frontend/css/login.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">MeatLocker</h1>
            <p class="login-subtitle">Admin Portal</p>

            <?php
            require_once 'backend/handlers/AuthHandler.php';
            
            $auth = new AuthHandler();
            $auth->checkAlreadyLoggedIn('dashboard.php');
            $auth->handleLogin('admin', 'dashboard.php');
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
                <p style="margin-top: 1rem;">
                    <a href="index.php" class="back-link">← Back to Home</a>
                </p>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: #999;">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>password</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
