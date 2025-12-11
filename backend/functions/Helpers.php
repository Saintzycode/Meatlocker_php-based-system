<?php
/**
 * Helpers.php
 * Common helper functions used throughout the application
 * Reduces code duplication and centralizes utility logic
 */

// Load all handlers and managers
require_once __DIR__ . '/ProductManager.php';
require_once __DIR__ . '/FileHandler.php';
require_once __DIR__ . '/../handlers/FormHandler.php';
require_once __DIR__ . '/../handlers/CookieHandler.php';

/**
 * Get all products
 */
function getProducts() {
    $pm = new ProductManager();
    return $pm->loadProducts();
}

/**
 * Get product by ID
 */
function getProduct($id) {
    $pm = new ProductManager();
    return $pm->getProductById($id);
}

/**
 * Get sales data for dashboard
 */
function getSalesData() {
    $pm = new ProductManager();
    return $pm->getSalesData();
}

/**
 * Get total revenue
 */
function getTotalRevenue() {
    $pm = new ProductManager();
    $salesData = $pm->getSalesData();
    return $pm->getTotalRevenue($salesData);
}

/**
 * Get average daily sales
 */
function getAverageDailySales() {
    $pm = new ProductManager();
    $salesData = $pm->getSalesData();
    return $pm->getAverageDailySales($salesData);
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = '₱') {
    return $currency . number_format($amount, 2);
}

/**
 * Format stock display with color coding
 */
function getStockClass($stock) {
    if ($stock > 10) return 'in-stock';
    if ($stock > 0) return 'low-stock';
    return 'out-of-stock';
}

/**
 * Format stock text
 */
function getStockText($stock) {
    if ($stock > 10) return "Stock: $stock left";
    if ($stock > 0) return "Low Stock: $stock left";
    return "Out of Stock";
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user info
 */
function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Redirect to home if not admin
 */
function requireAdmin($redirect = 'index.php') {
    if (!isAdmin()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Display formatted message
 */
function displayMessage($message, $type = 'info') {
    if (empty($message)) return;
    
    $colors = [
        'success' => '#10B981',
        'error' => '#EF4444',
        'info' => '#3B82F6',
        'warning' => '#F59E0B'
    ];
    
    $bgColor = $colors[$type] ?? $colors['info'];
    
    echo <<<HTML
    <div style="
        background-color: {$bgColor};
        color: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: 600;
    ">
        $message
    </div>
    HTML;
}

/**
 * Sanitize and escape output
 */
function esc($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Get image path
 */
function getImagePath($filename) {
    return 'assets/images/' . esc($filename ?? 'default-product.jpg');
}

/**
 * Validate product data
 */
function validateProductData($data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Product name is required';
    }
    
    if (empty($data['price']) || floatval($data['price']) <= 0) {
        $errors[] = 'Valid price is required';
    }
    
    if (empty($data['stock']) || intval($data['stock']) < 0) {
        $errors[] = 'Valid stock quantity is required';
    }
    
    if (empty($data['category'])) {
        $errors[] = 'Category is required';
    }
    
    if (empty($data['description'])) {
        $errors[] = 'Description is required';
    }
    
    return $errors;
}

/**
 * Get date range for analytics
 */
function getDateRange($days = 30) {
    $dates = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $dates[] = date('Y-m-d', strtotime("-$i days"));
    }
    return $dates;
}

/**
 * Convert timestamp to readable format
 */
function formatDate($timestamp, $format = 'M d, Y') {
    return date($format, strtotime($timestamp));
}

/**
 * Get cart from localStorage via JavaScript (cart is client-side)
 */
function getCartTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $qty = isset($item['quantity']) ? intval($item['quantity']) : 1;
        $price = floatval($item['price'] ?? 0);
        $total += ($price * $qty);
    }
    return $total;
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header('Location: ' . $url);
    exit;
}

/**
 * Display flash message if exists
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        displayMessage($message, $type);
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Log action for audit trail
 */
function logAction($action, $details = '') {
    $logFile = __DIR__ . '/../audit.log';
    $timestamp = date('Y-m-d H:i:s');
    $user = $_SESSION['username'] ?? 'Guest';
    $logEntry = "[$timestamp] User: $user | Action: $action | Details: $details\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>
