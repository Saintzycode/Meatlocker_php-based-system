<?php
/**
 * Configuration File
 * Central location for all application settings
 */

// Database / File Storage
define('DATA_DIR', __DIR__ . '/backend/data/');
define('UPLOAD_DIR', __DIR__ . '/assets/images/');

// Security Settings
define('ENABLE_HTTPS_REDIRECT', false); // Set to true in production
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SECURE', false); // Set to true in production with HTTPS

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Cookie Settings
define('COOKIE_SECURE', false); // Set to true in production with HTTPS
define('COOKIE_HTTPONLY', true);
define('COOKIE_SAMESITE', 'Lax');

// Application Settings
define('APP_NAME', 'MeatLocker');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', false); // Set to true for development

// Encryption (for sensitive data)
$GLOBALS['encryption_key'] = getenv('ENCRYPTION_KEY') ?: 'change-me-in-production';

// Session Configuration
ini_set('session.cookie_httponly', SESSION_COOKIE_HTTPONLY ? 1 : 0);
ini_set('session.cookie_secure', SESSION_COOKIE_SECURE ? 1 : 0);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// HTTPS Redirect (production only)
if (ENABLE_HTTPS_REDIRECT && empty($_SERVER['HTTPS'])) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
?>
