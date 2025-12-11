<?php
/**
 * Consolidated Authentication Handler
 * Used by both login.php and admin-login.php to reduce code duplication
 */

session_start();
require_once __DIR__ . '/../functions/UserManager.php';

class AuthHandler {
    private $userManager;
    private $error = '';
    private $success = '';
    private $isProcessing = false;

    public function __construct() {
        $this->userManager = new UserManager();
    }

    /**
     * Redirect if already logged in
     */
    public function checkAlreadyLoggedIn($redirect = 'index.php') {
        if (isset($_SESSION['user_id'])) {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Handle login form submission
     * @param string $expectedRole - 'admin' or 'customer'
     * @param string $redirectOnSuccess - Where to redirect on successful login
     * @return bool - True if login was successful
     */
    public function handleLogin($expectedRole = 'customer', $redirectOnSuccess = 'index.php') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        $this->error = $this->userManager->validateLogin($username, $password);

        if (!$this->error) {
            $user = $this->userManager->authenticate($username, $password);

            if ($user && $user['role'] === $expectedRole) {
                // Set session and mark as successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $this->isProcessing = true;
                header("Location: $redirectOnSuccess");
                exit;
            } else {
                $this->error = "Invalid credentials or {$expectedRole} account not found.";
            }
        }
        
        return false;
    }

    /**
     * Handle registration form submission
     * @param string $redirectOnSuccess - Where to redirect on successful registration
     * @return bool - True if registration was successful
     */
    public function handleRegistration($redirectOnSuccess = 'login.php') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validate input
        $this->error = $this->userManager->validateRegistration($username, $email, $password, $password_confirm);

        // Check if terms are accepted
        if (!$this->error && !isset($_POST['agree_terms'])) {
            $this->error = 'You must agree to the Terms of Service and Privacy Policy.';
        }

        if (!$this->error) {
            // Register new user
            if ($this->userManager->registerUser($username, $email, $password)) {
                $this->success = "Registration successful! <a href=\"{$redirectOnSuccess}\">Sign in now</a>";
                return true;
            } else {
                $this->error = 'Error saving user. Please try again.';
            }
        }
        
        return false;
    }

    /**
     * Get any error message
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Get any success message
     */
    public function getSuccess() {
        return $this->success;
    }

    /**
     * Check if there's an error
     */
    public function hasError() {
        return !empty($this->error);
    }

    /**
     * Check if there's a success message
     */
    public function hasSuccess() {
        return !empty($this->success);
    }
}
?>
