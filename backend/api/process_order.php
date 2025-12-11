<?php
/**
 * Process Order API
 * Handles order submission and stock reduction
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../handlers/OrderHandler.php';

session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get cart data from POST
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cart']) || !is_array($data['cart'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
    exit;
}

// Debug: Log what we received
$debugLog = "=== ORDER PROCESSING " . date('Y-m-d H:i:s') . " ===\n";
$debugLog .= "Cart received: " . json_encode($data['cart']) . "\n";
file_put_contents(__DIR__ . '/../../debug.log', $debugLog, FILE_APPEND);
error_log('Cart received: ' . json_encode($data['cart']));

// Process the order
$orderHandler = new OrderHandler();
$result = $orderHandler->processOrder($data['cart']);

$debugLog = "Result: " . json_encode($result) . "\n\n";
file_put_contents(__DIR__ . '/../../debug.log', $debugLog, FILE_APPEND);

echo json_encode($result);
?>
