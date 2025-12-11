<?php
/**
 * UserManager Class
 * Handles user authentication, registration, and data management
 * Eliminates duplicate functions across login.php, register.php, admin-login.php
 */
class UserManager {
    private $users_file;

    public function __construct($users_file = 'backend/data/users.json') {
        $this->users_file = $users_file;
    }

    /**
     * Load all users from JSON file
     */
    public function loadUsers() {
        if (!file_exists($this->users_file)) {
            return [];
        }
        return json_decode(file_get_contents($this->users_file), true) ?: [];
    }

    /**
     * Save users to JSON file
     */
    private function saveUsers($users) {
        return file_put_contents(
            $this->users_file,
            json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ) !== false;
    }

    /**
     * Authenticate user by username and password
     */
    public function authenticate($username, $password) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Check if username already exists
     */
    public function usernameExists($username) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return true;
            }
        }

        return false;
    }

    /**
     * Register a new user
     */
    public function registerUser($username, $email, $password) {
        $users = $this->loadUsers();

        // Get next ID
        $next_id = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;

        // Create new user
        $new_user = [
            'id' => $next_id,
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'customer',
            'created_at' => date('Y-m-d')
        ];

        $users[] = $new_user;

        // Save to JSON
        return $this->saveUsers($users);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Validate user registration data
     */
    public function validateRegistration($username, $email, $password, $password_confirm) {
        if (empty($username) || empty($email) || empty($password)) {
            return 'All fields are required.';
        }

        if (strlen($username) < 3) {
            return 'Username must be at least 3 characters.';
        }

        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters.';
        }

        if ($password !== $password_confirm) {
            return 'Passwords do not match.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email address.';
        }

        if ($this->usernameExists($username)) {
            return 'Username already exists.';
        }

        return null; // No errors
    }

    /**
     * Validate login data
     */
    public function validateLogin($username, $password) {
        if (empty($username) || empty($password)) {
            return 'Please enter both username and password.';
        }

        return null; // No errors
    }
}
?>
