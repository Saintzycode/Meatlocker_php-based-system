<?php
/**
 * CookieHandler Class
 * Handles secure cookie operations
 */
class CookieHandler {
    const COOKIE_PREFIX = 'meatlocker_';
    const COOKIE_DURATION = 2592000; // 30 days in seconds

    /**
     * Set a secure cookie
     * @param string $name - Cookie name (prefix will be added automatically)
     * @param string $value - Cookie value
     * @param int $expirationDays - Days until expiration (default: 30)
     * @param bool $httpOnly - Only accessible via HTTP (default: true for security)
     * @param bool $secure - Only sent over HTTPS (default: false, set to true in production)
     * @return bool
     */
    public static function set($name, $value, $expirationDays = 30, $httpOnly = true, $secure = false) {
        // Validate inputs
        if (empty($name) || !self::isValidCookieName($name)) {
            error_log("Invalid cookie name: $name");
            return false;
        }

        if (strlen($value) > 4096) {
            error_log("Cookie value too large for: $name");
            return false;
        }

        // Add prefix to cookie name
        $cookieName = self::COOKIE_PREFIX . $name;

        // Calculate expiration time
        $expiration = time() + ($expirationDays * 86400);

        // Set cookie with secure options
        return setcookie(
            $cookieName,
            $value,
            [
                'expires' => $expiration,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'] ?? '',
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * Get a cookie value
     * @param string $name - Cookie name (prefix will be added automatically)
     * @param string $default - Default value if cookie doesn't exist
     * @return mixed
     */
    public static function get($name, $default = null) {
        $cookieName = self::COOKIE_PREFIX . $name;
        return $_COOKIE[$cookieName] ?? $default;
    }

    /**
     * Check if cookie exists
     */
    public static function exists($name) {
        $cookieName = self::COOKIE_PREFIX . $name;
        return isset($_COOKIE[$cookieName]);
    }

    /**
     * Delete a cookie
     */
    public static function delete($name) {
        $cookieName = self::COOKIE_PREFIX . $name;
        
        return setcookie(
            $cookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'] ?? '',
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }

    /**
     * Clear all MeatLocker cookies
     */
    public static function clearAll() {
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name, self::COOKIE_PREFIX) === 0) {
                setcookie(
                    $name,
                    '',
                    [
                        'expires' => time() - 3600,
                        'path' => '/',
                        'domain' => $_SERVER['HTTP_HOST'] ?? '',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
            }
        }
    }

    /**
     * Set encrypted cookie (for sensitive data)
     */
    public static function setEncrypted($name, $value, $expirationDays = 30) {
        if (empty($GLOBALS['encryption_key'])) {
            error_log("Encryption key not set");
            return false;
        }

        // Encrypt value (simple base64 for now - in production use proper encryption)
        $encrypted = base64_encode($value);

        return self::set($name, $encrypted, $expirationDays);
    }

    /**
     * Get encrypted cookie
     */
    public static function getEncrypted($name, $default = null) {
        $encrypted = self::get($name);

        if ($encrypted === null) {
            return $default;
        }

        // Decrypt value
        $decrypted = base64_decode($encrypted, true);

        return $decrypted !== false ? $decrypted : $default;
    }

    /**
     * Validate cookie name (alphanumeric, underscore, hyphen)
     */
    private static function isValidCookieName($name) {
        return preg_match('/^[a-zA-Z0-9_\-]+$/', $name) === 1;
    }

    /**
     * Get all cookies with MeatLocker prefix
     */
    public static function getAll() {
        $cookies = [];
        
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name, self::COOKIE_PREFIX) === 0) {
                $cleanName = substr($name, strlen(self::COOKIE_PREFIX));
                $cookies[$cleanName] = $value;
            }
        }

        return $cookies;
    }

    /**
     * Set remember-me cookie for user
     */
    public static function setRememberMe($userId, $username) {
        $token = bin2hex(random_bytes(32));
        
        self::set('user_id', $userId, 30, true, false);
        self::set('username', $username, 30, true, false);
        self::set('remember_token', $token, 30, true, false);

        return $token;
    }

    /**
     * Clear user cookies
     */
    public static function clearUser() {
        self::delete('user_id');
        self::delete('username');
        self::delete('remember_token');
    }
}
?>
